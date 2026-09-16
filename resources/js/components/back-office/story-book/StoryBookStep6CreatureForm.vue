<script setup>
import InfiniteScrollApiSelect from "@/components/common/multi-select/InfiniteScrollApiSelect.vue";

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
        default: null,
    },
});

const isUpdate = computed(() => !!storyBook?.id);

const storyBookCreatureForm = useForm({
    creatures: storyBook?.creatures
        ? typeof storyBook.creatures === "string"
            ? storyBook.creatures
            : JSON.stringify(storyBook.creatures, null, 2)
        : "",
    ai_brain_id: storyBook?.ai_brain_id ?? null,
});

const validate = () => {
    storyBookCreatureForm.clearErrors();

    let valid = true;

    if (!storyBookCreatureForm.ai_brain_id) {
        storyBookCreatureForm.setError(
            "ai_brain_id",
            "AI Brain selection is required",
        );
        valid = false;
    }

    return valid;
};

const handleSuccess = () => {
    storyBookCreatureForm.clearErrors();
    emit("completed", storyBook);
};

const submit = () => {
    if (storyBookCreatureForm.processing || !validate()) {
        return;
    }

    const requestConfig = {
        preserveScroll: true,
        preserveState: true,
        onSuccess: handleSuccess,
    };

    if (isUpdate.value) {
        inertiaRoute.patch(
            route("back-office.story-books.regenerate.creature", {
                slug: storyBook?.slug,
            }),
            { ...storyBookCreatureForm.data(), _method: "patch" },
            requestConfig,
        );
    } else {
        storyBookCreatureForm.post(
            route("back-office.story-books.generate.creature"),
            requestConfig,
        );
    }
};
</script>

<template>
    <div class="space-y-6">
        <div class="bg-white border rounded-xl p-5 shadow-sm space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1"
                    >Creature</label
                >
                <textarea
                    v-model="storyBookCreatureForm.creatures"
                    rows="10"
                    placeholder="Enter creature details..."
                    class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none border-gray-300"
                ></textarea>
                <p
                    v-if="storyBookCreatureForm.errors.creatures"
                    class="text-red-500 text-sm mt-1"
                >
                    {{ storyBookCreatureForm.errors.creatures }}
                </p>
            </div>
        </div>

        <div class="bg-white border rounded-xl p-5 shadow-sm space-y-4">
            <div class="flex items-center gap-2">
                <FontAwesomeIcon icon="brain" class="text-purple-600" />
                <h3 class="text-base font-semibold">AI Brain Configuration</h3>
            </div>
            <p class="text-sm text-gray-500">
                Select the AI model that will generate the creatures.
            </p>
            <div
                class="border-2 border-dashed border-purple-200 rounded-xl p-4 bg-gradient-to-br from-purple-50 to-blue-50"
            >
                <InfiniteScrollApiSelect
                    :form="storyBookCreatureForm"
                    fieldName="ai_brain_id"
                    :selectedItem="storyBook?.ai_brain"
                    :apiUrl="route('search.ai-brains')"
                    :multiple="false"
                    placeholder="Select AI Brain"
                    :error="storyBookCreatureForm.errors.ai_brain_id"
                    class="ai-brain-select"
                />
            </div>
            <p
                v-if="storyBookCreatureForm.errors.ai_brain_id"
                class="text-red-500 text-sm"
            >
                {{ storyBookCreatureForm.errors.ai_brain_id }}
            </p>
        </div>

        <div class="flex justify-end">
            <button
                type="button"
                @click="submit"
                :disabled="storyBookCreatureForm.processing"
                class="px-5 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-md flex items-center gap-2 transition disabled:opacity-60 disabled:cursor-not-allowed"
            >
                <FontAwesomeIcon
                    v-if="storyBookCreatureForm.processing"
                    icon="spinner"
                    spin
                />
                <FontAwesomeIcon
                    v-else
                    icon="wand-magic-sparkles"
                />
                {{
                    storyBookCreatureForm.processing
                        ? "Generating..."
                        : "Generate Creature"
                }}
            </button>
        </div>
    </div>
</template>