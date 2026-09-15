<script setup>
import Layout from "@/pages/layouts/AuthLayout.vue";
import InfiniteScrollApiSelect from "@/components/common/multi-select/InfiniteScrollApiSelect.vue";

import { ref, computed, onMounted, nextTick, watch } from "vue";
import { Head, useForm } from "@inertiajs/vue3";

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library as FontAwesomeLibrary } from "@fortawesome/fontawesome-svg-core";
import {
    faSpinner,
    faCheck,
    faLock,
    faArrowLeft,
    faWandMagicSparkles,
    faBrain,
    faLightbulb,
    faUser,
    faGlobe,
    faLocationDot,
    faFlag,
    faFilm,
    faGear,
    faClock,
    faDiagramProject,
    faShuffle,
    faList,
    faComments,
    faFileLines,
    faBookOpen,
} from "@fortawesome/free-solid-svg-icons";

FontAwesomeLibrary.add(
    faSpinner,
    faCheck,
    faLock,
    faArrowLeft,
    faWandMagicSparkles,
    faBrain,
    faLightbulb,
    faUser,
    faGlobe,
    faLocationDot,
    faFlag,
    faFilm,
    faGear,
    faClock,
    faDiagramProject,
    faShuffle,
    faList,
    faComments,
    faFileLines,
    faBookOpen,
);

defineOptions({ layout: Layout });

const createPageTitle = "Create Story Book";

const { novel, storyBook } = defineProps({
    novel: {
        type: Object,
        default: null,
    },
    storyBook: {
        type: Object,
        default: null,
    },
});

const sourceNovel = novel ?? storyBook;
const initialInput =
    novel?.input && typeof novel.input === "object"
        ? novel.input
        : storyBook?.input && typeof storyBook.input === "object"
          ? storyBook.input
          : sourceNovel ?? {};

const initialValue = (field) => {
    const value = initialInput[field];

    if (value === null || value === undefined) {
        return "";
    }

    return typeof value === "string" ? value : JSON.stringify(value, null, 2);
};

const isUpdate = computed(() => !!sourceNovel?.title);

const pageTitle = computed(() => {
    return isUpdate.value ? `Edit ${sourceNovel?.title}` : "New Story Book";
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
        text: "Create the core story foundation with its title, subtitle, plot, theme, genre, and tone.",
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
        title: "Story Structure",
        text: "Create the overall narrative through its beginning, middle, ending, acts, and turning points.",
        icon: ["fas", "diagram-project"],
    },
    {
        number: "10",
        title: "Twists & Foreshadowing",
        text: "Create hidden narrative elements with twists, clues, reveals, and future connections.",
        icon: ["fas", "shuffle"],
    },
    {
        number: "11",
        title: "Scene Plan",
        text: "Create scene progression with objectives, events, locations, and purpose.",
        icon: ["fas", "list"],
    },
    {
        number: "12",
        title: "Dialogue Plan",
        text: "Create dialogue planning with dialogue points, emotional beats, and conversation flow.",
        icon: ["fas", "comments"],
    },
    {
        number: "13",
        title: "Page Plan",
        text: "Create page-by-page story planning with page sequence, descriptions, and illustration notes.",
        icon: ["fas", "file-lines"],
    },
];

const STEP_FIELDS = {
    1: ["title", "sub_title", "plot", "theme", "genre", "tone"],
    2: ["characters"],
    3: ["world_bible"],
    4: ["locations"],
    5: ["factions"],
    6: ["creatures"],
    7: ["systems"],
    8: ["timeline"],
    9: ["story_structure"],
    10: ["twists_and_foreshadowing"],
    11: ["scene_plans"],
    12: ["dialogue_plans"],
    13: ["page_plan"],
};

const FIELD_LABELS = {
    title: "Title",
    sub_title: "Subtitle",
    plot: "Plot",
    theme: "Theme",
    genre: "Genre",
    tone: "Tone",
    characters: "Characters",
    world_bible: "World Vibe",
    locations: "Locations",
    factions: "Factions",
    creatures: "Creature",
    systems: "System",
    timeline: "Timeline",
    story_structure: "Story Structure",
    twists_and_foreshadowing: "Twists & Foreshadowing",
    scene_plans: "Scene Plan",
    dialogue_plans: "Dialogue Plan",
    page_plan: "Page Plan",
};

