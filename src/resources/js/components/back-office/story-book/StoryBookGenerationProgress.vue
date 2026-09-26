<script setup>
import { statuses } from "@/composables/useStoryBook";

import { computed, onBeforeUnmount, ref, watch } from "vue";
import { router as inertiaRoute } from "@inertiajs/vue3";

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library as FontAwesomeLibrary } from "@fortawesome/fontawesome-svg-core";
import {
    faCircleCheck,
    faCircleExclamation,
    faCirclePause,
    faClock,
    faLock,
    faRotateRight,
    faSpinner,
    faStop,
} from "@fortawesome/free-solid-svg-icons";

FontAwesomeLibrary.add(
    faCircleCheck,
    faCircleExclamation,
    faCirclePause,
    faClock,
    faLock,
    faRotateRight,
    faSpinner,
    faStop,
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

const actionForm = ref({ processing: false });

let pollTimer = null;

let pollToken = 0;

const currentProgress = computed(() => liveProgress.value ?? props.progress);

const status = computed(() => currentProgress.value?.status ?? props.storyBook?.status);

const steps = computed(() => currentProgress.value?.steps ?? []);

const isRunning = computed(() => status.value === statuses.ProcessingText);

const isStopped = computed(() => status.value === statuses.StopText);

const isComplete = computed(() => status.value === statuses.CompleteText);

const isLocked = computed(() => status.value === statuses.Complete);

const canResume = computed(() => isStopped.value);

const percentage = computed(() => currentProgress.value?.percentage ?? 0);

const completedCount = computed(
    () => currentProgress.value?.completed_steps_count ?? 0,
);

const totalCount = computed(() => currentProgress.value?.total_steps ?? 0);

const stepClasses = (step) => {
    if (step.state === "completed") {
        return "border-green-200 bg-green-50 text-green-700";
    }

    if (step.state === "processing") {
        return "border-blue-200 bg-blue-50 text-blue-700";
    }

    if (step.state === "failed") {
        return "border-red-200 bg-red-50 text-red-700";
    }

    if (step.state === "stopped") {
        return "border-amber-200 bg-amber-50 text-amber-700";
    }

    return "border-gray-200 text-gray-500";
};

const stepIconClasses = (step) => {
    if (step.state === "completed") {
        return "bg-green-500 text-white";
    }

    if (step.state === "processing") {
        return "bg-blue-600 text-white";
    }

    if (step.state === "failed") {
        return "bg-red-500 text-white";
    }

    if (step.state === "stopped") {
        return "bg-amber-500 text-white";
    }

    return "bg-gray-200 text-gray-600";
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

        if (payload.progress?.status !== statuses.ProcessingText) {
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
                nextStatus === statuses.StopText
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
        if (newStatus === statuses.ProcessingText) {
            startPolling();

            return;
        }

        stopPolling();
    },
    { immediate: true },
);

onBeforeUnmount(stopPolling);

const stopGeneration = () => {
    if (actionForm.value.processing) {
        return;
    }

    actionForm.value.processing = true;

    applyOptimisticStatus(statuses.StopText);

    inertiaRoute.patch(
        route("back-office.story-books.text-generation.stop", {
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
            onFinish: () => {
                actionForm.value.processing = false;
            },
        },
    );
};

const resumeGeneration = () => {
    if (actionForm.value.processing) {
        return;
    }

    actionForm.value.processing = true;

    applyOptimisticStatus(statuses.ProcessingText);

    inertiaRoute.patch(
        route("back-office.story-books.text-generation.resume", {
            slug: props.storyBook?.slug,
        }),
        { _method: "patch" },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                startPolling();
            },
            onError: () => {
                liveProgress.value = props.progress ?? null;
            },
            onFinish: () => {
                actionForm.value.processing = false;
            },
        },
    );
};
</script>

