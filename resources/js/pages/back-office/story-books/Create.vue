<script setup>
import Layout from "@/pages/layouts/AuthLayout.vue";
import InfiniteScrollApiSelect from "@/components/common/multi-select/InfiniteScrollApiSelect.vue";

import { ref, computed, onMounted, nextTick, watch } from "vue";
import { Head, useForm } from "@inertiajs/vue3";

import {
    AiBrainOutputTypes,
    buildAiBrainSearchUrl,
} from "@/composables/useAiBrain";

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library as FontAwesomeLibrary } from "@fortawesome/fontawesome-svg-core";
import {
    faSave,
    faSpinner,
    faCheck,
    faLock,
    faArrowRight,
    faArrowLeft,
    faWandMagicSparkles,
    faBrain,
    faUser,
    faGlobe,
    faBook,
    faCog,
} from "@fortawesome/free-solid-svg-icons";

FontAwesomeLibrary.add(
    faSave,
    faSpinner,
    faCheck,
    faLock,
    faArrowRight,
    faArrowLeft,
    faWandMagicSparkles,
    faBrain,
    faUser,
    faGlobe,
    faBook,
    faCog,
);

defineOptions({ layout: Layout });

const createPageTitle = "Create Story Book";

const { storyBook = null } = defineProps({
    storyBook: Object,
});

const isUpdate = computed(() => !!storyBook?.title);

const pageTitle = computed(() => {
    return isUpdate.value ? `Edit ${storyBook?.title}` : "New Story Book";
});

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

const STEP_DEFINITIONS = [
    {
        id: 1,
        name: "Plot Generator",
        icon: "cog",
        description: "Basic generation settings",
        aiBrainOutputTypeCode: AiBrainOutputTypes.Text,
    },
    {
        id: 2,
        name: "World Building",
        icon: "globe",
        description: "World and setting details",
        aiBrainOutputTypeCode: AiBrainOutputTypes.Text,
    },
    {
        id: 3,
        name: "Characters",
        icon: "user",
        description: "Character development",
        aiBrainOutputTypeCode: null,
    },
    {
        id: 4,
        name: "Plot & Outline",
        icon: "book",
        description: "Story structure",
        aiBrainOutputTypeCode: null,
    },
    {
        id: 5,
        name: "Review & Generate",
        icon: "wand-magic-sparkles",
        description: "Final review",
        aiBrainOutputTypeCode: null,
    },
];

const getStepDefinition = (stepId) => {
    return STEP_DEFINITIONS.find((item) => item.id === stepId);
};

const activeStep = ref(1);
const completedSteps = ref(new Set());

const plotGeneratorSaveForm = useForm({
    additional_information: storyBook?.additional_information ?? null,
    language_id: storyBook?.language_id ?? null,
    genre_ids: [],
    story_book_type_id: storyBook?.story_book_type_id ?? null,
    audience_id: storyBook?.audience_id ?? null,
    ai_brain_id: storyBook?.ai_brain_id ?? null,
});

const genresApiUrl = computed(() => {
    const audienceId = plotGeneratorSaveForm.audience_id;

    if (!audienceId) {
        return route("search.genres");
    }

    return `${route("search.genres")}?audience_id=${encodeURIComponent(
        audienceId,
    )}`;
});

const isInitialAudienceLoad = ref(true);

watch(
    () => plotGeneratorSaveForm.audience_id,
    (newAudienceId, oldAudienceId) => {
        if (isInitialAudienceLoad.value) {
            isInitialAudienceLoad.value = false;
            return;
        }

        if (newAudienceId === oldAudienceId) {
            return;
        }

        plotGeneratorSaveForm.language_id = null;
        plotGeneratorSaveForm.genre_ids = [];
        plotGeneratorSaveForm.story_book_type_id = null;
        plotGeneratorSaveForm.additional_information = null;

        plotGeneratorSaveForm.clearErrors(
            "language_id",
            "genre_ids",
            "story_book_type_id",
            "additional_information",
        );
    },
);

const worldBuildingSaveForm = useForm({
    ai_brain_id: null,
});

const charactersSaveForm = useForm({
    ai_brain_id: null,
});

const plotOutlineSaveForm = useForm({
    ai_brain_id: null,
});

const reviewGenerateSaveForm = useForm({
    ai_brain_id: null,
});

