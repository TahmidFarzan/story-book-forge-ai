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

const storyBookPagePlanForm = useForm({
    page_plan: storyBook?.page_plan
        ? typeof storyBook.page_plan === "string"
            ? storyBook.page_plan
            : JSON.stringify(storyBook.page_plan, null, 2)
        : "",
    ai_brain_id: storyBook?.ai_brain_id ?? null,
});

const validate = () => {
    storyBookPagePlanForm.clearErrors();

    let valid = true;

    if (!storyBookPagePlanForm.ai_brain_id) {
        storyBookPagePlanForm.setError(
            "ai_brain_id",
            "AI Brain selection is required",
        );
        valid = false;
    }

    return valid;
};

const handleSuccess = () => {
    storyBookPagePlanForm.clearErrors();
    emit("completed", storyBook);
};

const submit = () => {
    if (storyBookPagePlanForm.processing || !validate()) {
        return;
    }

    const requestConfig = {
        preserveScroll: true,
        preserveState: true,
        onSuccess: handleSuccess,
    };

    if (isUpdate.value) {
        inertiaRoute.patch(
            route("back-office.story-books.regenerate.page-plan", {
                slug: storyBook?.slug,
            }),
            { ...storyBookPagePlanForm.data(), _method: "patch" },
            requestConfig,
        );
    } else {
        storyBookPagePlanForm.post(
            route("back-office.story-books.generate.page-plan"),
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
                    >Page Plan</label
                >
                <textarea
                    v-model="storyBookPagePlanForm.page_plan"
                    rows="10"
                    placeholder="Enter page plan details..."
                    class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none border-gray-300"
                ></textarea>
                <p
                    v-if="storyBookPagePlanForm.errors.page_plan"
                    class="text-red-500 text-sm mt-1"
                >
                    {{ storyBookPagePlanForm.errors.page_plan }}
                </p>
            </div>
        </div>

        <div class="bg-white border rounded-xl p-5 shadow-sm space-y-4">
            <div class="flex items-center gap-2">
                <FontAwesomeIcon icon="brain" class="text-purple-600" />
                <h3 class="text-base font-semibold">AI Brain Configuration</h3>
            </div>
            <p class="text-sm text-gray-500">
                Select the AI model that will generate the page plan.
            </p>
            <div
                class="border-2 border-dashed border-purple-200 rounded-xl p-4 bg-gradient-to-br from-purple-50 to-blue-50"
            >
                <InfiniteScrollApiSelect
                    :form="storyBookPagePlanForm"
                    fieldName="ai_brain_id"
                    :selectedItem="storyBook?.ai_brain"
                    :apiUrl="route('search.ai-brains')"
                    :multiple="false"
                    placeholder="Select AI Brain"
                    :error="storyBookPagePlanForm.errors.ai_brain_id"
                    class="ai-brain-select"
                />
            </div>
            <p
                v-if="storyBookPagePlanForm.errors.ai_brain_id"
                class="text-red-500 text-sm"
            >
                {{ storyBookPagePlanForm.errors.ai_brain_id }}
            </p>
        </div>

        <div class="flex justify-end">
            <button
                type="button"
                @click="submit"
                :disabled="storyBookPagePlanForm.processing"
                class="px-5 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-md flex items-center gap-2 transition disabled:opacity-60 disabled:cursor-not-allowed"
            >
                <FontAwesomeIcon
                    v-if="storyBookPagePlanForm.processing"
                    icon="spinner"
                    spin
                />
                <FontAwesomeIcon
                    v-else
                    icon="wand-magic-sparkles"
                />
                {{
                    storyBookPagePlanForm.processing
                        ? "Generating..."
                        : "Generate Page Plan"
                }}
            </button>
        </div>
    </div>
</template>