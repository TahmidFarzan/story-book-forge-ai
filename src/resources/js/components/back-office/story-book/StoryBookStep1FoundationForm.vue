<script setup>
import InfiniteScrollApiSelect from "@/components/common/multi-select/InfiniteScrollApiSelect.vue";

import { computed, watch } from "vue";
import { useForm, router as inertiaRoute } from "@inertiajs/vue3";

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library as FontAwesomeLibrary } from "@fortawesome/fontawesome-svg-core";
import {
    faBrain,
    faSpinner,
    faWandMagicSparkles,
} from "@fortawesome/free-solid-svg-icons";

FontAwesomeLibrary.add(faBrain, faSpinner, faWandMagicSparkles);

const emit = defineEmits(["completed"]);

const { storyBook } = defineProps({
    storyBook: {
        type: Object,
        default: null,
    },
});

const isUpdate = computed(() => !!storyBook?.id);

const foundationGeneratorForm = useForm({
    additional_information: storyBook?.additional_information ?? "",
    language_id: storyBook?.language_id ?? null,
    genre_ids:storyBook?.genres?.map((genre) => genre.id) ?? [],
    story_book_type_id: storyBook?.story_book_type_id ?? null,
    audience_id: storyBook?.audience_id ?? null,
    ai_brain_id:  null,
});

let isInitialAudienceLoad = true;

const genresApiUrl = computed(() => {
    if (!foundationGeneratorForm.audience_id) {
        return route("search.genres");
    }

    return `${route("search.genres")}?audience_id=${encodeURIComponent(
        foundationGeneratorForm.audience_id,
    )}`;
});

watch(
    () => foundationGeneratorForm.audience_id,
    (newAudienceId, oldAudienceId) => {
        if (isInitialAudienceLoad) {
            isInitialAudienceLoad = false;
            return;
        }

        if (newAudienceId === oldAudienceId) {
            return;
        }

        foundationGeneratorForm.language_id = null;
        foundationGeneratorForm.genre_ids = [];
        foundationGeneratorForm.story_book_type_id = null;
        foundationGeneratorForm.additional_information = "";

        foundationGeneratorForm.clearErrors(
            "language_id",
            "genre_ids",
            "story_book_type_id",
            "additional_information",
        );
    },
);

const validate = () => {
    foundationGeneratorForm.clearErrors();

    let valid = true;

    if (!foundationGeneratorForm.language_id) {
        foundationGeneratorForm.setError("language_id", "Language is required");
        valid = false;
    }

    if (
        !Array.isArray(foundationGeneratorForm.genre_ids) ||
        foundationGeneratorForm.genre_ids.length === 0
    ) {
        foundationGeneratorForm.setError("genre_ids", "Genres is required");
        valid = false;
    }

    if (!foundationGeneratorForm.story_book_type_id) {
        foundationGeneratorForm.setError(
            "story_book_type_id",
            "Story Book type is required",
        );
        valid = false;
    }

    if (!foundationGeneratorForm.audience_id) {
        foundationGeneratorForm.setError("audience_id", "Audience is required");
        valid = false;
    }

    if (!foundationGeneratorForm.ai_brain_id) {
        foundationGeneratorForm.setError("ai_brain_id", "AI Brain selection is required");
        valid = false;
    }

    return valid;
};

const handleSuccess = () => {
    foundationGeneratorForm.clearErrors();
    emit("completed", storyBook);
};

const submit = () => {
    if (foundationGeneratorForm.processing || !validate()) {
        return;
    }

    const requestConfig = {
        preserveScroll: true,
        preserveState: true,
        onSuccess: handleSuccess,
    };

    if (isUpdate.value) {
        inertiaRoute.patch(
            route("back-office.story-books.generate.foundation", {
                slug: storyBook?.slug,
            }),
            { ...foundationGeneratorForm.data(), _method: "patch" },
            requestConfig,
        );
    } else {
        foundationGeneratorForm.post(
            route("back-office.story-books.create.foundation"),
            requestConfig,
        );
    }
};
</script>

