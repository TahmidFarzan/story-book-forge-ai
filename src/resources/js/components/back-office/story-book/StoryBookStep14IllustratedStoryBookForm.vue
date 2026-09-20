<script setup>

import { computed, ref } from "vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library as FontAwesomeLibrary } from "@fortawesome/fontawesome-svg-core";
import {
    faBookOpen,
    faBrain,
    faCheck,
    faFileLines,
    faImage,
    faLock,
    faSpinner,
    faWandMagicSparkles,
} from "@fortawesome/free-solid-svg-icons";

FontAwesomeLibrary.add(
    faBookOpen,
    faBrain,
    faCheck,
    faFileLines,
    faImage,
    faLock,
    faSpinner,
    faWandMagicSparkles,
);

import StoryBookStep14Form from "@/components/back-office/story-book/StoryBookStep14Form.vue";
import StoryBookStep15Form from "@/components/back-office/story-book/StoryBookStep15Form.vue";
import StoryBookStep16Form from "@/components/back-office/story-book/StoryBookStep16Form.vue";

const emit = defineEmits(["completed"]);

const { storyBook } = defineProps({
    storyBook: {
        type: Object,
        default: null,
    },
});

const SUB_STEP_DEFINITIONS = [
    {
        number: "14",
        title: "Page Narration",
        text: "Generate the narration text for every page of the illustrated story book.",
        icon: "file-lines",
    },
    {
        number: "15",
        title: "Illustration Planning",
        text: "Select the illustration type applied to every page of the illustrated story book.",
        icon: "image",
    },
    {
        number: "16",
        title: "Illustration Generation",
        text: "Generate one illustration per page using an image-output AI Brain.",
        icon: "wand-magic-sparkles",
    },
];

const activeSubStep = ref(1);
const completedSubSteps = ref(new Set());

const activeSubStepDefinition = computed(
    () => SUB_STEP_DEFINITIONS[activeSubStep.value - 1],
);

const isSubStepAccessible = (subStepId) => {
    if (subStepId === 1) {
        return true;
    }

    return completedSubSteps.value.has(subStepId - 1);
};

const isSubStepCompleted = (subStepId) =>
    completedSubSteps.value.has(subStepId);

const handleSubStepCompleted = () => {
    const subStepId = activeSubStep.value;

    completedSubSteps.value = new Set([
        ...completedSubSteps.value,
        subStepId,
    ]);

    if (subStepId < SUB_STEP_DEFINITIONS.length) {
        activeSubStep.value = subStepId + 1;
    }

    emit("completed");
};

const goToSubStep = (subStepId) => {
    if (isSubStepAccessible(subStepId) || isSubStepCompleted(subStepId)) {
        activeSubStep.value = subStepId;
    }
};
</script>

<template>
    <div class="space-y-6">
        <div class="bg-white border rounded-xl p-5 shadow-sm">
            <div class="flex items-start gap-3">
                <span
                    class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600"
                >
                    <FontAwesomeIcon :icon="activeSubStepDefinition.icon" />
                </span>
                <div>
                    <h3 class="text-base font-semibold">
                        {{ activeSubStepDefinition.title }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ activeSubStepDefinition.text }}
                    </p>
                </div>
            </div>

            <nav class="mt-4 grid gap-2 md:grid-cols-3">
                <button
                    v-for="(subStep, index) in SUB_STEP_DEFINITIONS"
                    :key="subStep.number"
                    type="button"
                    @click="goToSubStep(index + 1)"
                    :disabled="
                        !isSubStepAccessible(index + 1) &&
                        !isSubStepCompleted(index + 1)
                    "
                    class="flex items-center gap-2 rounded-lg border px-3 py-2 text-left text-sm font-medium transition disabled:opacity-40 disabled:cursor-not-allowed"
                    :class="{
                        'border-blue-200 bg-blue-50 text-blue-700':
                            activeSubStep === index + 1,
                        'border-green-200 bg-green-50 text-green-700':
                            isSubStepCompleted(index + 1),
                        'border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50':
                            isSubStepAccessible(index + 1),
                        'border-gray-100 text-gray-300':
                            !isSubStepAccessible(index + 1) &&
                            !isSubStepCompleted(index + 1),
                    }"
                >
                    <span
                        class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full text-xs font-bold"
                        :class="{
                            'bg-blue-600 text-white':
                                activeSubStep === index + 1,
                            'bg-green-500 text-white':
                                isSubStepCompleted(index + 1),
                            'bg-gray-200 text-gray-600':
                                isSubStepAccessible(index + 1),
                            'bg-gray-100 text-gray-400':
                                !isSubStepAccessible(index + 1) &&
                                !isSubStepCompleted(index + 1),
                        }"
                    >
                        <FontAwesomeIcon
                            v-if="isSubStepCompleted(index + 1)"
                            icon="check"
                            class="text-xs"
                        />
                        <span v-else>{{ subStep.number }}</span>
                    </span>
                    <FontAwesomeIcon
                        :icon="subStep.icon"
                        class="flex-shrink-0 text-xs"
                    />
                    <span class="min-w-0 flex-1 break-words">{{
                        subStep.title
                    }}</span>
                    <FontAwesomeIcon
                        v-if="
                            !isSubStepAccessible(index + 1) &&
                            !isSubStepCompleted(index + 1)
                        "
                        icon="lock"
                        class="flex-shrink-0 text-xs"
                    />
                </button>
            </nav>
        </div>

        <StoryBookStep14Form
            v-if="activeSubStep === 1"
            :story-book="storyBook"
            @completed="handleSubStepCompleted"
        />

        <StoryBookStep15Form
            v-else-if="activeSubStep === 2"
            :story-book="storyBook"
            @completed="handleSubStepCompleted"
        />

        <StoryBookStep16Form
            v-else-if="activeSubStep === 3"
            :story-book="storyBook"
            @completed="handleSubStepCompleted"
        />
    </div>
</template>