const STEP_FORMS = {
    1: plotGeneratorSaveForm,
    2: worldBuildingSaveForm,
    3: charactersSaveForm,
    4: plotOutlineSaveForm,
    5: reviewGenerateSaveForm,
};

const isStepAccessible = (stepId) => {
    if (stepId === 1) {
        return true;
    }

    return completedSteps.value.has(stepId - 1);
};

const isStepCompleted = (stepId) => {
    return completedSteps.value.has(stepId);
};

const getStepState = (stepId) => {
    if (isStepCompleted(stepId)) {
        return "completed";
    }

    if (stepId === activeStep.value) {
        return "active";
    }

    if (isStepAccessible(stepId)) {
        return "accessible";
    }

    return "locked";
};

const validateStep = (stepId) => {
    const step = getStepDefinition(stepId);
    const stepForm = STEP_FORMS[stepId];

    stepForm.clearErrors();

    let valid = true;

    if (step?.aiBrainOutputTypeCode && !stepForm.ai_brain_id) {
        stepForm.setError("ai_brain_id", "AI Brain selection is required");
        valid = false;
    }

    if (stepId === 1) {
        if (!stepForm.language_id) {
            stepForm.setError("language_id", "Language is required");
            valid = false;
        }

        if (
            !Array.isArray(stepForm.genre_ids) ||
            stepForm.genre_ids.length === 0
        ) {
            stepForm.setError("genre_ids", "Genres is required");
            valid = false;
        }

        if (!stepForm.story_book_type_id) {
            stepForm.setError(
                "story_book_type_id",
                "Story Book type is required",
            );
            valid = false;
        }

        if (!stepForm.audience_id) {
            stepForm.setError("audience_id", "Audience is required");
            valid = false;
        }
    }

    return valid;
};

function submitStep1() {
    if (plotGeneratorSaveForm.processing) {
        return;
    }

    if (!validateStep(1)) {
        return;
    }

    plotGeneratorSaveForm.post(route("back-office.story-books.save.plot"), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            completedSteps.value.add(1);
            activeStep.value = 2;
            plotGeneratorSaveForm.clearErrors();
        },
        onError: (errors) => {
            plotGeneratorSaveForm.clearErrors();
            plotGeneratorSaveForm.setError(errors);
        },
    });
}

const submitStep2 = () => {
    if (worldBuildingSaveForm.processing) {
        return;
    }

    if (!validateStep(2)) {
        return;
    }

    completedSteps.value.add(2);
    activeStep.value = 3;
    worldBuildingSaveForm.clearErrors();
};

const goToStep = (stepId) => {
    if (isStepAccessible(stepId) || isStepCompleted(stepId)) {
        activeStep.value = stepId;
    }
};

const goNext = () => {
    if (activeStep.value < 5 && isStepAccessible(activeStep.value + 1)) {
        activeStep.value++;
    }
};

const goPrev = () => {
    if (activeStep.value > 1) {
        activeStep.value--;
    }
};
</script>

