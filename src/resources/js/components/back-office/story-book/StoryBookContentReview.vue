<script setup>
import StoryBookJsonViewer from "@/components/back-office/story-book/StoryBookJsonViewer.vue";

import { computed, ref } from "vue";

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library as FontAwesomeLibrary } from "@fortawesome/fontawesome-svg-core";
import { faBookOpen, faFileLines } from "@fortawesome/free-solid-svg-icons";

FontAwesomeLibrary.add(faBookOpen, faFileLines);

const props = defineProps({
    storyBook: {
        type: Object,
        required: true,
    },
});

const SECTIONS = [
    { key: "characters", label: "Characters" },
    { key: "world_bible", label: "World Bible" },
    { key: "locations", label: "Locations" },
    { key: "factions", label: "Factions" },
    { key: "creatures", label: "Creatures" },
    { key: "systems", label: "Systems" },
    { key: "timeline", label: "Timeline" },
    { key: "story_structure", label: "Story Structure" },
    {
        key: "twists_and_foreshadowing",
        label: "Twists And Foreshadowing",
    },
    { key: "scene_plans", label: "Scene Plans" },
    { key: "dialogue_plans", label: "Dialogue Plans" },
    { key: "page_plan", label: "Page Plan" },
];

const activeSection = ref(SECTIONS[0].key);

const pages = computed(() => props.storyBook?.story_book_pages ?? []);

const hasContent = computed(() =>
    SECTIONS.some((section) => {
        const value = props.storyBook?.[section.key];

        if (value === null || value === undefined) {
            return false;
        }

        if (Array.isArray(value)) {
            return value.length > 0;
        }

        if (typeof value === "object") {
            return Object.keys(value).length > 0;
        }

        return value !== "";
    }),
);
</script>

<template>
    <div class="space-y-6">
        <div
            v-if="storyBook?.title || storyBook?.foundation"
            class="bg-white border rounded-xl p-5 shadow-sm space-y-3"
        >
            <div class="flex items-center gap-2">
                <FontAwesomeIcon icon="book-open" class="text-blue-600" />
                <h3 class="text-base font-semibold">Foundation</h3>
            </div>

            <div v-if="storyBook?.title">
                <h4 class="text-lg font-semibold text-gray-800">
                    {{ storyBook?.title }}
                </h4>
                <p
                    v-if="storyBook?.sub_title"
                    class="text-sm text-gray-500"
                >
                    {{ storyBook?.sub_title }}
                </p>
            </div>

            <p class="text-sm text-gray-700 whitespace-pre-wrap">
                {{ storyBook?.foundation }}
            </p>
        </div>

        <div
            v-if="hasContent"
            class="bg-white border rounded-xl p-5 shadow-sm space-y-4"
        >
            <div class="flex items-center gap-2">
                <FontAwesomeIcon icon="file-lines" class="text-purple-600" />
                <h3 class="text-base font-semibold">Generated Content</h3>
            </div>

            <div
                class="flex flex-wrap gap-2 border-b border-gray-200 pb-3"
            >
                <button
                    v-for="section in SECTIONS"
                    :key="section.key"
                    type="button"
                    @click="activeSection = section.key"
                    class="rounded-md px-3 py-1.5 text-xs font-medium transition"
                    :class="
                        activeSection === section.key
                            ? 'bg-purple-600 text-white'
                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                    "
                >
                    {{ section.label }}
                </button>
            </div>

            <StoryBookJsonViewer
                :label="
                    SECTIONS.find(
                        (section) => section.key === activeSection,
                    )?.label
                "
                :value="storyBook?.[activeSection]"
            />
        </div>

        <div
            v-if="pages.length"
            class="bg-white border rounded-xl p-5 shadow-sm space-y-4"
        >
            <div class="flex items-center gap-2">
                <FontAwesomeIcon icon="file-lines" class="text-green-600" />
                <h3 class="text-base font-semibold">Pages</h3>

                <span
                    class="rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700"
                >
                    {{ pages.length }} pages
                </span>
            </div>

            <ul class="space-y-3">
                <li
                    v-for="page in pages"
                    :key="page.no"
                    class="rounded-xl border border-gray-100 bg-gray-50 p-4 space-y-2"
                >
                    <div class="flex items-center gap-2">
                        <span
                            class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700"
                        >
                            {{ page.no }}
                        </span>
                        <span class="text-sm font-semibold text-gray-700">
                            Page {{ page.no }}
                        </span>
                    </div>

                    <p class="text-sm text-gray-700 whitespace-pre-wrap">
                        {{ page.narration || "Narration pending." }}
                    </p>

                    <p
                        v-if="page.illustration_prompt"
                        class="rounded-lg border border-purple-100 bg-purple-50 p-2 text-xs text-purple-800"
                    >
                        <span class="font-medium">Illustration Prompt:</span>
                        {{ page.illustration_prompt }}
                    </p>
                </li>
            </ul>
        </div>
    </div>
</template>