<template>
    <div class="space-y-6">
        <div class="bg-white border rounded-xl p-5 shadow-sm space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1"
                        >Audience <span class="text-red-500">*</span></label
                    >
                    <InfiniteScrollApiSelect
                        :form="foundationGeneratorForm"
                        fieldName="audience_id"
                        :selectedItem="storyBook?.audience"
                        :apiUrl="route('search.audiences')"
                        :multiple="false"
                        placeholder="Select audience"
                    />
                    <p
                        v-if="foundationGeneratorForm.errors.audience_id"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ foundationGeneratorForm.errors.audience_id }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1"
                        >Language <span class="text-red-500">*</span></label
                    >
                    <InfiniteScrollApiSelect
                        :form="foundationGeneratorForm"
                        fieldName="language_id"
                        :selectedItem="storyBook?.language"
                        :apiUrl="route('search.languages')"
                        :multiple="false"
                        placeholder="Select language"
                    />
                    <p
                        v-if="foundationGeneratorForm.errors.language_id"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ foundationGeneratorForm.errors.language_id }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1"
                        >Genres <span class="text-red-500">*</span></label
                    >
                    <InfiniteScrollApiSelect
                        :form="foundationGeneratorForm"
                        fieldName="genre_ids"
                        :selectedItem="storyBook?.genres"
                        :apiUrl="genresApiUrl"
                        :multiple="true"
                        placeholder="Select genres"
                    />
                    <p
                        v-if="foundationGeneratorForm.errors.genre_ids"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ foundationGeneratorForm.errors.genre_ids }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1"
                        >Story Book Type
                        <span class="text-red-500">*</span></label
                    >
                    <InfiniteScrollApiSelect
                        :form="foundationGeneratorForm"
                        fieldName="story_book_type_id"
                        :selectedItem="storyBook?.story_book_type"
                        :apiUrl="route('search.story-book-types')"
                        :multiple="false"
                        placeholder="Select story book type"
                    />
                    <p
                        v-if="foundationGeneratorForm.errors.story_book_type_id"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ foundationGeneratorForm.errors.story_book_type_id }}
                    </p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1"
                        >Additional Information</label
                    >
                    <textarea
                        v-model="foundationGeneratorForm.additional_information"
                        rows="3"
                        placeholder="Any additional context or instructions for the AI..."
                        class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none border-gray-300"
                    ></textarea>
                </div>
            </div>
        </div>

        <div class="bg-white border rounded-xl p-5 shadow-sm space-y-4">
            <div class="flex items-center gap-2">
                <FontAwesomeIcon icon="brain" class="text-purple-600" />
                <h3 class="text-base font-semibold">AI Brain Configuration</h3>
            </div>
            <p class="text-sm text-gray-500">
                Select the AI model that will generate the story foundation.
            </p>
            <div
                class="border-2 border-dashed border-purple-200 rounded-xl p-4 bg-gradient-to-br from-purple-50 to-blue-50"
            >
                <InfiniteScrollApiSelect
                    :form="foundationGeneratorForm"
                    fieldName="ai_brain_id"
                    :selectedItem="storyBook?.ai_brain"
                    :apiUrl="route('search.ai-brains')"
                    :multiple="false"
                    placeholder="Select AI Brain"
                    :error="foundationGeneratorForm.errors.ai_brain_id"
                    class="ai-brain-select"
                />
            </div>
            <p
                v-if="foundationGeneratorForm.errors.ai_brain_id"
                class="text-red-500 text-sm"
            >
                {{ foundationGeneratorForm.errors.ai_brain_id }}
            </p>
        </div>

        <div class="flex justify-end">
            <button
                type="button"
                @click="submit"
                :disabled="foundationGeneratorForm.processing"
                class="px-5 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-md flex items-center gap-2 transition disabled:opacity-60 disabled:cursor-not-allowed"
            >
                <FontAwesomeIcon
                    v-if="foundationGeneratorForm.processing"
                    icon="spinner"
                    spin
                />
                <FontAwesomeIcon v-else icon="wand-magic-sparkles" />
                {{
                    foundationGeneratorForm.processing
                        ? "Generating..."
                        : "Generate Foundation"
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