<template>
    <Head :title="createPageTitle" />

    <div class="w-full">
        <div
            class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 md:p-6"
        >
            <div class="flex flex-col">
                <div
                    class="flex items-center px-0 py-4 border-b border-gray-200"
                >
                    <h2 class="text-lg font-semibold flex items-center gap-2">
                        <FontAwesomeIcon
                            icon="wand-magic-sparkles"
                            class="text-purple-600"
                        />
                        {{ pageTitle }}
                    </h2>
                </div>

                <div class="px-0 pt-4 border-b border-gray-200">
                    <nav class="hidden md:flex overflow-x-auto pb-px">
                        <button
                            v-for="step in STEP_DEFINITIONS"
                            :key="step.id"
                            type="button"
                            @click="goToStep(step.id)"
                            :disabled="
                                !isStepAccessible(step.id) &&
                                !isStepCompleted(step.id)
                            "
                            class="flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition disabled:opacity-40 disabled:cursor-not-allowed"
                            :class="{
                                'border-blue-600 text-blue-600':
                                    getStepState(step.id) === 'active',
                                'border-green-500 text-green-600':
                                    getStepState(step.id) === 'completed',
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300':
                                    getStepState(step.id) === 'accessible',
                                'border-transparent text-gray-300':
                                    getStepState(step.id) === 'locked',
                            }"
                        >
                            <span
                                class="flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold"
                                :class="{
                                    'bg-blue-600 text-white':
                                        getStepState(step.id) === 'active',
                                    'bg-green-500 text-white':
                                        getStepState(step.id) === 'completed',
                                    'bg-gray-200 text-gray-600':
                                        getStepState(step.id) === 'accessible',
                                    'bg-gray-100 text-gray-400':
                                        getStepState(step.id) === 'locked',
                                }"
                            >
                                <FontAwesomeIcon
                                    v-if="isStepCompleted(step.id)"
                                    icon="check"
                                    class="text-xs"
                                />
                                <span v-else>{{ step.id }}</span>
                            </span>

                            <span>{{ step.name }}</span>

                            <FontAwesomeIcon
                                v-if="
                                    !isStepAccessible(step.id) &&
                                    !isStepCompleted(step.id)
                                "
                                icon="lock"
                                class="text-xs text-gray-300"
                            />
                        </button>
                    </nav>

                    <nav class="md:hidden -mx-2 px-2">
                        <button
                            v-for="step in STEP_DEFINITIONS"
                            :key="step.id"
                            type="button"
                            @click="goToStep(step.id)"
                            :disabled="
                                !isStepAccessible(step.id) &&
                                !isStepCompleted(step.id)
                            "
                            class="w-full flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition disabled:opacity-40 disabled:cursor-not-allowed text-left"
                            :class="{
                                'bg-blue-50 text-blue-700':
                                    getStepState(step.id) === 'active' ||
                                    getStepState(step.id) === 'completed',
                                'text-gray-600 hover:bg-gray-50':
                                    getStepState(step.id) === 'accessible',
                                'text-gray-300':
                                    getStepState(step.id) === 'locked',
                            }"
                        >
                            <span
                                class="flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold flex-shrink-0"
                                :class="{
                                    'bg-blue-600 text-white':
                                        getStepState(step.id) === 'active',
                                    'bg-green-500 text-white':
                                        getStepState(step.id) === 'completed',
                                    'bg-gray-200 text-gray-600':
                                        getStepState(step.id) === 'accessible',
                                    'bg-gray-100 text-gray-400':
                                        getStepState(step.id) === 'locked',
                                }"
                            >
                                <FontAwesomeIcon
                                    v-if="isStepCompleted(step.id)"
                                    icon="check"
                                    class="text-xs"
                                />
                                <span v-else>{{ step.id }}</span>
                            </span>

                            <span class="flex-1">{{ step.name }}</span>

                            <FontAwesomeIcon
                                v-if="
                                    !isStepAccessible(step.id) &&
                                    !isStepCompleted(step.id)
                                "
                                icon="lock"
                                class="text-xs text-gray-300 flex-shrink-0"
                            />
                        </button>
                    </nav>
                </div>

                <div class="px-0 py-6">
                    <div v-if="activeStep === 1" class="space-y-6">
                        <div
                            class="bg-white border rounded-xl p-5 shadow-sm space-y-4"
                        >
                            <h3
                                class="text-base font-semibold flex items-center gap-2"
                            >
                                <FontAwesomeIcon
                                    icon="cog"
                                    class="text-blue-600"
                                />
                                Generation Configuration
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium mb-1"
                                    >
                                        Audience
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <InfiniteScrollApiSelect
                                        :form="plotGeneratorSaveForm"
                                        fieldName="audience_id"
                                        :selectedItem="storyBook?.audience"
                                        :apiUrl="route('search.audiences')"
                                        :multiple="false"
                                        placeholder="Select audiences"
                                    />

                                    <p
                                        v-if="
                                            plotGeneratorSaveForm.errors
                                                .audience_id
                                        "
                                        class="text-red-500 text-sm mt-1"
                                    >
                                        {{
                                            plotGeneratorSaveForm.errors
                                                .audience_id
                                        }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium mb-1"
                                    >
                                        Language
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <InfiniteScrollApiSelect
                                        :form="plotGeneratorSaveForm"
                                        fieldName="language_id"
                                        :selectedItem="storyBook?.language"
                                        :apiUrl="route('search.languages')"
                                        :multiple="false"
                                        placeholder="Select languages"
                                    />

                                    <p
                                        v-if="
                                            plotGeneratorSaveForm.errors
                                                .language_id
                                        "
                                        class="text-red-500 text-sm mt-1"
                                    >
                                        {{
                                            plotGeneratorSaveForm.errors
                                                .language_id
                                        }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium mb-1"
                                    >
                                        Genres
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <InfiniteScrollApiSelect
                                        :form="plotGeneratorSaveForm"
                                        fieldName="genre_ids"
                                        :selectedItem="storyBook?.genres"
                                        :apiUrl="genresApiUrl"
                                        :multiple="true"
                                        placeholder="Select genres"
                                    />

                                    <p
                                        v-if="
                                            plotGeneratorSaveForm.errors
                                                .genre_ids
                                        "
                                        class="text-red-500 text-sm mt-1"
                                    >
                                        {{
                                            plotGeneratorSaveForm.errors
                                                .genre_ids
                                        }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium mb-1"
                                    >
                                        Story Book Type
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <InfiniteScrollApiSelect
                                        :form="plotGeneratorSaveForm"
                                        fieldName="story_book_type_id"
                                        :selectedItem="
                                            storyBook?.story_book_type
                                        "
                                        :apiUrl="
                                            route('search.story-book-types')
                                        "
                                        :multiple="false"
                                        placeholder="Select story book types"
                                    />
                                </div>

                                <div class="md:col-span-2">
                                    <label
                                        class="block text-sm font-medium mb-1"
                                    >
                                        Additional Information
                                    </label>

                                    <textarea
                                        v-model="
                                            plotGeneratorSaveForm.additional_information
                                        "
                                        rows="3"
                                        placeholder="Any additional context or instructions for the AI..."
                                        class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none border-gray-300"
                                    ></textarea>
                                </div>
                            </div>

                        </div>

                        <div
                            class="bg-white border rounded-xl p-5 shadow-sm space-y-4"
                        >
                            <h3
                                class="text-base font-semibold flex items-center gap-2"
                            >
                                <FontAwesomeIcon
                                    icon="brain"
                                    class="text-purple-600"
                                />
                                AI Brain Configuration
                            </h3>

                            <p class="text-sm text-gray-500">
                                Select the AI model that will be used for
                                generating your story book's plot.
                            </p>

                            <div
                                class="border-2 border-dashed border-purple-200 rounded-xl p-4 bg-gradient-to-br from-purple-50 to-blue-50"
                            >
                                <InfiniteScrollApiSelect
                                    :form="plotGeneratorSaveForm"
                                    fieldName="ai_brain_id"
                                    :selectedItem="
                                        plotGeneratorSaveForm.ai_brain_id
                                    "
                                    :apiUrl="
                                        buildAiBrainSearchUrl(
                                            getStepDefinition(1)
                                                .aiBrainOutputTypeCode,
                                        )
                                    "
                                    :multiple="false"
                                    placeholder="Select AI Brain"
                                    :error="
                                        plotGeneratorSaveForm.errors.ai_brain_id
                                    "
                                    class="ai-brain-select"
                                />
                            </div>

                            <p
                                v-if="plotGeneratorSaveForm.errors.ai_brain_id"
                                class="text-red-500 text-sm"
                            >
                                {{ plotGeneratorSaveForm.errors.ai_brain_id }}
                            </p>
                        </div>
                    </div>

                    <div v-if="activeStep === 2" class="space-y-6">
                        <div
                            class="bg-white border rounded-xl p-5 shadow-sm space-y-4"
                        >
                            <h3
                                class="text-base font-semibold flex items-center gap-2"
                            >
                                <FontAwesomeIcon
                                    icon="globe"
                                    class="text-blue-600"
                                />
                                World Building
                            </h3>

                            <p class="text-sm text-gray-500">
                                Define the world, setting, and rules that will
                                shape your story.
                            </p>

                            <div
                                class="border-2 border-dashed border-purple-200 rounded-xl p-4 bg-gradient-to-br from-purple-50 to-blue-50"
                            >
                                <InfiniteScrollApiSelect
                                    :form="worldBuildingSaveForm"
                                    fieldName="ai_brain_id"
                                    :selectedItem="
                                        worldBuildingSaveForm.ai_brain_id
                                    "
                                    :apiUrl="
                                        buildAiBrainSearchUrl(
                                            getStepDefinition(2)
                                                .aiBrainOutputTypeCode,
                                        )
                                    "
                                    :multiple="false"
                                    placeholder="Select AI Brain"
                                    :error="
                                        worldBuildingSaveForm.errors.ai_brain_id
                                    "
                                    class="ai-brain-select"
                                />
                            </div>

                            <p
                                v-if="worldBuildingSaveForm.errors.ai_brain_id"
                                class="text-red-500 text-sm"
                            >
                                {{ worldBuildingSaveForm.errors.ai_brain_id }}
                            </p>
                        </div>
                    </div>

                    <div
                        v-if="activeStep === 3"
                        class="flex items-center justify-center h-64"
                    >
                        <div class="text-center space-y-3">
                            <FontAwesomeIcon
                                icon="user"
                                class="text-4xl text-gray-300"
                            />
                            <p class="text-gray-400 text-sm">
                                Characters - Coming Soon
                            </p>
                        </div>
                    </div>

                    <div
                        v-if="activeStep === 4"
                        class="flex items-center justify-center h-64"
                    >
                        <div class="text-center space-y-3">
                            <FontAwesomeIcon
                                icon="book"
                                class="text-4xl text-gray-300"
                            />
                            <p class="text-gray-400 text-sm">
                                Plot & Outline - Coming Soon
                            </p>
                        </div>
                    </div>

                    <div
                        v-if="activeStep === 5"
                        class="flex items-center justify-center h-64"
                    >
                        <div class="text-center space-y-3">
                            <FontAwesomeIcon
                                icon="wand-magic-sparkles"
                                class="text-4xl text-gray-300"
                            />
                            <p class="text-gray-400 text-sm">
                                Review & Generate - Coming Soon
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="px-0 py-4 border-t border-gray-200 flex justify-between items-center"
                >
                    <button
                        type="button"
                        @click="goPrev"
                        :disabled="activeStep === 1"
                        class="px-4 py-2 text-sm rounded-md border border-gray-300 hover:bg-gray-50 transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <FontAwesomeIcon icon="arrow-left" />
                        Previous
                    </button>

                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-400">
                            Step {{ activeStep }} of
                            {{ STEP_DEFINITIONS.length }}
                        </span>
                    </div>

                    <div>
                        <button
                            v-if="activeStep === 1"
                            type="button"
                            @click="submitStep1"
                            :disabled="plotGeneratorSaveForm.processing"
                            class="px-5 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-md flex items-center gap-2 transition disabled:opacity-60 disabled:cursor-not-allowed"
                        >
                            <FontAwesomeIcon
                                v-if="plotGeneratorSaveForm.processing"
                                icon="spinner"
                                spin
                            />
                            <FontAwesomeIcon v-else icon="save" />
                            {{
                                plotGeneratorSaveForm.processing
                                    ? "Saving..."
                                    : "Save & Continue"
                            }}
                        </button>

                        <button
                            v-else-if="activeStep === 2"
                            type="button"
                            @click="submitStep2"
                            :disabled="worldBuildingSaveForm.processing"
                            class="px-5 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-md flex items-center gap-2 transition disabled:opacity-60 disabled:cursor-not-allowed"
                        >
                            <FontAwesomeIcon icon="check" />
                            Continue
                        </button>

                        <button
                            v-else
                            type="button"
                            @click="goNext"
                            :disabled="
                                !isStepAccessible(activeStep + 1) ||
                                activeStep === 5
                            "
                            class="px-5 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-md flex items-center gap-2 transition disabled:opacity-40 disabled:cursor-not-allowed"
                        >
                            Next
                            <FontAwesomeIcon icon="arrow-right" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.ai-brain-select :deep(.multiselect) {
    min-height: 44px;
}

.ai-brain-select :deep(.multiselect__tags) {
    min-height: 44px;
    padding: 8px 40px 0 12px;
    border-color: #a78bfa;
    border-width: 1px;
    border-radius: 0.5rem;
    background: white;
}

.ai-brain-select :deep(.multiselect__single) {
    padding: 4px 0 0 0;
    margin-bottom: 0;
    color: #1f2937;
}

.ai-brain-select :deep(.multiselect__placeholder) {
    padding: 4px 0 0 0;
    color: #9ca3af;
}

.ai-brain-select :deep(.multiselect__select) {
    height: 44px;
}

.ai-brain-select :deep(.multiselect__content-wrapper) {
    border-color: #a78bfa;
}
</style>
