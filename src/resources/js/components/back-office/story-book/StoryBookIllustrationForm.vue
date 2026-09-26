<script setup>
import InfiniteScrollApiSelect from "@/components/common/multi-select/InfiniteScrollApiSelect.vue";
import { AiBrainOutputTypes } from "@/composables/useAiBrain";
import {
    canStartIllustrationGeneration,
    isStoryBookComplete,
    statuses,
} from "@/composables/useStoryBook";

import { computed, onBeforeUnmount, ref, watch } from "vue";
import { router as inertiaRoute, useForm } from "@inertiajs/vue3";

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library as FontAwesomeLibrary } from "@fortawesome/fontawesome-svg-core";
import {
    faCircleCheck,
    faCircleExclamation,
    faImage,
    faSpinner,
    faStop,
    faWandMagicSparkles,
} from "@fortawesome/free-solid-svg-icons";

FontAwesomeLibrary.add(
    faCircleCheck,
    faCircleExclamation,
    faImage,
    faSpinner,
    faStop,
    faWandMagicSparkles,
);

const props = defineProps({
    storyBook: {
        type: Object,
        required: true,
    },
    progress: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(["updated"]);

const liveProgress = ref(null);

const illustrationForm = useForm({
    ai_brain_illustration_id: props.storyBook?.ai_brain_illustration_id ?? null,
});

let pollTimer = null;

let pollToken = 0;

const currentProgress = computed(() => liveProgress.value ?? props.progress);

const status = computed(
    () => currentProgress.value?.status ?? props.storyBook?.status,
);

const illustration = computed(() => currentProgress.value?.illustration ?? {});

const pages = computed(() => props.storyBook?.story_book_pages ?? []);

const isRunning = computed(
    () => status.value === statuses.ProcessingIllustration,
);

const isStopped = computed(
    () => status.value === statuses.StopIllustration,
);

const isComplete = computed(() => isStoryBookComplete(status.value));

const canStart = computed(
    () => canStartIllustrationGeneration(status.value) && !isRunning.value,
);

const errorMessage = computed(() => currentProgress.value?.error_message);

const illustrationAiBrainApiUrl = computed(() =>
    route("search.ai-brains", {
        ai_brain_output_type_code: AiBrainOutputTypes.Image,
    }),
);

const illustrationImageUrl = (page) =>
    page?.illustration_image?.preview_url ??
    page?.illustration_image?.original_url ??
    null;

const pageState = (page) => {
    if (illustrationImageUrl(page)) {
        return "generated";
    }

    if (isRunning.value && page.no === illustration.value?.current_page) {
        return "generating";
    }

    if (isRunning.value && (page.no ?? 0) < (illustration.value?.current_page ?? 0)) {
        return "failed";
    }

    return "waiting";
};

const stopPolling = () => {
    pollToken += 1;

    if (pollTimer) {
        clearInterval(pollTimer);
        pollTimer = null;
    }
};

const poll = async () => {
    const token = pollToken;

    try {
        const response = await fetch(
            route("back-office.story-books.generation-status", {
                slug: props.storyBook?.slug,
            }),
            {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            },
        );

        if (token !== pollToken || !response.ok) {
            return;
        }

        const payload = await response.json();

        if (token !== pollToken) {
            return;
        }

        liveProgress.value = payload.progress;

        emit("updated", payload);

        if (payload.progress?.status !== statuses.ProcessingIllustration) {
            stopPolling();
        }
    } catch (error) {
        if (token === pollToken) {
            stopPolling();
        }
    }
};

const startPolling = () => {
    stopPolling();

    pollTimer = setInterval(poll, 3000);
};

const applyProgress = (progress, storyBookStatus = null) => {
    const nextStatus = storyBookStatus ?? progress?.status ?? null;

    liveProgress.value = progress ?? null;

    emit("updated", {
        progress: liveProgress.value,
        storyBook: {
            ...(props.storyBook ?? {}),
            status: nextStatus ?? props.storyBook?.status,
        },
    });
};

const applyOptimisticStatus = (nextStatus) => {
    stopPolling();

    applyProgress(
        {
            ...(currentProgress.value ?? {}),
            status: nextStatus,
            stopped_at:
                nextStatus === statuses.StopIllustration
                    ? new Date().toISOString()
                    : null,
        },
        nextStatus,
    );
};

watch(
    () => props.progress,
    (newProgress) => {
        liveProgress.value = newProgress ?? null;
    },
);

watch(
    () => props.storyBook?.status,
    (newStatus) => {
        if (newStatus === statuses.ProcessingIllustration) {
            startPolling();

            return;
        }

        stopPolling();
    },
    { immediate: true },
);

onBeforeUnmount(stopPolling);

const startGeneration = () => {
    if (illustrationForm.processing || !illustrationForm.ai_brain_illustration_id) {
        return;
    }

    illustrationForm.clearErrors();

    applyOptimisticStatus(statuses.ProcessingIllustration);

    illustrationForm.patch(
        route("back-office.story-books.illustration-generation.start", {
            slug: props.storyBook?.slug,
        }),
        { ...illustrationForm.data(), _method: "patch" },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                startPolling();
            },
            onError: () => {
                liveProgress.value = props.progress ?? null;
            },
        },
    );
};

