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
    faSpinner,
    faWandMagicSparkles,
} from "@fortawesome/free-solid-svg-icons";

FontAwesomeLibrary.add(
    faBookOpen,
    faBrain,
    faFileLines,
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

const pages = computed(() => storyBook?.pages ?? []);

const pageNarrationGeneratorForm = useForm({
    additional_information: null,
    ai_brain_id: null,
});

const validate = () => {
    pageNarrationGeneratorForm.clearErrors();

    let valid = true;

    if (!pageNarrationGeneratorForm.ai_brain_id) {
        pageNarrationGeneratorForm.setError(
            "ai_brain_id",
            "AI Brain selection is required",
        );
        valid = false;
    }

    return valid;
};

function buildAiBrainSearchUrl() {
    return route("search.ai-brains", {
        ai_brain_output_type_code: AiBrainOutputTypes.Text,
    });
}


const handleSuccess = () => {
    pageNarrationGeneratorForm.clearErrors();
    emit("completed", storyBook);
};

const submit = () => {
    if (
        !isUpdate.value ||
        pageNarrationGeneratorForm.processing ||
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
        route("back-office.story-books.regenerate.step14-1.page-narration", {
            slug: storyBook?.slug,
        }),
        {
            ...pageNarrationGeneratorForm.data(),
            _method: "patch",
        },
        requestConfig,
    );
};
</script>

<template>
    <div class="space-y-6">
        <div class="bg-white border rounded-xl p-5 shadow-sm space-y-4">
            <div>
                <p
                    v-if="
                        pageNarrationGeneratorForm.errors
                            .additional_information
                    "
                >
                    {{
                        pageNarrationGeneratorForm.errors
                            .additional_information
                    }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">
                    Page Narration Additional Information
                </label>

                <textarea
                    v-model="
                        pageNarrationGeneratorForm.additional_information
                    "
                    rows="3"
                    placeholder="Any additional context or instructions for the AI..."
                    class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none border-gray-300"
                ></textarea>

                <p
                    v-if="
                        pageNarrationGeneratorForm.errors
                            .additional_information
                    "
                >
                    {{
                        pageNarrationGeneratorForm.errors
                            .additional_information
                    }}
                </p>
            </div>
        </div>

        <div class="bg-white border rounded-xl p-5 shadow-sm space-y-4">
            <div class="flex items-center gap-2">
                <FontAwesomeIcon icon="brain" class="text-purple-600" />
                <h3 class="text-base font-semibold">AI Brain Configuration</h3>
            </div>

            <p class="text-sm text-gray-500">
                Select the AI model that will generate the page narration.
            </p>

            <div
                class="border-2 border-dashed border-purple-200 rounded-xl p-4 bg-gradient-to-br from-purple-50 to-blue-50"
            >
                <InfiniteScrollApiSelect
                    :form="pageNarrationGeneratorForm"
                    fieldName="ai_brain_id"
                    :selectedItem="storyBook?.ai_brain"
                    :apiUrl="buildAiBrainSearchUrl()"
                    :multiple="false"
                    placeholder="Select AI Brain"
                    :error="pageNarrationGeneratorForm.errors.ai_brain_id"
                    class="ai-brain-select"
                />
            </div>

            <p
                v-if="pageNarrationGeneratorForm.errors.ai_brain_id"
                class="text-red-500 text-sm"
            >
                {{ pageNarrationGeneratorForm.errors.ai_brain_id }}
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
                The narration is generated one page at a time and stored with
                the story book.
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
                </li>
            </ul>
        </div>

        <div class="flex justify-end">
            <button
                type="button"
                @click="submit"
                :disabled="!isUpdate || pageNarrationGeneratorForm.processing"
                class="px-5 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-md flex items-center gap-2 transition disabled:opacity-60 disabled:cursor-not-allowed"
            >
                <FontAwesomeIcon
                    v-if="pageNarrationGeneratorForm.processing"
                    icon="spinner"
                    spin
                />

                <FontAwesomeIcon v-else icon="wand-magic-sparkles" />

                {{
                    pageNarrationGeneratorForm.processing
                        ? "Generating..."
                        : "Generate Page Narration"
                }}
            </button>
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
