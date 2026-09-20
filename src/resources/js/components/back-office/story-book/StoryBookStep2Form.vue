<script setup>
import InfiniteScrollApiSelect from "@/components/common/multi-select/InfiniteScrollApiSelect.vue";
import { AiBrainOutputTypes } from "@/composables/useAiBrain";

import { computed } from "vue";
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
        required: true,
    },
});

const isUpdate = computed(() => !!storyBook?.id);

const charactersGeneratorForm = useForm({
    foundation: storyBook?.foundation
        ? typeof storyBook.foundation === "string"
            ? storyBook.foundation
            : JSON.stringify(storyBook.foundation)
        : null,
    ai_brain_id: null,
});

function buildAiBrainSearchUrl() {
    return route("search.ai-brains", {
        ai_brain_output_type_code: AiBrainOutputTypes.Text,
    });
}

const validate = () => {
    charactersGeneratorForm.clearErrors();

    let valid = true;

    if (!charactersGeneratorForm.ai_brain_id) {
        charactersGeneratorForm.setError(
            "ai_brain_id",
            "AI Brain selection is required",
        );
        valid = false;
    }

    if (!charactersGeneratorForm.foundation) {
        charactersGeneratorForm.setError("foundation", "Foundation is required");
        valid = false;
    }

    return valid;
};

const handleSuccess = () => {
    charactersGeneratorForm.clearErrors();
    emit("completed", storyBook);
};

const submit = () => {
    if (!isUpdate.value || charactersGeneratorForm.processing || !validate()) {
        return;
    }

    const requestConfig = {
        preserveScroll: true,
        preserveState: true,
        onSuccess: handleSuccess,
    };

    inertiaRoute.patch(
        route("back-office.story-books.regenerate.step2-characters", {
            slug: storyBook?.slug,
        }),
        {
            ...charactersGeneratorForm.data(),
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
                <p v-if="charactersGeneratorForm.errors.foundation">
                    {{ charactersGeneratorForm.errors.foundation }}
                </p>
            </div>
        </div>

        <div class="bg-white border rounded-xl p-5 shadow-sm space-y-4">
            <div class="flex items-center gap-2">
                <FontAwesomeIcon icon="brain" class="text-purple-600" />
                <h3 class="text-base font-semibold">AI Brain Configuration</h3>
            </div>

            <p class="text-sm text-gray-500">
                Select the AI model that will generate the characters.
            </p>

            <div
                class="border-2 border-dashed border-purple-200 rounded-xl p-4 bg-gradient-to-br from-purple-50 to-blue-50"
            >
                <InfiniteScrollApiSelect
                    :form="charactersGeneratorForm"
                    fieldName="ai_brain_id"
                    :selectedItem="storyBook?.ai_brain"
                    :apiUrl="buildAiBrainSearchUrl()"
                    :multiple="false"
                    placeholder="Select AI Brain"
                    :error="charactersGeneratorForm.errors.ai_brain_id"
                    class="ai-brain-select"
                />
            </div>

            <p
                v-if="charactersGeneratorForm.errors.ai_brain_id"
                class="text-red-500 text-sm"
            >
                {{ charactersGeneratorForm.errors.ai_brain_id }}
            </p>
        </div>

        <div class="flex justify-end">
            <button
                type="button"
                @click="submit"
                :disabled="!isUpdate || charactersGeneratorForm.processing"
                class="px-5 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-md flex items-center gap-2 transition disabled:opacity-60 disabled:cursor-not-allowed"
            >
                <FontAwesomeIcon
                    v-if="charactersGeneratorForm.processing"
                    icon="spinner"
                    spin
                />

                <FontAwesomeIcon v-else icon="wand-magic-sparkles" />

                {{
                    charactersGeneratorForm.processing
                        ? "Generating..."
                        : "Generate Characters"
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
