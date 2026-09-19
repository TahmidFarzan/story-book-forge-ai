<script setup>
import InfiniteScrollApiSelect from "@/components/common/multi-select/InfiniteScrollApiSelect.vue";

import { AiBrainOutputTypes } from "@/composables/useAiBrain";

import { computed, nextTick, reactive, ref, watch } from "vue";
import { useForm, router as inertiaRoute } from "@inertiajs/vue3";

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library as FontAwesomeLibrary } from "@fortawesome/fontawesome-svg-core";
import {
    faBrain,
    faCircleCheck,
    faCircleXmark,
    faClock,
    faImage,
    faSpinner,
    faWandMagicSparkles,
} from "@fortawesome/free-solid-svg-icons";

FontAwesomeLibrary.add(
    faBrain,
    faCircleCheck,
    faCircleXmark,
    faClock,
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

const pages = computed(() => storyBook?.pages ?? []);

const pageImages = computed(() => storyBook?.story_book_page_images ?? []);

const pageImageByNo = computed(() => {
    const map = new Map();

    for (const media of pageImages.value) {
        const no = Number(
            media?.custom_properties?.page_no ?? media?.order_column,
        );

        if (Number.isInteger(no) && no > 0) {
            map.set(no, media);
        }
    }

    return map;
});

function buildAiBrainSearchUrl() {
    return route("search.ai-brains", {
        ai_brain_output_type_code:AiBrainOutputTypes.Image,
    });
}

const illustrationGenerationForm = useForm({
    ai_brain_id: null,
});

const generatingNo = ref(null);
const generatingAll = ref(false);
const sequentialProgress = ref({
    index: 0,
    total: 0,
});

const failedPageNos = reactive(new Set());

const hasSelectedAiBrain = computed(
    () => !!illustrationGenerationForm.ai_brain_id,
);

const isBusy = computed(() => generatingNo.value !== null);

const generateEnabled = computed(
    () => isUpdate.value && hasSelectedAiBrain.value && !isBusy.value,
);

const generatedCount = computed(
    () => pages.value.filter((page) => pageImageByNo.value.has(page.no))
        .length,
);

const remainingPages = computed(() =>
    pages.value.filter(
        (page) => !pageImageByNo.value.has(page.no),
    ),
);

const allPagesGenerated = computed(
    () =>
        pages.value.length > 0 &&
        pages.value.every((page) => pageImageByNo.value.has(page.no)),
);

const pageImageUrl = (pageNo) => {
    const media = pageImageByNo.value.get(pageNo);

    return media?.original_url ?? media?.preview_url ?? null;
};

const pageState = (pageNo) => {
    if (generatingNo.value === pageNo) {
        return "generating";
    }

    if (failedPageNos.has(pageNo)) {
        return "failed";
    }

    if (pageImageByNo.value.has(pageNo)) {
        return "generated";
    }

    return "waiting";
};

const isPageGenerated = (pageNo) => pageImageByNo.value.has(pageNo);

const handlePageCompletion = (pageNo, onGenerated, onFailed) => {
    generatingNo.value = null;

    nextTick(() => {
        if (pageImageByNo.value.has(pageNo)) {
            failedPageNos.delete(pageNo);

            onGenerated?.();
        } else {
            failedPageNos.add(pageNo);
            onFailed?.();
        }
    });
};

const generateSinglePage = (page, { onGenerated, onFailed } = {}) => {
    if (!generateEnabled.value) {
        return;
    }

    if (generatingAll.value) {
        return;
    }

    failedPageNos.delete(page.no);
    generatingNo.value = page.no;
    illustrationGenerationForm.clearErrors();

    inertiaRoute.patch(
        route("back-office.story-books.regenerate.step14-3.illustration", {
            slug: storyBook?.slug,
        }),
        {
            ...illustrationGenerationForm.data(),
            page_no: page.no,
            _method: "patch",
        },
        {
            preserveScroll: true,
            preserveState: true,
            onFinish: () => {
                handlePageCompletion(
                    page.no,
                    () => onGenerated?.(),
                    () => onFailed?.(),
                );
            },
        },
    );
};

const generateAllRemaining = () => {
    if (!generateEnabled.value) {
        return;
    }

    const remaining = remainingPages.value;

    if (remaining.length === 0) {
        return;
    }

    generatingAll.value = true;
    sequentialProgress.value = {
        index: 1,
        total: remaining.length,
    };

    const runSequentially = (index) => {
        if (index >= remaining.length) {
            generatingAll.value = false;
            return;
        }

        const page = remaining[index];

        generateSinglePage(page, {
            onGenerated: () => {
                sequentialProgress.value = {
                    index: index + 2 > remaining.length
                        ? remaining.length
                        : index + 2,
                    total: remaining.length,
                };

                runSequentially(index + 1);
            },
            onFailed: () => {
                generatingAll.value = false;
            },
        });
    };

    runSequentially(0);
};

watch(
    allPagesGenerated,
    (generated) => {
        if (generated) {
            emit("completed");
        }
    },
    { immediate: true },
);
</script>

<template>
    <div class="space-y-6">
        <div class="bg-white border rounded-xl p-5 shadow-sm space-y-4">
            <div class="flex items-center gap-2">
                <FontAwesomeIcon
                    icon="wand-magic-sparkles"
                    class="text-purple-600"
                />
                <h3 class="text-base font-semibold">AI Brain Configuration</h3>
            </div>

            <p class="text-sm text-gray-500">
                Select the Image-output AI model that will generate one
                illustration for each story book page.
            </p>

            <div
                class="border-2 border-dashed border-purple-200 rounded-xl p-4 bg-gradient-to-br from-purple-50 to-blue-50"
            >
                <InfiniteScrollApiSelect
                    :form="illustrationGenerationForm"
                    fieldName="ai_brain_id"
                    :selectedItem="storyBook?.ai_brain"
                    :apiUrl="buildAiBrainSearchUrl()"
                    :multiple="false"
                    placeholder="Select Image AI Brain"
                    :error="
                        illustrationGenerationForm.errors.ai_brain_id
                    "
                    class="image-ai-brain-select"
                />
            </div>

            <p
                v-if="illustrationGenerationForm.errors.ai_brain_id"
                class="text-red-500 text-sm"
            >
                {{
                    illustrationGenerationForm.errors
                        .ai_brain_id
                }}
            </p>

            <p
                v-if="isUpdate && !hasSelectedAiBrain"
                class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-700"
            >
                Select an Image-output AI Brain before generating
                illustrations.
            </p>
        </div>

        <div
            v-if="isUpdate"
            class="bg-white border rounded-xl p-5 shadow-sm space-y-4"
        >
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <FontAwesomeIcon icon="image" class="text-blue-600" />
                    <h3 class="text-base font-semibold">
                        Page Illustrations
                    </h3>
                    <span
                        class="rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700"
                    >
                        {{ generatedCount }} / {{ pages.length }} generated
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <span
                        v-if="
                            generatingAll &&
                            generatingNo !== null
                        "
                        class="flex items-center gap-2 text-sm text-purple-700"
                    >
                        <FontAwesomeIcon icon="spinner" spin />
                        Generating page
                        {{ sequentialProgress.index }} of
                        {{ sequentialProgress.total }}
                    </span>

                    <button
                        type="button"
                        @click="generateAllRemaining"
                        :disabled="
                            !generateEnabled ||
                            remainingPages.length === 0 ||
                            generatingAll
                        "
                        class="flex items-center gap-2 rounded-md bg-purple-600 px-4 py-2 text-sm text-white transition hover:bg-purple-700 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <FontAwesomeIcon
                            v-if="generatingAll"
                            icon="spinner"
                            spin
                        />
                        <FontAwesomeIcon v-else icon="wand-magic-sparkles" />

                        {{
                            remainingPages.length === 0
                                ? "All Generated"
                                : generatingAll
                                  ? "Generating..."
                                  : `Generate All (${remainingPages.length})`
                        }}
                    </button>
                </div>
            </div>

            <ul class="space-y-3">
                <li
                    v-for="page in pages"
                    :key="page.no"
                    class="rounded-xl border border-gray-100 bg-gray-50 p-4"
                >
                    <div class="flex flex-col gap-4 md:flex-row">
                        <div
                            v-if="
                                pageImageUrl(page.no)
                            "
                            class="flex-shrink-0"
                        >
                            <img
                                :src="pageImageUrl(page.no)"
                                :alt="`Story Book Page ${page.no}`"
                                class="h-28 w-28 rounded-lg border border-gray-200 object-cover"
                            />
                        </div>

                        <div class="min-w-0 flex-1 space-y-2">
                            <div
                                class="flex flex-wrap items-center gap-2"
                            >
                                <span
                                    class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700"
                                >
                                    {{ page.no }}
                                </span>

                                <span
                                    class="flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium"
                                    :class="{
                                        'bg-green-100 text-green-700':
                                            pageState(page.no) ===
                                            'generated',
                                        'bg-amber-100 text-amber-700':
                                            pageState(page.no) ===
                                            'generating',
                                        'bg-red-100 text-red-700':
                                            pageState(page.no) === 'failed',
                                        'bg-gray-100 text-gray-600':
                                            pageState(page.no) ===
                                            'waiting',
                                    }"
                                >
                                    <FontAwesomeIcon
                                        v-if="
                                            pageState(page.no) ===
                                            'generated'
                                        "
                                        icon="circle-check"
                                        class="text-xs"
                                    />
                                    <FontAwesomeIcon
                                        v-else-if="
                                            pageState(page.no) ===
                                            'generating'
                                        "
                                        icon="spinner"
                                        spin
                                        class="text-xs"
                                    />
                                    <FontAwesomeIcon
                                        v-else-if="
                                            pageState(page.no) === 'failed'
                                        "
                                        icon="circle-xmark"
                                        class="text-xs"
                                    />
                                    <FontAwesomeIcon
                                        v-else
                                        icon="clock"
                                        class="text-xs"
                                    />

                                    {{
                                        pageState(page.no) === 'generated'
                                            ? "Generated"
                                            : pageState(page.no) ===
                                                'generating'
                                              ? "Generating"
                                              : pageState(page.no) ===
                                                  'failed'
                                                ? "Failed"
                                                : "Waiting"
                                    }}
                                </span>

                                <button
                                    v-if="
                                        isUpdate &&
                                        !isBusy &&
                                        pageState(page.no) === 'failed'
                                    "
                                    type="button"
                                    @click="generateSinglePage(page)"
                                    class="flex items-center gap-1 rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 transition hover:bg-red-100"
                                >
                                    <FontAwesomeIcon
                                        icon="wand-magic-sparkles"
                                        class="text-xs"
                                    />
                                    Retry
                                </button>
                            </div>

                            <p class="text-sm text-gray-700">
                                {{ page.narration || "Narration pending." }}
                            </p>

                            <p
                                class="rounded-lg border border-purple-100 bg-purple-50 p-2 text-xs text-purple-800"
                            >
                                <span class="font-medium">
                                    Illustration Prompt:
                                </span>
                                {{
                                    page.illustration_prompt ||
                                    "Illustration prompt pending."
                                }}
                            </p>

                            <div class="flex justify-end">
                                <button
                                    v-if="
                                        isUpdate &&
                                        pageState(page.no) !== 'generating'
                                    "
                                    type="button"
                                    @click="generateSinglePage(page)"
                                    :disabled="
                                        !generateEnabled || generatingAll
                                    "
                                    class="flex items-center gap-2 rounded-md bg-blue-600 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                                >
                                    <FontAwesomeIcon
                                        icon="wand-magic-sparkles"
                                        class="text-xs"
                                    />
                                    {{
                                        pageState(page.no) === 'generated'
                                            ? "Regenerate"
                                            : "Generate"
                                    }}
                                </button>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <div
            v-else
            class="bg-white border rounded-xl p-5 shadow-sm space-y-4"
        >
            <div class="flex items-center gap-2">
                <FontAwesomeIcon icon="image" class="text-blue-600" />
                <h3 class="text-base font-semibold">Page Illustrations</h3>
            </div>

            <p class="text-sm text-gray-500">
                Save the story book foundation first, then generate one
                illustration per page.
            </p>
        </div>
    </div>
</template>

<style scoped>
.image-ai-brain-select :deep(.multiselect) {
    min-height: 44px;
}

.image-ai-brain-select :deep(.multiselect__tags) {
    min-height: 44px;
    padding: 8px 40px 0 12px;
    border-color: #a78bfa;
    border-width: 1px;
    border-radius: 0.5rem;
    background: white;
}

.image-ai-brain-select :deep(.multiselect__single) {
    padding: 4px 0 0 0;
    margin-bottom: 0;
    color: #1f2937;
}

.image-ai-brain-select :deep(.multiselect__placeholder) {
    padding: 4px 0 0 0;
    color: #9ca3af;
}

.image-ai-brain-select :deep(.multiselect__select) {
    height: 44px;
}

.image-ai-brain-select :deep(.multiselect__content-wrapper) {
    border-color: #a78bfa;
}
</style>