const stopGeneration = () => {
    if (illustrationForm.processing) {
        return;
    }

    applyOptimisticStatus(statuses.StopIllustration);

    illustrationForm.patch(
        route("back-office.story-books.illustration-generation.stop", {
            slug: props.storyBook?.slug,
        }),
        { _method: "patch" },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                stopPolling();
            },
            onError: () => {
                liveProgress.value = props.progress ?? null;
            },
        },
    );
};
</script>

<template>
    <div class="space-y-6">
        <div class="bg-white border rounded-xl p-5 shadow-sm space-y-4">
            <div class="flex items-center gap-2">
                <FontAwesomeIcon
                    icon="wand-magic-sparkles"
                    class="text-purple-600"
                />
                <h3 class="text-base font-semibold">
                    Illustration Generation
                </h3>
            </div>

            <p
                v-if="!canStart && !isRunning"
                class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-700"
            >
                Text generation must be completed before illustration
                generation can start.
            </p>

            <p
                v-else-if="isComplete"
                class="rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700"
            >
                This story book is complete and can no longer be updated.
            </p>

            <div
                class="border-2 border-dashed border-purple-200 rounded-xl p-4 bg-gradient-to-br from-purple-50 to-blue-50"
            >
                <InfiniteScrollApiSelect
                    :form="illustrationForm"
                    fieldName="ai_brain_illustration_id"
                    :selectedItem="storyBook?.ai_brain_illustration"
                    :apiUrl="illustrationAiBrainApiUrl"
                    :multiple="false"
                    placeholder="Select Image AI Brain"
                    :error="illustrationForm.errors.ai_brain_illustration_id"
                    class="ai-brain-select"
                />
            </div>

            <p
                v-if="illustrationForm.errors.ai_brain_illustration_id"
                class="text-red-500 text-sm"
            >
                {{ illustrationForm.errors.ai_brain_illustration_id }}
            </p>

            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span
                        class="rounded-full bg-purple-50 px-2 py-0.5 text-xs font-medium text-purple-700"
                    >
                        {{ illustration.completed_pages ?? 0 }} /
                        {{ illustration.total_pages ?? 0 }} pages generated
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        v-if="isRunning"
                        type="button"
                        @click="stopGeneration"
                        :disabled="illustrationForm.processing"
                        class="flex items-center gap-2 rounded-md border border-red-300 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 transition hover:bg-red-100 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <FontAwesomeIcon icon="stop" />
                        Stop
                    </button>

                    <button
                        v-else
                        type="button"
                        @click="startGeneration"
                        :disabled="
                            illustrationForm.processing ||
                            !illustrationForm.ai_brain_illustration_id ||
                            !canStart
                        "
                        class="flex items-center gap-2 rounded-md bg-purple-600 px-4 py-2 text-sm text-white transition hover:bg-purple-700 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <FontAwesomeIcon
                            v-if="illustrationForm.processing"
                            icon="spinner"
                            spin
                        />
                        <FontAwesomeIcon v-else icon="wand-magic-sparkles" />

                        {{
                            isStopped
                                ? "Resume Illustrations"
                                : "Generate Illustrations"
                        }}
                    </button>
                </div>
            </div>

            <div
                v-if="isRunning || isStopped || isComplete"
                class="w-full"
            >
                <div
                    class="h-2 w-full overflow-hidden rounded-full bg-gray-200"
                >
                    <div
                        class="h-full rounded-full bg-purple-600 transition-all duration-500"
                        :style="{ width: `${illustration.percentage ?? 0}%` }"
                    ></div>
                </div>
            </div>

            <p
                v-if="isRunning && illustration.current_page"
                class="flex items-center gap-2 rounded-lg border border-blue-100 bg-blue-50 p-3 text-sm text-blue-800"
            >
                <FontAwesomeIcon icon="spinner" spin />
                Generating page {{ illustration.current_page }} of
                {{ illustration.total_pages }}
            </p>

            <p
                v-if="errorMessage"
                class="flex items-start gap-2 rounded-lg border border-red-100 bg-red-50 p-3 text-sm text-red-700"
            >
                <FontAwesomeIcon
                    icon="circle-exclamation"
                    class="mt-0.5 flex-shrink-0"
                />
                <span>
                    <span class="font-medium">
                        Page {{ illustration.current_page }} failed:
                    </span>
                    {{ errorMessage }}
                </span>
            </p>

            <p
                v-else-if="isStopped"
                class="rounded-lg border border-amber-100 bg-amber-50 p-3 text-sm text-amber-700"
            >
                Illustration generation stopped at page
                {{ illustration.current_page }}. Resume to continue from the
                next page.
            </p>
        </div>

        <div
            v-if="pages.length"
            class="bg-white border rounded-xl p-5 shadow-sm space-y-4"
        >
            <div class="flex items-center gap-2">
                <FontAwesomeIcon icon="image" class="text-blue-600" />
                <h3 class="text-base font-semibold">Page Illustrations</h3>
            </div>

            <ul class="space-y-3">
                <li
                    v-for="page in pages"
                    :key="page.no"
                    class="rounded-xl border border-gray-100 bg-gray-50 p-4"
                >
                    <div class="flex flex-col gap-4 md:flex-row">
                        <div
                            v-if="illustrationImageUrl(page)"
                            class="flex-shrink-0"
                        >
                            <img
                                :src="illustrationImageUrl(page)"
                                :alt="`Story Book Page ${page.no}`"
                                class="h-28 w-28 rounded-lg border border-gray-200 object-cover"
                            />
                        </div>

                        <div
                            v-else
                            class="flex h-28 w-28 flex-shrink-0 items-center justify-center rounded-lg border border-dashed border-gray-300 bg-white text-gray-300"
                        >
                            <FontAwesomeIcon icon="image" class="text-2xl" />
                        </div>

                        <div class="min-w-0 flex-1 space-y-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <span
                                    class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700"
                                >
                                    {{ page.no }}
                                </span>

                                <span
                                    class="flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium"
                                    :class="{
                                        'bg-green-100 text-green-700':
                                            pageState(page) === 'generated',
                                        'bg-blue-100 text-blue-700':
                                            pageState(page) === 'generating',
                                        'bg-red-100 text-red-700':
                                            pageState(page) === 'failed',
                                        'bg-gray-100 text-gray-600':
                                            pageState(page) === 'waiting',
                                    }"
                                >
                                    <FontAwesomeIcon
                                        v-if="pageState(page) === 'generated'"
                                        icon="circle-check"
                                        class="text-xs"
                                    />
                                    <FontAwesomeIcon
                                        v-else-if="
                                            pageState(page) === 'generating'
                                        "
                                        icon="spinner"
                                        spin
                                        class="text-xs"
                                    />
                                    <FontAwesomeIcon
                                        v-else-if="pageState(page) === 'failed'"
                                        icon="circle-exclamation"
                                        class="text-xs"
                                    />

                                    {{
                                        pageState(page) === "generated"
                                            ? "Generated"
                                            : pageState(page) === "generating"
                                              ? "Generating"
                                              : pageState(page) === "failed"
                                                ? "Failed"
                                                : "Waiting"
                                    }}
                                </span>
                            </div>

                            <p class="text-sm text-gray-700">
                                {{ page.narration || "Narration pending." }}
                            </p>
                        </div>
                    </div>
                </li>
            </ul>
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
