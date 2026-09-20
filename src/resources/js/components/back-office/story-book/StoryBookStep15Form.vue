<script setup>
import InfiniteScrollApiSelect from "@/components/common/multi-select/InfiniteScrollApiSelect.vue";
import { AiBrainOutputTypes } from "@/composables/useAiBrain";

import { computed } from "vue";
import { useForm, router as inertiaRoute } from "@inertiajs/vue3";

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library as FontAwesomeLibrary } from "@fortawesome/fontawesome-svg-core";
import {
    faBookOpen,
    faBrain,
    faFileLines,
    faImage,
    faSpinner,
    faWandMagicSparkles,
} from "@fortawesome/free-solid-svg-icons";

FontAwesomeLibrary.add(
    faBookOpen,
    faBrain,
    faFileLines,
    faImage,
    faSpinner,
    faWandMagicSparkles,
);

const emit = defineEmits(["completed"]);

const { storyBook } = defineProps({
    storyBook: {
        type: Object,
        default: null,
    },
});

const isUpdate = computed(() => !!storyBook?.id);

const pages = computed(() => storyBook?.story_book_pages ?? []);

function buildAiBrainSearchUrl() {
    return route("search.ai-brains", {
        ai_brain_output_type_code: AiBrainOutputTypes.Text,
    });
}

const illustrationPlanningForm = useForm({
    ai_brain_id: null,
    illustration_type_id: null,
});

const validate = () => {
    illustrationPlanningForm.clearErrors();

    let valid = true;

    if (!illustrationPlanningForm.illustration_type_id) {
        illustrationPlanningForm.setError(
            "illustration_type_id",
            "Illustration Type selection is required",
        );
        valid = false;
    }

    if (!illustrationPlanningForm.ai_brain_id) {
        illustrationPlanningForm.setError(
            "ai_brain_id",
            "AI Brain selection is required",
        );
        valid = false;
    }

    return valid;
};

const handleSuccess = () => {
    illustrationPlanningForm.clearErrors();
    emit("completed", storyBook);
};

const submit = () => {
    if (
        !isUpdate.value ||
        illustrationPlanningForm.processing ||
        !validate()
    ) {
        return;
    }

    const requestConfig = {
        preserveScroll: true,
        preserveState: true,
        onSuccess: handleSuccess,
    };

    inertiaRoute.patch(
        route("back-office.story-books.regenerate.step15.illustration-planning", {
            slug: storyBook?.slug,
        }),
        {
            ...illustrationPlanningForm.data(),
            _method: "patch",
        },
        requestConfig,
    );
};
</script>

