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

const storyBookScenePlanForm = useForm({
    scene_plans: storyBook?.scene_plans
        ? typeof storyBook.scene_plans === "string"
            ? storyBook.scene_plans
            : JSON.stringify(storyBook.scene_plans, null, 2)
        : "",
    ai_brain_id: storyBook?.ai_brain_id ?? null,
});

const validate = () => {
    storyBookScenePlanForm.clearErrors();

    let valid = true;

    if (!storyBookScenePlanForm.ai_brain_id) {
        storyBookScenePlanForm.setError(
            "ai_brain_id",
            "AI Brain selection is required",
        );
        valid = false;
    }

    return valid;
};

const handleSuccess = () => {
    storyBookScenePlanForm.clearErrors();
    emit("completed", storyBook);
};

const submit = () => {
    if (storyBookScenePlanForm.processing || !validate()) {
        return;
    }

    const requestConfig = {
        preserveScroll: true,
        preserveState: true,
        onSuccess: handleSuccess,
    };

    if (isUpdate.value) {
        inertiaRoute.patch(
            route("back-office.story-books.regenerate.scene-plan", {
                slug: storyBook?.slug,
            }),
            { ...storyBookScenePlanForm.data(), _method: "patch" },
            requestConfig,
        );
    } else {
        storyBookScenePlanForm.post(
            route("back-office.story-books.generate.scene-plan"),
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
                    >Scene Plan</label
                >
                <textarea
                    v-model="storyBookScenePlanForm.scene_plans"
                    rows="10"
                    placeholder="Enter scene plan details..."
                    class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none border-gray-300"
                ></textarea>
                <p
                    v-if="storyBookScenePlanForm.errors.scene_plans"
                    class="text-red-500 text-sm mt-1"
                >
                    {{ storyBookScenePlanForm.errors.scene_plans }}
                </p>
            </div>
        </div>

        <div class="bg-white border rounded-xl p-5 shadow-sm space-y-4">
            <div class="flex items-center gap-2">
                <FontAwesomeIcon icon="brain" class="text-purple-600" />
                <h3 class="text-base font-semibold">AI Brain Configuration</h3>
            </div>
            <p class="text-sm text-gray-500">
                Select the AI model that will generate the scene plans.
            </p>
            <div
                class="border-2 border-dashed border-purple-200 rounded-xl p-4 bg-gradient-to-br from-purple-50 to-blue-50"
            >
                <InfiniteScrollApiSelect
                    :form="storyBookScenePlanForm"
                    fieldName="ai_brain_id"
                    :selectedItem="storyBook?.ai_brain"
                    :apiUrl="route('search.ai-brains')"
                    :multiple="false"
                    placeholder="Select AI Brain"
                    :error="storyBookScenePlanForm.errors.ai_brain_id"
                    class="ai-brain-select"
                />
            </div>
            <p
                v-if="storyBookScenePlanForm.errors.ai_brain_id"
                class="text-red-500 text-sm"
            >
                {{ storyBookScenePlanForm.errors.ai_brain_id }}
            </p>
        </div>

        <div class="flex justify-end">
            <button
                type="button"
                @click="submit"
                :disabled="storyBookScenePlanForm.processing"
                class="px-5 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-md flex items-center gap-2 transition disabled:opacity-60 disabled:cursor-not-allowed"
            >
                <FontAwesomeIcon
                    v-if="storyBookScenePlanForm.processing"
                    icon="spinner"
                    spin
                />
                <FontAwesomeIcon
                    v-else
                    icon="wand-magic-sparkles"
                />
                {{
                    storyBookScenePlanForm.processing
                        ? "Generating..."
                        : "Generate Scene Plan"
                }}
            </button>
        </div>
    </div>
</template>