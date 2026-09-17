<script setup>
import Layout from "@/pages/layouts/AuthLayout.vue";

import StoryBookStep2CharactersForm from "@/components/back-office/story-book/StoryBookStep2CharactersForm.vue";
import StoryBookStep6CreatureForm from "@/components/back-office/story-book/StoryBookStep6CreatureForm.vue";
import StoryBookStep11DialoguePlanForm from "@/components/back-office/story-book/StoryBookStep11DialoguePlanForm.vue";
import StoryBookStep5FactionsForm from "@/components/back-office/story-book/StoryBookStep5FactionsForm.vue";
import StoryBookStep1FoundationForm from "@/components/back-office/story-book/StoryBookStep1FoundationForm.vue";
import StoryBookStep4LocationsForm from "@/components/back-office/story-book/StoryBookStep4LocationsForm.vue";
import StoryBookStep12PagePlanForm from "@/components/back-office/story-book/StoryBookStep12PagePlanForm.vue";
import StoryBookStep10ScenePlanForm from "@/components/back-office/story-book/StoryBookStep10ScenePlanForm.vue";
import StoryBookStep7SystemForm from "@/components/back-office/story-book/StoryBookStep7SystemForm.vue";
import StoryBookStep8TimelineForm from "@/components/back-office/story-book/StoryBookStep8TimelineForm.vue";
import StoryBookStep9TwistsAndForeshadowingForm from "@/components/back-office/story-book/StoryBookStep9TwistsAndForeshadowingForm.vue";
import StoryBookStep3WorldVibeForm from "@/components/back-office/story-book/StoryBookStep3WorldVibeForm.vue";

import { computed, nextTick, onMounted, ref } from "vue";
import { Head } from "@inertiajs/vue3";

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library as FontAwesomeLibrary } from "@fortawesome/fontawesome-svg-core";
import {
    faArrowLeft,
    faBookOpen,
    faCheck,
    faClock,
    faComments,
    faFileLines,
    faFilm,
    faFlag,
    faGear,
    faGlobe,
    faLightbulb,
    faList,
    faLocationDot,
    faLock,
    faShuffle,
    faUser,
    faWandMagicSparkles,
} from "@fortawesome/free-solid-svg-icons";

FontAwesomeLibrary.add(
    faArrowLeft,
    faBookOpen,
    faCheck,
    faClock,
    faComments,
    faFileLines,
    faFilm,
    faFlag,
    faGear,
    faGlobe,
    faLightbulb,
    faList,
    faLocationDot,
    faLock,
    faShuffle,
    faUser,
    faWandMagicSparkles,
);

defineOptions({ layout: Layout });

const createPageTitle = "Create Story Book";

const { storyBook } = defineProps({
    storyBook: {
        type: Object,
        default: null,
    },
});
const isUpdate = computed(() => !!storyBook?.id);

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
        number: "01",
        title: "Foundation",
        text: "Create the core story foundation with its title, subtitle, foundation, theme, genre, and tone.",
        icon: ["fas", "lightbulb"],
    },
    {
        number: "02",
        title: "Character",
        text: "Create protagonists, supporting characters, antagonists, relationships, and character arcs.",
        icon: ["fas", "user"],
    },
    {
        number: "03",
        title: "World Vibe",
        text: "Create the story world through its overview, culture, history, environment, rules, and lore.",
        icon: ["fas", "globe"],
    },
    {
        number: "04",
        title: "Location",
        text: "Create important places, regions, cities, and special story locations.",
        icon: ["fas", "location-dot"],
    },
    {
        number: "05",
        title: "Faction",
        text: "Create organizations, kingdoms, rival groups, goals, and conflicts.",
        icon: ["fas", "flag"],
    },
    {
        number: "06",
        title: "Creature",
        text: "Create short cinematic story moments, special entities, and important scenes.",
        icon: ["fas", "film"],
    },
    {
        number: "07",
        title: "System",
        text: "Create magic, technology, world mechanics, and the limitations that shape the story.",
        icon: ["fas", "gear"],
    },
    {
        number: "08",
        title: "Timeline",
        text: "Create chronological story history with past events, present events, future events, and milestones.",
        icon: ["fas", "clock"],
    },
    {
        number: "09",
        title: "Twists & Foreshadowing",
        text: "Create hidden narrative elements with twists, clues, reveals, and future connections.",
        icon: ["fas", "shuffle"],
    },
    {
        number: "10",
        title: "Scene Plan",
        text: "Create scene progression with objectives, events, locations, and purpose.",
        icon: ["fas", "list"],
    },
    {
        number: "11",
        title: "Dialogue Plan",
        text: "Create dialogue planning with dialogue points, emotional beats, and conversation flow.",
        icon: ["fas", "comments"],
    },
    {
        number: "12",
        title: "Page Plan",
        text: "Create page-by-page story planning with page sequence, descriptions, and illustration notes.",
        icon: ["fas", "file-lines"],
    },
];