<template>
    <div class="space-y-6">
        <div class="bg-white border rounded-xl p-5 shadow-sm space-y-4">
            <div class="flex items-center gap-2">
                <FontAwesomeIcon icon="image" class="text-purple-600" />
                <h3 class="text-base font-semibold">Illustration Type</h3>
            </div>

            <p class="text-sm text-gray-500">
                Select the illustration type applied to every page. The
                selected type's prompt instruction will be stored inside each
                generated page as illustration_type_prompt_instruction.
            </p>

            <InfiniteScrollApiSelect
                :form="illustrationPlanningForm"
                fieldName="illustration_type_id"
                :selectedItem="storyBook?.illustration_type"
                :apiUrl="route('search.illustration-types')"
                :multiple="false"
                placeholder="Select Illustration Type"
                :error="illustrationPlanningForm.errors.illustration_type_id"
                class="illustration-type-select"
            />

            <p
                v-if="illustrationPlanningForm.errors.illustration_type_id"
                class="text-red-500 text-sm"
            >
                {{ illustrationPlanningForm.errors.illustration_type_id }}
            </p>
        </div>

        <div class="bg-white border rounded-xl p-5 shadow-sm space-y-4">
            <div class="flex items-center gap-2">
                <FontAwesomeIcon icon="brain" class="text-purple-600" />
                <h3 class="text-base font-semibold">AI Brain Configuration</h3>
            </div>

            <p class="text-sm text-gray-500">
                Select the Text-output AI model that will generate the
                illustration prompts.
            </p>

            <div
                class="border-2 border-dashed border-purple-200 rounded-xl p-4 bg-gradient-to-br from-purple-50 to-blue-50"
            >
                <InfiniteScrollApiSelect
                    :form="illustrationPlanningForm"
                    fieldName="ai_brain_id"
                    :selectedItem="storyBook?.ai_brain"
                    :apiUrl="buildAiBrainSearchUrl()"
                    :multiple="false"
                    placeholder="Select AI Brain"
                    :error="illustrationPlanningForm.errors.ai_brain_id"
                    class="ai-brain-select"
                />
            </div>

            <p
                v-if="illustrationPlanningForm.errors.ai_brain_id"
                class="text-red-500 text-sm"
            >
                {{ illustrationPlanningForm.errors.ai_brain_id }}
            </p>
        </div>

        <div
            v-if="pages.length"
            class="bg-white border rounded-xl p-5 shadow-sm space-y-3"
        >
            <div class="flex items-center gap-2">
                <FontAwesomeIcon icon="book-open" class="text-blue-600" />
                <h3 class="text-base font-semibold">Generated Pages</h3>
            </div>

            <p class="text-sm text-gray-500">
                Every page receives the selected illustration type's prompt
                instruction and one AI-generated illustration prompt.
            </p>

            <ul class="space-y-2">
                <li
                    v-for="page in pages"
                    :key="page.no"
                    class="rounded-lg border border-gray-100 bg-gray-50 p-3"
                >
                    <div class="flex items-center gap-2">
                        <span
                            class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700"
                        >
                            {{ page.no }}
                        </span>
                        <FontAwesomeIcon
                            icon="file-lines"
                            class="text-xs text-gray-400"
                        />
                    </div>
                    <p class="mt-2 text-sm text-gray-700">
                        {{ page.narration || "Narration pending." }}
                    </p>
                    <p
                        class="mt-2 text-xs text-purple-700 bg-purple-50 border border-purple-100 rounded-md p-2"
                    >
                        <span class="font-medium">Type Instruction:</span>
                        {{
                            page.illustration_type_prompt_instruction ||
                            "Illustration type instruction pending."
                        }}
                    </p>
                    <p
                        class="mt-2 text-xs text-blue-700 bg-blue-50 border border-blue-100 rounded-md p-2"
                    >
                        <span class="font-medium">Illustration Prompt:</span>
                        {{
                            page.illustration_prompt ||
                            "Illustration prompt pending."
                        }}
                    </p>
                </li>
            </ul>
        </div>

        <div class="flex justify-end">
            <button
                type="button"
                @click="submit"
                :disabled="!isUpdate || illustrationPlanningForm.processing"
                class="px-5 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-md flex items-center gap-2 transition disabled:opacity-60 disabled:cursor-not-allowed"
            >
                <FontAwesomeIcon
                    v-if="illustrationPlanningForm.processing"
                    icon="spinner"
                    spin
                />

                <FontAwesomeIcon v-else icon="wand-magic-sparkles" />

                {{
                    illustrationPlanningForm.processing
                        ? "Generating..."
                        : "Generate Illustration Planning"
                }}
            </button>
        </div>
    </div>
</template>

<style scoped>
.illustration-type-select :deep(.multiselect) {
    min-height: 44px;
}

.illustration-type-select :deep(.multiselect__tags) {
    min-height: 44px;
    padding: 8px 40px 0 12px;
    border-color: #c084fc;
    border-width: 1px;
    border-radius: 0.5rem;
    background: white;
}

.illustration-type-select :deep(.multiselect__single) {
    padding: 4px 0 0 0;
    margin-bottom: 0;
    color: #1f2937;
}

.illustration-type-select :deep(.multiselect__placeholder) {
    padding: 4px 0 0 0;
    color: #9ca3af;
}

.illustration-type-select :deep(.multiselect__select) {
    height: 44px;
}

.illustration-type-select :deep(.multiselect__content-wrapper) {
    border-color: #c084fc;
}

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
