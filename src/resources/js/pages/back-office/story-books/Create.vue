<script setup>
import StoryBookContentReview from "@/components/back-office/story-book/StoryBookContentReview.vue";
import StoryBookGenerationProgress from "@/components/back-office/story-book/StoryBookGenerationProgress.vue";
import StoryBookIllustrationForm from "@/components/back-office/story-book/StoryBookIllustrationForm.vue";
import StoryBookSetupForm from "@/components/back-office/story-book/StoryBookSetupForm.vue";
import {
    canStartIllustrationGeneration,
    isGenerationRunning,
    isStoryBookComplete,
    isTextGenerationComplete,
    statusClasses,
    statusLabels,
} from "@/composables/useStoryBook";

import Layout from "@/pages/layouts/AuthLayout.vue";

import { computed, nextTick, onMounted, ref, watch } from "vue";
import { Head } from "@inertiajs/vue3";

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library as FontAwesomeLibrary } from "@fortawesome/fontawesome-svg-core";
import {
    faArrowLeft,
    faBookOpen,
    faImage,
    faLock,
    faWandMagicSparkles,
} from "@fortawesome/free-solid-svg-icons";

FontAwesomeLibrary.add(
    faArrowLeft,
    faBookOpen,
    faImage,
    faLock,
    faWandMagicSparkles,
);

defineOptions({ layout: Layout });

const createPageTitle = "Create Story Book";

const { storyBook, progress } = defineProps({
    storyBook: {
        type: Object,
        default: null,
    },
    progress: {
        type: Object,
        default: null,
    },
});

const isUpdate = computed(() => !!storyBook?.id);

const liveProgress = ref(null);

const currentProgress = computed(() => liveProgress.value ?? progress);

const status = computed(
    () => currentProgress.value?.status ?? storyBook?.status,
);

const pageTitle = computed(() => {
    return isUpdate.value ? `Edit ${storyBook?.title}` : "New Story Book";
});

const isRunning = computed(() => isGenerationRunning(status.value));

const isTextComplete = computed(() =>
    isTextGenerationComplete(status.value),
);

const isComplete = computed(() => isStoryBookComplete(status.value));

const canStartIllustration = computed(() =>
    canStartIllustrationGeneration(status.value),
);

const showSetup = computed(
    () => !isUpdate.value || (!isTextComplete.value && !isComplete.value),
);

const TABS = [
    { key: "setup", label: "Setup", icon: "wand-magic-sparkles" },
    { key: "progress", label: "Progress", icon: "book-open" },
    { key: "review", label: "Review", icon: "book-open" },
    { key: "illustration", label: "Illustration", icon: "image" },
];

const activeTab = ref(isUpdate.value ? "progress" : "setup");

onMounted(async () => {
    await nextTick();

    window.dispatchEvent(
        new CustomEvent("set-breadcrumb", {
            detail: [
                {
                    text: "Story Books",
                    href: route("back-office.story-books.index"),
                },
                { text: createPageTitle, active: true },
            ],
        }),
    );
});

const isTabVisible = (tab) => {
    if (!isUpdate.value) {
        return tab === "setup";
    }

    if (tab === "setup") {
        return showSetup.value;
    }

    if (tab === "review") {
        return isTextComplete.value || isComplete.value;
    }

    if (tab === "illustration") {
        return isTextComplete.value || isRunning.value || isComplete.value;
    }

    return true;
};

const visibleTabs = computed(() => TABS.filter((tab) => isTabVisible(tab)));

const handleProgressUpdated = (payload) => {
    liveProgress.value = payload?.progress ?? null;
};

watch(
    () => progress,
    (newProgress) => {
        liveProgress.value = newProgress ?? null;
    },
);

watch(
    () => storyBook?.status,
    (newStatus) => {
        if (!isGenerationRunning(newStatus)) {
            liveProgress.value = progress ?? null;
        }
    },
);
</script>

