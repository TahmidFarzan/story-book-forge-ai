<script setup>
import Layout from "@/pages/layouts/AuthLayout.vue";

import { computed, onMounted, nextTick } from "vue";
import { Head, useForm, router as intertiaJsRoute } from "@inertiajs/vue3";

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library as FontAwesomeLibrary } from "@fortawesome/fontawesome-svg-core";
import { faSave, faSpinner } from "@fortawesome/free-solid-svg-icons";

FontAwesomeLibrary.add(faSave, faSpinner);

defineOptions({ layout: Layout });

const { aiBrain } = defineProps({
    aiBrain: Object,
});

const isUpdate = computed(() => !!aiBrain?.slug);

const pageTitle = computed(() => {
    return isUpdate.value ? `Edit ${aiBrain?.name}` : "Create Ai Brain";
});

const saveForm = useForm({
    name: aiBrain?.name || null,
    model: aiBrain?.model || null,
    api_url: aiBrain?.api_url || null,
    api_key: aiBrain?.api_key || null,
    brief: aiBrain?.brief || null,
    focus: aiBrain?.focus || null,
    context_window: aiBrain?.context_window,
    average_latency: aiBrain?.average_latency,
    minimum_wait_time: aiBrain?.minimum_wait_time,
    timeout_seconds: aiBrain?.timeout_seconds,
    max_output_tokens: aiBrain?.max_output_tokens,
});

function validateForm() {
    saveForm.clearErrors();

    let valid = true;

    if (!saveForm.name) {
        saveForm.setError("name", "Name is required");
        valid = false;
    }

    if (!saveForm.model) {
        saveForm.setError("model", "Model is required");
        valid = false;
    }

    if (!saveForm.api_url) {
        saveForm.setError("api_url", "API URL is required");
        valid = false;
    }

    if (!saveForm.api_key) {
        saveForm.setError("api_key", "API key is required");
        valid = false;
    }

    if (
        saveForm.average_latency === null ||
        saveForm.average_latency === undefined ||
        saveForm.average_latency === ""
    ) {
        saveForm.setError("average_latency", "Average latency is required.");

        valid = false;
    }

    if (
        saveForm.minimum_wait_time === null ||
        saveForm.minimum_wait_time === undefined ||
        saveForm.minimum_wait_time === ""
    ) {
        saveForm.setError(
            "minimum_wait_time",
            "Minimum wait time is required.",
        );

        valid = false;
    }

    if (
        saveForm.timeout_seconds === null ||
        saveForm.timeout_seconds === undefined ||
        saveForm.timeout_seconds === ""
    ) {
        saveForm.setError("timeout_seconds", "Timeout seconds is required.");

        valid = false;
    }
    return valid;
}

function handleSave() {
    if (saveForm.processing) return;

    if (!validateForm()) return;

    saveForm.processing = true;

    const requestConfig = {
        preserveScroll: true,
        preserveState: true,
        forceFormData: true,
        onSuccess: () => {
            saveForm.reset();
            saveForm.clearErrors();
        },
        onError: (errors) => {
            saveForm.clearErrors();
            saveForm.setError(errors);
        },
        onFinish: () => {
            saveForm.processing = false;
        },
    };

    if (isUpdate.value) {
        intertiaJsRoute.post(
            route("back-office.ai-brains.update", { slug: aiBrain?.slug }),
            { ...saveForm.data(), _method: "patch" },
            requestConfig,
        );
    } else {
        saveForm.post(route("back-office.ai-brains.save"), requestConfig);
    }
}

onMounted(async () => {
    await nextTick();

    window.dispatchEvent(
        new CustomEvent("set-breadcrumb", {
            detail: [
                {
                    text: "Ai Brains",
                    href: route("back-office.ai-brains.index"),
                },
                { text: pageTitle.value, active: true },
            ],
        }),
    );
});
</script>

