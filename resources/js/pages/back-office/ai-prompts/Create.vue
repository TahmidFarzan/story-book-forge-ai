<script setup>
import Layout from '@/pages/layouts/AuthLayout.vue'

import { computed, onMounted, nextTick } from 'vue'
import { Head, useForm, router as intertiaJsRoute } from '@inertiajs/vue3'

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome"
import { library as FontAwesomeLibrary } from '@fortawesome/fontawesome-svg-core'
import { faSave, faSpinner } from '@fortawesome/free-solid-svg-icons'

FontAwesomeLibrary.add(faSave, faSpinner)

defineOptions({ layout: Layout })

const { aiPrompt } = defineProps({
    aiPrompt: Object,
})

const isUpdate = computed(() => !!aiPrompt?.slug)

const pageTitle = computed(() => {
    return isUpdate.value
        ? `Edit ${aiPrompt?.name}`
        : 'Create Ai Prompt'
})

const saveForm = useForm({
    name: aiPrompt?.name || null,
    prompt: aiPrompt?.prompt || null,
})

function validateForm() {
    saveForm.clearErrors()

    let valid = true

    if (!saveForm.name) {
        saveForm.setError('name', 'Name is required')
        valid = false
    }

    if (!saveForm.prompt) {
        saveForm.setError('prompt', 'Prompt is required')
        valid = false
    }

    return valid
}

function handleSave() {
    if (saveForm.processing) return

    if (!validateForm()) return

    saveForm.processing = true

    const requestConfig = {
        preserveScroll: true,
        preserveState: true,
        forceFormData: true,
        onSuccess: () => {
            saveForm.reset()
            saveForm.clearErrors()
        },
        onError: (errors) => {
            saveForm.clearErrors()
            saveForm.setError(errors)
        },
        onFinish: () => {
            saveForm.processing = false
        }
    }

    if (isUpdate.value) {
        const data = { ...saveForm.data(), _method: 'patch' }
        if (isUpdate.value) {
            delete data.name
        }
        intertiaJsRoute.post(
            route('back-office.ai-prompts.update', { slug: aiPrompt?.slug }),
            data,
            requestConfig
        )
    } else {
        saveForm.post(route('back-office.ai-prompts.save'), requestConfig)
    }
}

onMounted(async () => {
    await nextTick()

    window.dispatchEvent(
        new CustomEvent('set-breadcrumb', {
            detail: [
                { text: 'Ai Prompts', href: route('back-office.ai-prompts.index') },
                { text: pageTitle.value, active: true }
            ],
        })
    )
})
</script>

<template>
    <Head :title="pageTitle" />

    <div class="w-full">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 md:p-6">

            <form @submit.prevent="handleSave" class="space-y-6">

                <div class="bg-white border rounded-xl p-5 shadow-sm space-y-4">
                    <h3 class="text-base font-semibold">
                        Basic Information
                    </h3>

                    <div class="grid grid-cols-1 gap-4">

                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Name <span class="text-red-500">*</span>
                            </label>

                            <input v-model="saveForm.name" placeholder="Enter prompt name"
                                :disabled="isUpdate"
                                class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                :class="{
                                    'bg-gray-100 text-gray-500 cursor-not-allowed': isUpdate,
                                    'border-red-500': saveForm.errors.name,
                                    'border-gray-300': !saveForm.errors.name,
                                }" readonly/>

                            <p v-if="saveForm.errors.name" class="text-red-500 text-sm mt-1">
                                {{ saveForm.errors.name }}
                            </p>

                            <p v-if="isUpdate" class="text-gray-500 text-xs mt-1">
                                Name cannot be changed after creation.
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Prompt <span class="text-red-500">*</span>
                            </label>

                            <textarea v-model="saveForm.prompt" rows="12" placeholder="Enter the AI prompt"
                                class="w-full border rounded-md px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                :class="saveForm.errors.prompt ? 'border-red-500' : 'border-gray-300'"></textarea>

                            <p v-if="saveForm.errors.prompt" class="text-red-500 text-sm mt-1">
                                {{ saveForm.errors.prompt }}
                            </p>
                        </div>

                    </div>
                </div>

                <div class="flex justify-center">
                    <button type="submit" :disabled="saveForm.processing"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md flex items-center gap-2 transition disabled:opacity-60 disabled:cursor-not-allowed">
                        <FontAwesomeIcon v-if="saveForm.processing" icon="spinner" spin />
                        <FontAwesomeIcon v-else icon="save" />
                        {{ saveForm.processing ? 'Saving...' : 'Save' }}
                    </button>
                </div>

            </form>

        </div>
    </div>
</template>