const novelForm = useForm({
    title: initialValue("title"),
    sub_title: initialValue("sub_title"),
    plot: initialValue("plot"),
    theme: initialValue("theme"),
    genre: initialValue("genre"),
    tone: initialValue("tone"),
    characters: initialValue("characters"),
    world_bible: initialValue("world_bible"),
    locations: initialValue("locations"),
    factions: initialValue("factions"),
    creatures: initialValue("creatures"),
    systems: initialValue("systems"),
    timeline: initialValue("timeline"),
    story_structure: initialValue("story_structure"),
    twists_and_foreshadowing: initialValue("twists_and_foreshadowing"),
    scene_plans: initialValue("scene_plans"),
    dialogue_plans: initialValue("dialogue_plans"),
    page_plan: initialValue("page_plan"),
    additional_information: initialValue("additional_information"),
    language_id: sourceNovel?.language_id ?? initialInput.language_id ?? null,
    genre_ids: sourceNovel?.genre_ids ?? initialInput.genre_ids ?? [],
    story_book_type_id:
        sourceNovel?.story_book_type_id ?? initialInput.story_book_type_id ?? null,
    audience_id: sourceNovel?.audience_id ?? initialInput.audience_id ?? null,
    ai_brain_id: sourceNovel?.ai_brain_id ?? initialInput.ai_brain_id ?? null,
});

const activeStep = ref(1);
const completedSteps = ref(new Set());
const isStoryBookComplete = ref(false);
const isInitialAudienceLoad = ref(true);

const activeStepDefinition = computed(
    () => STEP_DEFINITIONS[activeStep.value - 1],
);

const activeStepFields = computed(() => STEP_FIELDS[activeStep.value] ?? []);

const genresApiUrl = computed(() => {
    if (!novelForm.audience_id) {
        return route("search.genres");
    }

    return `${route("search.genres")}?audience_id=${encodeURIComponent(
        novelForm.audience_id,
    )}`;
});

