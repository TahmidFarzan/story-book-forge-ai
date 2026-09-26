<script setup>
import InfiniteScrollApiSelect from "@/components/common/multi-select/InfiniteScrollApiSelect.vue";
import { AiBrainOutputTypes } from "@/composables/useAiBrain";

import { computed, watch } from "vue";
import { router as inertiaRoute, useForm } from "@inertiajs/vue3";

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library as FontAwesomeLibrary } from "@fortawesome/fontawesome-svg-core";
import {
    faBrain,
    faImage,
    faSpinner,
    faWandMagicSparkles,
} from "@fortawesome/free-solid-svg-icons";

FontAwesomeLibrary.add(faBrain, faImage, faSpinner, faWandMagicSparkles);

const props = defineProps({
    storyBook: {
        type: Object,
        default: null,
    },
    isUpdate: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const isLocked = computed(() => !!props.storyBook?.is_locked);

const setupForm = useForm({
    additional_information:
        props.storyBook?.additional_information ?? "",
    language_id: props.storyBook?.language_id ?? null,
    genre_ids: props.storyBook?.genres?.map((genre) => genre.id) ?? [],
    story_book_type_id: props.storyBook?.story_book_type_id ?? null,
    audience_id: props.storyBook?.audience_id ?? null,
    illustration_type_id: props.storyBook?.illustration_type_id ?? null,
    ai_brain_text_id: props.storyBook?.ai_brain_text_id ?? null,
});

const genresApiUrl = computed(() => {
    if (!setupForm.audience_id) {
        return route("search.genres");
    }

    return `${route("search.genres")}?audience_id=${encodeURIComponent(
        setupForm.audience_id,
    )}`;
});

const textAiBrainApiUrl = computed(() =>
    route("search.ai-brains", {
        ai_brain_output_type_code: AiBrainOutputTypes.Text,
    }),
);

const isBusy = computed(
    () => setupForm.processing || props.disabled || isLocked.value,
);

watch(
    () => setupForm.audience_id,
    (newAudienceId, oldAudienceId) => {
        if (newAudienceId === oldAudienceId) {
            return;
        }

        if (props.storyBook?.id) {
            return;
        }

        setupForm.language_id = null;
        setupForm.genre_ids = [];
        setupForm.story_book_type_id = null;
        setupForm.additional_information = "";

        setupForm.clearErrors(
            "language_id",
            "genre_ids",
            "story_book_type_id",
            "additional_information",
        );
    },
);

const validate = () => {
    setupForm.clearErrors();

    let valid = true;

    const requiredFields = [
        ["language_id", "Language is required"],
        ["story_book_type_id", "Story Book type is required"],
        ["audience_id", "Audience is required"],
        ["illustration_type_id", "Illustration type is required"],
        ["ai_brain_text_id", "Text AI Brain selection is required"],
    ];

    requiredFields.forEach(([field, message]) => {
        if (!setupForm[field]) {
            setupForm.setError(field, message);
            valid = false;
        }
    });

    if (!Array.isArray(setupForm.genre_ids) || !setupForm.genre_ids.length) {
        setupForm.setError("genre_ids", "Genres is required");
        valid = false;
    }

    return valid;
};

const submit = () => {
    if (isBusy.value || !validate()) {
        return;
    }

    if (props.isUpdate) {
        inertiaRoute.patch(
            route("back-office.story-books.save", {
                slug: props.storyBook?.slug,
            }),
            { ...setupForm.data(), _method: "patch" },
            {
                preserveScroll: true,
                preserveState: true,
            },
        );

        return;
    }

    setupForm.post(
        route("back-office.story-books.create.text-generation"),
        {
            preserveScroll: true,
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
                <h3 class="text-base font-semibold">Story Book Setup</h3>
            </div>

            <p class="text-sm text-gray-500">
                These details drive every text generation step. The AI
                foundation is generated first, and the story book is only saved
                once that call succeeds.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1"
                        >Audience <span class="text-red-500">*</span></label
                    >
                    <InfiniteScrollApiSelect
                        :form="setupForm"
                        fieldName="audience_id"
                        :selectedItem="storyBook?.audience"
                        :apiUrl="route('search.audiences')"
                        :multiple="false"
                        placeholder="Select audience"
                    />
                    <p
                        v-if="setupForm.errors.audience_id"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ setupForm.errors.audience_id }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1"
                        >Language <span class="text-red-500">*</span></label
                    >
                    <InfiniteScrollApiSelect
                        :form="setupForm"
                        fieldName="language_id"
                        :selectedItem="storyBook?.language"
                        :apiUrl="route('search.languages')"
                        :multiple="false"
                        placeholder="Select language"
                    />
                    <p
                        v-if="setupForm.errors.language_id"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ setupForm.errors.language_id }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1"
                        >Genres <span class="text-red-500">*</span></label
                    >
                    <InfiniteScrollApiSelect
                        :form="setupForm"
                        fieldName="genre_ids"
                        :selectedItem="storyBook?.genres"
                        :apiUrl="genresApiUrl"
                        :multiple="true"
                        placeholder="Select genres"
                    />
                    <p
                        v-if="setupForm.errors.genre_ids"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ setupForm.errors.genre_ids }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1"
                        >Story Book Type
                        <span class="text-red-500">*</span></label
                    >
                    <InfiniteScrollApiSelect
                        :form="setupForm"
                        fieldName="story_book_type_id"
                        :selectedItem="storyBook?.story_book_type"
                        :apiUrl="route('search.story-book-types')"
                        :multiple="false"
                        placeholder="Select story book type"
                    />
                    <p
                        v-if="setupForm.errors.story_book_type_id"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ setupForm.errors.story_book_type_id }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1"
                        >Illustration Type
                        <span class="text-red-500">*</span></label
                    >
                    <InfiniteScrollApiSelect
                        :form="setupForm"
                        fieldName="illustration_type_id"
                        :selectedItem="storyBook?.illustration_type"
                        :apiUrl="route('search.illustration-types')"
                        :multiple="false"
                        placeholder="Select illustration type"
                    />
                    <p
                        v-if="setupForm.errors.illustration_type_id"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ setupForm.errors.illustration_type_id }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1"
                        >Text AI Brain <span class="text-red-500">*</span></label
                    >
                    <InfiniteScrollApiSelect
                        :form="setupForm"
                        fieldName="ai_brain_text_id"
                        :selectedItem="storyBook?.ai_brain_text"
                        :apiUrl="textAiBrainApiUrl"
                        :multiple="false"
                        placeholder="Select Text AI Brain"
                    />
                    <p
                        v-if="setupForm.errors.ai_brain_text_id"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ setupForm.errors.ai_brain_text_id }}
                    </p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1"
                        >Additional Information</label
                    >
                    <textarea
                        v-model="setupForm.additional_information"
                        rows="3"
                        placeholder="Any additional context or instructions for the AI..."
                        class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none border-gray-300"
                    ></textarea>
                </div>
            </div>
        </div>

        <div
            v-if="storyBook?.illustration_type"
            class="bg-white border rounded-xl p-5 shadow-sm space-y-2"
        >
            <div class="flex items-center gap-2">
                <FontAwesomeIcon icon="image" class="text-blue-600" />
                <h3 class="text-base font-semibold">Illustration Type</h3>
            </div>
            <p class="text-sm text-gray-500">
                {{ storyBook?.illustration_type?.name }}
            </p>
            <p
                v-if="storyBook?.illustration_type?.prompt_instruction"
                class="rounded-lg border border-blue-100 bg-blue-50 p-2 text-xs text-blue-800"
            >
                <span class="font-medium">Style Instruction:</span>
                {{ storyBook?.illustration_type?.prompt_instruction }}
            </p>
        </div>

        <div
            v-if="isLocked"
            class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700"
        >
            This story book is complete and can no longer be updated.
        </div>

        <div class="flex justify-end">
            <button
                type="button"
                @click="submit"
                :disabled="isBusy"
                class="px-5 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-md flex items-center gap-2 transition disabled:opacity-60 disabled:cursor-not-allowed"
            >
                <FontAwesomeIcon
                    v-if="setupForm.processing"
                    icon="spinner"
                    spin
                />
                <FontAwesomeIcon v-else icon="brain" />

                {{
                    setupForm.processing
                        ? isUpdate
                            ? "Saving..."
                            : "Generating..."
                        : isUpdate
                          ? "Save Changes"
                          : "Generate Story Book"
                }}
            </button>
        </div>
    </div>
</template>