<template>
    <Head :title="createPageTitle" />

    <div class="w-full">
        <div
            class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 md:p-6"
        >
            <div class="flex flex-col">
                <div
                    class="flex flex-wrap items-center justify-between gap-3 px-0 py-4 border-b border-gray-200"
                >
                    <h2 class="text-lg font-semibold flex items-center gap-2">
                        <FontAwesomeIcon
                            icon="wand-magic-sparkles"
                            class="text-purple-600"
                        />
                        {{ pageTitle }}
                    </h2>

                    <div class="flex items-center gap-2">
                        <span
                            v-if="isUpdate"
                            class="flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium"
                            :class="statusClasses[status] ?? 'bg-gray-100 text-gray-700'"
                        >
                            <FontAwesomeIcon
                                v-if="isComplete"
                                icon="lock"
                                class="text-xs"
                            />
                            {{ statusLabels[status] ?? status }}
                        </span>

                        <a
                            :href="route('back-office.story-books.index')"
                            class="px-3 py-1.5 text-sm rounded-md border border-gray-300 hover:bg-gray-50 transition flex items-center gap-2"
                        >
                            <FontAwesomeIcon icon="arrow-left" />
                            Back to List
                        </a>
                    </div>
                </div>

                <div
                    v-if="isUpdate"
                    class="px-0 pt-4 border-b border-gray-200"
                >
                    <nav class="flex flex-wrap gap-2 pb-4">
                        <button
                            v-for="tab in visibleTabs"
                            :key="tab.key"
                            type="button"
                            @click="activeTab = tab.key"
                            class="flex items-center gap-2 rounded-lg border px-3 py-2 text-sm font-medium transition"
                            :class="
                                activeTab === tab.key
                                    ? 'border-blue-200 bg-blue-50 text-blue-700'
                                    : 'border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50'
                            "
                        >
                            <FontAwesomeIcon :icon="tab.icon" class="text-xs" />
                            {{ tab.label }}
                        </button>
                    </nav>
                </div>

                <div class="px-0 py-6 space-y-6">
                    <StoryBookSetupForm
                        v-if="activeTab === 'setup' && showSetup"
                        :story-book="storyBook"
                        :is-update="isUpdate"
                        :disabled="isRunning"
                    />

                    <StoryBookGenerationProgress
                        v-if="activeTab === 'progress' && isUpdate"
                        :story-book="storyBook"
                        :progress="currentProgress"
                        @updated="handleProgressUpdated"
                    />

                    <div
                        v-if="
                            activeTab === 'progress' &&
                            isUpdate &&
                            canStartIllustration
                        "
                        class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700 flex flex-wrap items-center justify-between gap-3"
                    >
                        <span class="flex items-center gap-2">
                            <FontAwesomeIcon icon="book-open" />
                            Story Book is created successfully, You can review
                            first then start Illustration page.
                        </span>

                        <button
                            type="button"
                            @click="activeTab = 'illustration'"
                            class="flex items-center gap-2 rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-green-700"
                        >
                            <FontAwesomeIcon icon="image" />
                            Go to Illustration
                        </button>
                    </div>

                    <StoryBookContentReview
                        v-if="activeTab === 'review' && isUpdate"
                        :story-book="storyBook"
                    />

                    <StoryBookIllustrationForm
                        v-if="activeTab === 'illustration' && isUpdate"
                        :story-book="storyBook"
                        :progress="currentProgress"
                        @updated="handleProgressUpdated"
                    />

                    <div
                        v-if="!isUpdate"
                        class="rounded-xl border border-blue-200 bg-blue-50 p-5 text-center"
                    >
                        <FontAwesomeIcon
                            icon="wand-magic-sparkles"
                            class="text-2xl text-blue-600"
                        />
                        <h3 class="mt-2 text-lg font-semibold text-blue-800">
                            One click generates steps 1 to 15
                        </h3>
                        <p class="mt-1 text-sm text-blue-700">
                            The story book is saved only after the foundation
                            call succeeds, then every remaining text step runs
                            automatically. You can stop and resume at any time.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