watch(
    () => novelForm.audience_id,
    (newAudienceId, oldAudienceId) => {
        if (isInitialAudienceLoad.value) {
            isInitialAudienceLoad.value = false;
            return;
        }

        if (newAudienceId === oldAudienceId) {
            return;
        }

        novelForm.language_id = null;
        novelForm.genre_ids = [];
        novelForm.story_book_type_id = null;
        novelForm.additional_information = "";

        novelForm.clearErrors(
            "language_id",
            "genre_ids",
            "story_book_type_id",
            "additional_information",
        );
    },
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

const validateStep = (stepId) => {
    novelForm.clearErrors();

    if (stepId !== 1) {
        return true;
    }

    let valid = true;

    if (!novelForm.language_id) {
        novelForm.setError("language_id", "Language is required");
        valid = false;
    }

    if (!Array.isArray(novelForm.genre_ids) || novelForm.genre_ids.length === 0) {
        novelForm.setError("genre_ids", "Genres is required");
        valid = false;
    }

    if (!novelForm.story_book_type_id) {
        novelForm.setError("story_book_type_id", "Story Book type is required");
        valid = false;
    }

    if (!novelForm.audience_id) {
        novelForm.setError("audience_id", "Audience is required");
        valid = false;
    }

    if (!novelForm.ai_brain_id) {
        novelForm.setError("ai_brain_id", "AI Brain selection is required");
        valid = false;
    }

    return valid;
};

const buildAiBrainSearchUrl = () => route("search.ai-brains");

const markStepComplete = (stepId) => {
    completedSteps.value = new Set([...completedSteps.value, stepId]);

    if (stepId < STEP_DEFINITIONS.length) {
        activeStep.value = stepId + 1;
    }
};

const generateActiveStep = () => {
    const stepId = activeStep.value;

    if (novelForm.processing || !validateStep(stepId)) {
        return;
    }

    if (stepId === 1) {
        novelForm.post(route("back-office.story-books.generate.foundation"), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                markStepComplete(stepId);
                novelForm.clearErrors();
            },
        });

        return;
    }

    markStepComplete(stepId);
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
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 md:p-6">
            <div class="flex flex-col">
                <div class="flex items-center px-0 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold flex items-center gap-2">
                        <FontAwesomeIcon icon="wand-magic-sparkles" class="text-purple-600" />
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
                            :disabled="!isStepAccessible(index + 1) && !isStepCompleted(index + 1)"
                            class="flex items-center gap-2 rounded-lg border px-3 py-2 text-left text-sm font-medium transition disabled:opacity-40 disabled:cursor-not-allowed"
                            :class="{
                                'border-blue-200 bg-blue-50 text-blue-700': getStepState(index + 1) === 'active',
                                'border-green-200 bg-green-50 text-green-700': getStepState(index + 1) === 'completed',
                                'border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50': getStepState(index + 1) === 'accessible',
                                'border-gray-100 text-gray-300': getStepState(index + 1) === 'locked',
                            }"
                        >
                            <span class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full text-xs font-bold" :class="{
                                'bg-blue-600 text-white': getStepState(index + 1) === 'active',
                                'bg-green-500 text-white': getStepState(index + 1) === 'completed',
                                'bg-gray-200 text-gray-600': getStepState(index + 1) === 'accessible',
                                'bg-gray-100 text-gray-400': getStepState(index + 1) === 'locked',
                            }">
                                <FontAwesomeIcon v-if="isStepCompleted(index + 1)" icon="check" class="text-xs" />
                                <span v-else>{{ step.number }}</span>
                            </span>
                            <FontAwesomeIcon :icon="step.icon" class="flex-shrink-0 text-xs" />
                            <span class="min-w-0 flex-1 break-words">{{ step.title }}</span>
                            <FontAwesomeIcon v-if="getStepState(index + 1) === 'locked'" icon="lock" class="flex-shrink-0 text-xs" />
                        </button>
                    </nav>

                    <nav class="md:hidden -mx-2 px-2 pb-3">
                        <button
                            v-for="(step, index) in STEP_DEFINITIONS"
                            :key="step.number"
                            type="button"
                            @click="goToStep(index + 1)"
                            :disabled="!isStepAccessible(index + 1) && !isStepCompleted(index + 1)"
                            class="w-full flex items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm transition disabled:opacity-40 disabled:cursor-not-allowed"
                            :class="{
                                'bg-blue-50 text-blue-700': getStepState(index + 1) === 'active' || getStepState(index + 1) === 'completed',
                                'text-gray-600 hover:bg-gray-50': getStepState(index + 1) === 'accessible',
                                'text-gray-300': getStepState(index + 1) === 'locked',
                            }"
                        >
                            <span class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full text-xs font-bold" :class="{
                                'bg-blue-600 text-white': getStepState(index + 1) === 'active',
                                'bg-green-500 text-white': getStepState(index + 1) === 'completed',
                                'bg-gray-200 text-gray-600': getStepState(index + 1) === 'accessible',
                                'bg-gray-100 text-gray-400': getStepState(index + 1) === 'locked',
                            }">
                                <FontAwesomeIcon v-if="isStepCompleted(index + 1)" icon="check" class="text-xs" />
                                <span v-else>{{ step.number }}</span>
                            </span>
                            <FontAwesomeIcon :icon="step.icon" class="flex-shrink-0 text-xs" />
                            <span class="flex-1 break-words">{{ step.title }}</span>
                            <FontAwesomeIcon v-if="getStepState(index + 1) === 'locked'" icon="lock" class="flex-shrink-0 text-xs" />
                        </button>
                    </nav>
                </div>

                <div class="px-0 py-6">
                    <section class="space-y-6">
                        <div class="bg-white border rounded-xl p-5 shadow-sm space-y-4">
                            <div class="flex items-start gap-3">
                                <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                                    <FontAwesomeIcon :icon="activeStepDefinition.icon" />
                                </span>
                                <div>
                                    <h3 class="text-base font-semibold">{{ activeStepDefinition.title }}</h3>
                                    <p class="mt-1 text-sm text-gray-500">{{ activeStepDefinition.text }}</p>
                                </div>
                            </div>

                            <div v-if="activeStep === 1" class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-gray-100 pt-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1">Audience <span class="text-red-500">*</span></label>
                                    <InfiniteScrollApiSelect
                                        :form="novelForm"
                                        fieldName="audience_id"
                                        :selectedItem="sourceNovel?.audience"
                                        :apiUrl="route('search.audiences')"
                                        :multiple="false"
                                        placeholder="Select audience"
                                    />
                                    <p v-if="novelForm.errors.audience_id" class="text-red-500 text-sm mt-1">{{ novelForm.errors.audience_id }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Language <span class="text-red-500">*</span></label>
                                    <InfiniteScrollApiSelect
                                        :form="novelForm"
                                        fieldName="language_id"
                                        :selectedItem="sourceNovel?.language"
                                        :apiUrl="route('search.languages')"
                                        :multiple="false"
                                        placeholder="Select language"
                                    />
                                    <p v-if="novelForm.errors.language_id" class="text-red-500 text-sm mt-1">{{ novelForm.errors.language_id }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Genres <span class="text-red-500">*</span></label>
                                    <InfiniteScrollApiSelect
                                        :form="novelForm"
                                        fieldName="genre_ids"
                                        :selectedItem="sourceNovel?.genres"
                                        :apiUrl="genresApiUrl"
                                        :multiple="true"
                                        placeholder="Select genres"
                                    />
                                    <p v-if="novelForm.errors.genre_ids" class="text-red-500 text-sm mt-1">{{ novelForm.errors.genre_ids }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Story Book Type <span class="text-red-500">*</span></label>
                                    <InfiniteScrollApiSelect
                                        :form="novelForm"
                                        fieldName="story_book_type_id"
                                        :selectedItem="sourceNovel?.story_book_type"
                                        :apiUrl="route('search.story-book-types')"
                                        :multiple="false"
                                        placeholder="Select story book type"
                                    />
                                    <p v-if="novelForm.errors.story_book_type_id" class="text-red-500 text-sm mt-1">{{ novelForm.errors.story_book_type_id }}</p>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium mb-1">Additional Information</label>
                                    <textarea v-model="novelForm.additional_information" rows="3" placeholder="Any additional context or instructions for the AI..." class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none border-gray-300"></textarea>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-gray-100 pt-4">
                                <div v-for="field in activeStepFields" :key="field" :class="{ 'md:col-span-2': field === 'plot' || activeStep !== 1 }">
                                    <label class="block text-sm font-medium mb-1">{{ FIELD_LABELS[field] }}</label>
                                    <textarea v-model="novelForm[field]" :rows="activeStep === 1 ? field === 'plot' ? 7 : 3 : 10" :placeholder="`Enter ${FIELD_LABELS[field].toLowerCase()} details...`" class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none border-gray-300"></textarea>
                                    <p v-if="novelForm.errors[field]" class="text-red-500 text-sm mt-1">{{ novelForm.errors[field] }}</p>
                                </div>
                            </div>
                        </div>

                        <div v-if="activeStep === 1" class="bg-white border rounded-xl p-5 shadow-sm space-y-4">
                            <div class="flex items-center gap-2">
                                <FontAwesomeIcon icon="brain" class="text-purple-600" />
                                <h3 class="text-base font-semibold">AI Brain Configuration</h3>
                            </div>
                            <p class="text-sm text-gray-500">Select the AI model that will generate the story foundation.</p>
                            <div class="border-2 border-dashed border-purple-200 rounded-xl p-4 bg-gradient-to-br from-purple-50 to-blue-50">
                                <InfiniteScrollApiSelect
                                    :form="novelForm"
                                    fieldName="ai_brain_id"
                                    :selectedItem="sourceNovel?.ai_brain"
                                    :apiUrl="buildAiBrainSearchUrl()"
                                    :multiple="false"
                                    placeholder="Select AI Brain"
                                    :error="novelForm.errors.ai_brain_id"
                                    class="ai-brain-select"
                                />
                            </div>
                            <p v-if="novelForm.errors.ai_brain_id" class="text-red-500 text-sm">{{ novelForm.errors.ai_brain_id }}</p>
                        </div>
                    </section>

                    <div v-if="isStoryBookComplete" class="mt-6 rounded-xl border border-green-200 bg-green-50 p-5 text-center">
                        <FontAwesomeIcon icon="check" class="text-2xl text-green-600" />
                        <h3 class="mt-2 text-lg font-semibold text-green-800">Story Book Complete</h3>
                        <p class="mt-1 text-sm text-green-700">All story modules are ready for the final story book.</p>
                    </div>
                </div>

                <div class="px-0 py-4 border-t border-gray-200 flex justify-between items-center gap-3">
                    <button type="button" @click="goPrev" :disabled="activeStep === 1" class="px-4 py-2 text-sm rounded-md border border-gray-300 hover:bg-gray-50 transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-2">
                        <FontAwesomeIcon icon="arrow-left" />
                        Previous
                    </button>

                    <span class="hidden sm:inline text-xs text-gray-400">Step {{ activeStep }} of {{ STEP_DEFINITIONS.length }}</span>

                    <div class="flex items-center gap-2">
                        <button v-if="activeStep === STEP_DEFINITIONS.length && isStepCompleted(activeStep)" type="button" @click="completeStoryBook" :disabled="isStoryBookComplete" class="px-5 py-2 text-sm bg-green-600 hover:bg-green-700 text-white rounded-md flex items-center gap-2 transition disabled:opacity-60 disabled:cursor-not-allowed">
                            <FontAwesomeIcon icon="book-open" />
                            {{ isStoryBookComplete ? "Completed" : "Complete Story Book" }}
                        </button>
                        <button v-else type="button" @click="generateActiveStep" :disabled="novelForm.processing" class="px-5 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-md flex items-center gap-2 transition disabled:opacity-60 disabled:cursor-not-allowed">
                            <FontAwesomeIcon v-if="novelForm.processing" icon="spinner" spin />
                            <FontAwesomeIcon v-else icon="wand-magic-sparkles" />
                            {{ novelForm.processing ? "Generating..." : `Generate ${activeStepDefinition.title}` }}
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