const activeStep = ref(1);
const completedSteps = ref(new Set());
const isStoryBookComplete = ref(false);

const activeStepDefinition = computed(
    () => STEP_DEFINITIONS[activeStep.value - 1],
);

const isStepAccessible = (stepId) => {
    if (stepId === 1) {
        return true;
    }

    return completedSteps.value.has(stepId - 1);
};

const isStepCompleted = (stepId) => completedSteps.value.has(stepId);

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

const handleStepCompleted = () => {
    const stepId = activeStep.value;

    completedSteps.value = new Set([...completedSteps.value, stepId]);

    if (stepId < STEP_DEFINITIONS.length) {
        activeStep.value = stepId + 1;
    }
};

const completeStoryBook = () => {
    if (isStepCompleted(STEP_DEFINITIONS.length)) {
        isStoryBookComplete.value = true;
    }
};

const goToStep = (stepId) => {
    if (isStepAccessible(stepId) || isStepCompleted(stepId)) {
        activeStep.value = stepId;
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
                    <nav class="hidden md:grid md:grid-cols-4 gap-2 pb-4">
                        <button
                            v-for="(step, index) in STEP_DEFINITIONS"
                            :key="step.number"
                            type="button"
                            @click="goToStep(index + 1)"
                            :disabled="
                                !isStepAccessible(index + 1) &&
                                !isStepCompleted(index + 1)
                            "
                            class="flex items-center gap-2 rounded-lg border px-3 py-2 text-left text-sm font-medium transition disabled:opacity-40 disabled:cursor-not-allowed"
                            :class="{
                                'border-blue-200 bg-blue-50 text-blue-700':
                                    getStepState(index + 1) === 'active',
                                'border-green-200 bg-green-50 text-green-700':
                                    getStepState(index + 1) === 'completed',
                                'border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50':
                                    getStepState(index + 1) === 'accessible',
                                'border-gray-100 text-gray-300':
                                    getStepState(index + 1) === 'locked',
                            }"
                        >
                            <span
                                class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full text-xs font-bold"
                                :class="{
                                    'bg-blue-600 text-white':
                                        getStepState(index + 1) === 'active',
                                    'bg-green-500 text-white':
                                        getStepState(index + 1) === 'completed',
                                    'bg-gray-200 text-gray-600':
                                        getStepState(index + 1) ===
                                        'accessible',
                                    'bg-gray-100 text-gray-400':
                                        getStepState(index + 1) === 'locked',
                                }"
                            >
                                <FontAwesomeIcon
                                    v-if="isStepCompleted(index + 1)"
                                    icon="check"
                                    class="text-xs"
                                />
                                <span v-else>{{ step.number }}</span>
                            </span>
                            <FontAwesomeIcon
                                :icon="step.icon"
                                class="flex-shrink-0 text-xs"
                            />
                            <span class="min-w-0 flex-1 break-words">{{
                                step.title
                            }}</span>
                            <FontAwesomeIcon
                                v-if="getStepState(index + 1) === 'locked'"
                                icon="lock"
                                class="flex-shrink-0 text-xs"
                            />
                        </button>
                    </nav>

                    <nav class="md:hidden -mx-2 px-2 pb-3">
                        <button
                            v-for="(step, index) in STEP_DEFINITIONS"
                            :key="step.number"
                            type="button"
                            @click="goToStep(index + 1)"
                            :disabled="
                                !isStepAccessible(index + 1) &&
                                !isStepCompleted(index + 1)
                            "
                            class="w-full flex items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm transition disabled:opacity-40 disabled:cursor-not-allowed"
                            :class="{
                                'bg-blue-50 text-blue-700':
                                    getStepState(index + 1) === 'active' ||
                                    getStepState(index + 1) === 'completed',
                                'text-gray-600 hover:bg-gray-50':
                                    getStepState(index + 1) === 'accessible',
                                'text-gray-300':
                                    getStepState(index + 1) === 'locked',
                            }"
                        >
                            <span
                                class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full text-xs font-bold"
                                :class="{
                                    'bg-blue-600 text-white':
                                        getStepState(index + 1) === 'active',
                                    'bg-green-500 text-white':
                                        getStepState(index + 1) === 'completed',
                                    'bg-gray-200 text-gray-600':
                                        getStepState(index + 1) ===
                                        'accessible',
                                    'bg-gray-100 text-gray-400':
                                        getStepState(index + 1) === 'locked',
                                }"
                            >
                                <FontAwesomeIcon
                                    v-if="isStepCompleted(index + 1)"
                                    icon="check"
                                    class="text-xs"
                                />
                                <span v-else>{{ step.number }}</span>
                            </span>
                            <FontAwesomeIcon
                                :icon="step.icon"
                                class="flex-shrink-0 text-xs"
                            />
                            <span class="flex-1 break-words">{{
                                step.title
                            }}</span>
                            <FontAwesomeIcon
                                v-if="getStepState(index + 1) === 'locked'"
                                icon="lock"
                                class="flex-shrink-0 text-xs"
                            />
                        </button>
                    </nav>
                </div>

                <div class="px-0 py-6">
                    <section class="space-y-6">
                        <div class="bg-white border rounded-xl p-5 shadow-sm">
                            <div class="flex items-start gap-3">
                                <span
                                    class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600"
                                >
                                    <FontAwesomeIcon
                                        :icon="activeStepDefinition.icon"
                                    />
                                </span>
                                <div>
                                    <h3 class="text-base font-semibold">
                                        {{ activeStepDefinition.title }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ activeStepDefinition.text }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <StoryBookStep1FoundationForm
                            v-if="activeStep === 1"
                            :story-book="storyBook"
                            @completed="handleStepCompleted"
                        />

                        <StoryBookStep2CharactersForm
                            v-else-if="activeStep === 2"
                            :story-book="storyBook"
                            @completed="handleStepCompleted"
                        />

                        <StoryBookStep3WorldVibeForm
                            v-else-if="activeStep === 3"
                            :story-book="storyBook"
                            @completed="handleStepCompleted"
                        />

                        <StoryBookStep4LocationsForm
                            v-else-if="activeStep === 4"
                            :story-book="storyBook"
                            @completed="handleStepCompleted"
                        />

                        <StoryBookStep5FactionsForm
                            v-else-if="activeStep === 5"
                            :story-book="storyBook"
                            @completed="handleStepCompleted"
                        />

                        <StoryBookStep6CreatureForm
                            v-else-if="activeStep === 6"
                            :story-book="storyBook"
                            @completed="handleStepCompleted"
                        />

                        <StoryBookStep7SystemForm
                            v-else-if="activeStep === 7"
                            :story-book="storyBook"
                            @completed="handleStepCompleted"
                        />

                        <StoryBookStep8TimelineForm
                            v-else-if="activeStep === 8"
                            :story-book="storyBook"
                            @completed="handleStepCompleted"
                        />

                        <StoryBookStep9TwistsAndForeshadowingForm
                            v-else-if="activeStep === 9"
                            :story-book="storyBook"
                            @completed="handleStepCompleted"
                        />

                        <StoryBookStep10ScenePlanForm
                            v-else-if="activeStep === 10"
                            :story-book="storyBook"
                            @completed="handleStepCompleted"
                        />

                        <StoryBookStep11DialoguePlanForm
                            v-else-if="activeStep === 11"
                            :story-book="storyBook"
                            @completed="handleStepCompleted"
                        />

                        <StoryBookStep12PagePlanForm
                            v-else-if="activeStep === 12"
                            :story-book="storyBook"
                            @completed="handleStepCompleted"
                        />
                    </section>

                    <div
                        v-if="isStoryBookComplete"
                        class="mt-6 rounded-xl border border-green-200 bg-green-50 p-5 text-center"
                    >
                        <FontAwesomeIcon
                            icon="check"
                            class="text-2xl text-green-600"
                        />
                        <h3 class="mt-2 text-lg font-semibold text-green-800">
                            Story Book Complete
                        </h3>
                        <p class="mt-1 text-sm text-green-700">
                            All story modules are ready for the final story
                            book.
                        </p>
                    </div>
                </div>

                <div
                    class="px-0 py-4 border-t border-gray-200 flex justify-between items-center gap-3"
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

                    <span class="hidden sm:inline text-xs text-gray-400"
                        >Step {{ activeStep }} of
                        {{ STEP_DEFINITIONS.length }}</span
                    >

                    <div class="flex items-center gap-2">
                        <button
                            v-if="
                                activeStep === STEP_DEFINITIONS.length &&
                                isStepCompleted(activeStep)
                            "
                            type="button"
                            @click="completeStoryBook"
                            :disabled="isStoryBookComplete"
                            class="px-5 py-2 text-sm bg-green-600 hover:bg-green-700 text-white rounded-md flex items-center gap-2 transition disabled:opacity-60 disabled:cursor-not-allowed"
                        >
                            <FontAwesomeIcon icon="book-open" />
                            {{
                                isStoryBookComplete
                                    ? "Completed"
                                    : "Complete Story Book"
                            }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