<template>
    <div class="bg-white border rounded-xl p-5 shadow-sm space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <FontAwesomeIcon
                    v-if="isRunning"
                    icon="spinner"
                    spin
                    class="text-blue-600"
                />
                <FontAwesomeIcon
                    v-else-if="isComplete"
                    icon="circle-check"
                    class="text-green-600"
                />
                <FontAwesomeIcon
                    v-else-if="isLocked"
                    icon="lock"
                    class="text-emerald-600"
                />
                <FontAwesomeIcon
                    v-else
                    icon="circle-pause"
                    class="text-amber-600"
                />

                <h3 class="text-base font-semibold">Text Generation</h3>

                <span
                    class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700"
                >
                    {{ completedCount }} / {{ totalCount }} steps
                </span>
            </div>

            <div class="flex items-center gap-2">
                <button
                    v-if="canResume"
                    type="button"
                    @click="resumeGeneration"
                    :disabled="actionForm.processing"
                    class="flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <FontAwesomeIcon
                        v-if="actionForm.processing"
                        icon="spinner"
                        spin
                    />
                    <FontAwesomeIcon v-else icon="rotate-right" />
                    Resume Generation
                </button>

                <button
                    v-if="isRunning"
                    type="button"
                    @click="stopGeneration"
                    :disabled="actionForm.processing"
                    class="flex items-center gap-2 rounded-md border border-red-300 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 transition hover:bg-red-100 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <FontAwesomeIcon
                        v-if="actionForm.processing"
                        icon="spinner"
                        spin
                    />
                    <FontAwesomeIcon v-else icon="stop" />
                    Stop
                </button>
            </div>
        </div>

        <div class="w-full">
            <div
                class="h-2 w-full overflow-hidden rounded-full bg-gray-200"
                role="progressbar"
                :aria-valuenow="percentage"
                aria-valuemin="0"
                aria-valuemax="100"
            >
                <div
                    class="h-full rounded-full transition-all duration-500"
                    :class="
                        isComplete
                            ? 'bg-green-500'
                            : isRunning
                              ? 'bg-blue-600'
                              : isStopped
                                ? 'bg-amber-500'
                                : 'bg-gray-400'
                    "
                    :style="{ width: `${percentage}%` }"
                ></div>
            </div>

            <p class="mt-1 text-right text-xs text-gray-400">
                {{ percentage }}%
            </p>
        </div>

        <p
            v-if="isRunning && currentProgress?.current_step_name"
            class="flex items-center gap-2 rounded-lg border border-blue-100 bg-blue-50 p-3 text-sm text-blue-800"
        >
            <FontAwesomeIcon icon="spinner" spin />
            Running step {{ currentProgress?.current_step }} of
            {{ totalCount }} &mdash; {{ currentProgress?.current_step_name }}
        </p>

        <p
            v-if="currentProgress?.error_message"
            class="flex items-start gap-2 rounded-lg border border-red-100 bg-red-50 p-3 text-sm text-red-700"
        >
            <FontAwesomeIcon
                icon="circle-exclamation"
                class="mt-0.5 flex-shrink-0"
            />
            <span>
                <span class="font-medium">
                    Step {{ currentProgress?.current_step }} failed:
                </span>
                {{ currentProgress?.error_message }}
            </span>
        </p>

        <p
            v-if="isStopped"
            class="flex items-center gap-2 rounded-lg border border-amber-100 bg-amber-50 p-3 text-sm text-amber-700"
        >
            <FontAwesomeIcon icon="circle-pause" />
            Generation stopped at step
            {{ currentProgress?.current_step }}. Resume to continue.
        </p>

        <p
            v-if="isComplete"
            class="flex items-center gap-2 rounded-lg border border-green-100 bg-green-50 p-3 text-sm text-green-700"
        >
            <FontAwesomeIcon icon="circle-check" />
            All {{ totalCount }} text generation steps are complete. Review the
            content, then start illustration generation.
        </p>

        <ol class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
            <li
                v-for="step in steps"
                :key="step.number"
                class="flex items-center gap-2 rounded-lg border px-3 py-2 text-left text-sm font-medium"
                :class="stepClasses(step)"
            >
                <span
                    class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full text-xs font-bold"
                    :class="stepIconClasses(step)"
                >
                    <FontAwesomeIcon
                        v-if="step.state === 'completed'"
                        icon="circle-check"
                        class="text-xs"
                    />
                    <FontAwesomeIcon
                        v-else-if="step.state === 'processing'"
                        icon="spinner"
                        spin
                        class="text-xs"
                    />
                    <FontAwesomeIcon
                        v-else-if="step.state === 'failed'"
                        icon="circle-exclamation"
                        class="text-xs"
                    />
                    <FontAwesomeIcon
                        v-else-if="step.state === 'stopped'"
                        icon="circle-pause"
                        class="text-xs"
                    />
                    <FontAwesomeIcon
                        v-else
                        icon="clock"
                        class="text-xs"
                    />
                </span>

                <span class="min-w-0 flex-1 break-words">
                    {{ step.number }}. {{ step.name }}
                </span>
            </li>
        </ol>
    </div>
</template>