<template>
    <Head :title="pageTitle" />

    <div class="w-full">
        <div
            class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 md:p-6"
        >
            <form @submit.prevent="handleSave" class="space-y-6">
                <div class="bg-white border rounded-xl p-5 shadow-sm space-y-4">
                    <h3 class="text-base font-semibold">Basic Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Name <span class="text-red-500">*</span>
                            </label>

                            <input
                                v-model="saveForm.name"
                                placeholder="Enter brain name"
                                class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                :class="
                                    saveForm.errors.name
                                        ? 'border-red-500'
                                        : 'border-gray-300'
                                "
                            />

                            <p
                                v-if="saveForm.errors.name"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ saveForm.errors.name }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Model <span class="text-red-500">*</span>
                            </label>

                            <input
                                v-model="saveForm.model"
                                placeholder="Enter brain model"
                                class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                :class="
                                    saveForm.errors.model
                                        ? 'border-red-500'
                                        : 'border-gray-300'
                                "
                            />

                            <p
                                v-if="saveForm.errors.model"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ saveForm.errors.model }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">
                                API URL <span class="text-red-500">*</span>
                            </label>

                            <input
                                v-model="saveForm.api_url"
                                placeholder="https://api.example.com/v1"
                                class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                :class="
                                    saveForm.errors.api_url
                                        ? 'border-red-500'
                                        : 'border-gray-300'
                                "
                            />

                            <p
                                v-if="saveForm.errors.api_url"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ saveForm.errors.api_url }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">
                                API Key <span class="text-red-500">*</span>
                            </label>

                            <input
                                v-model="saveForm.api_key"
                                type="password"
                                placeholder="Enter API key"
                                class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                :class="
                                    saveForm.errors.api_key
                                        ? 'border-red-500'
                                        : 'border-gray-300'
                                "
                            />

                            <p
                                v-if="saveForm.errors.api_key"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ saveForm.errors.api_key }}
                            </p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1">
                                Focus
                            </label>

                            <textarea
                                v-model="saveForm.focus"
                                rows="4"
                                placeholder="e.g. Creative writing, code generation"
                                class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                :class="
                                    saveForm.errors.focus
                                        ? 'border-red-500'
                                        : 'border-gray-300'
                                "
                            ></textarea>

                            <p
                                v-if="saveForm.errors.focus"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ saveForm.errors.focus }}
                            </p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1">
                                Brief
                            </label>

                            <textarea
                                v-model="saveForm.brief"
                                rows="4"
                                placeholder="Enter brief description"
                                class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                :class="
                                    saveForm.errors.brief
                                        ? 'border-red-500'
                                        : 'border-gray-300'
                                "
                            ></textarea>

                            <p
                                v-if="saveForm.errors.brief"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ saveForm.errors.brief }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border rounded-xl p-5 shadow-sm space-y-4">
                    <h3 class="text-base font-semibold">Configuration</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Context Window
                            </label>

                            <input
                                v-model="saveForm.context_window"
                                type="number"
                                step="1"
                                min="1"
                                placeholder="e.g. 32768"
                                class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                :class="
                                    saveForm.errors.context_window
                                        ? 'border-red-500'
                                        : 'border-gray-300'
                                "
                            />

                            <p
                                v-if="saveForm.errors.context_window"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ saveForm.errors.context_window }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Average Latency (sec)
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                v-model="saveForm.average_latency"
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="e.g. 0.90"
                                class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                :class="
                                    saveForm.errors.average_latency
                                        ? 'border-red-500'
                                        : 'border-gray-300'
                                "
                            />

                            <p
                                v-if="saveForm.errors.average_latency"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ saveForm.errors.average_latency }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Max Output Tokens
                            </label>

                            <input
                                v-model="saveForm.max_output_tokens"
                                type="number"
                                step="1"
                                min="1"
                                placeholder="e.g. 4096"
                                class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                :class="
                                    saveForm.errors.max_output_tokens
                                        ? 'border-red-500'
                                        : 'border-gray-300'
                                "
                            />

                            <p
                                v-if="saveForm.errors.max_output_tokens"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ saveForm.errors.max_output_tokens }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Minimum Wait Time (sec)
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                v-model="saveForm.minimum_wait_time"
                                type="number"
                                step="1"
                                min="0"
                                placeholder="e.g. 2"
                                class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                :class="
                                    saveForm.errors.minimum_wait_time
                                        ? 'border-red-500'
                                        : 'border-gray-300'
                                "
                            />

                            <p
                                v-if="saveForm.errors.minimum_wait_time"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ saveForm.errors.minimum_wait_time }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Timeout Seconds
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                v-model="saveForm.timeout_seconds"
                                type="number"
                                step="1"
                                min="1"
                                placeholder="e.g. 60"
                                class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                :class="
                                    saveForm.errors.timeout_seconds
                                        ? 'border-red-500'
                                        : 'border-gray-300'
                                "
                            />

                            <p
                                v-if="saveForm.errors.timeout_seconds"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ saveForm.errors.timeout_seconds }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center">
                    <button
                        type="submit"
                        :disabled="saveForm.processing"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md flex items-center gap-2 transition disabled:opacity-60 disabled:cursor-not-allowed"
                    >
                        <FontAwesomeIcon
                            v-if="saveForm.processing"
                            icon="spinner"
                            spin
                        />
                        <FontAwesomeIcon v-else icon="save" />
                        {{ saveForm.processing ? "Saving..." : "Save" }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
