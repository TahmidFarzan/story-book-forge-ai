
<script setup>
import Layout from '@/pages/layouts/AuthLayout.vue'
import InfiniteScrollApiSelect from '@/components/common/multi-select/InfiniteScrollApiSelect.vue'

import { computed, ref, onMounted, nextTick } from 'vue'
import { Head, useForm, router as intertiaJsRoute } from '@inertiajs/vue3'

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome"
import { library as FontAwesomeLibrary } from '@fortawesome/fontawesome-svg-core'
import { faSave, faEye, faEyeSlash, faSpinner } from '@fortawesome/free-solid-svg-icons'

FontAwesomeLibrary.add(faSave, faEye, faEyeSlash, faSpinner)

defineOptions({ layout: Layout })

const { genre } = defineProps({
    genre: Object,
})

const isUpdate = computed(() => !!genre?.slug)

const pageTitle = computed(() => {
    return isUpdate.value
        ? `Edit ${genre?.name}`
        : 'Create Genre'
})

const saveForm = useForm({
    name: genre?.name || null,
    brief: genre?.brief || null,
    prompt_instruction: genre?.prompt_instruction || null,
    audience_ids: [],
})

const selectedAudiences = ref(
    (genre?.audiences || []).map((audience) => ({
        id: audience.id,
        name: audience.name,
    }))
)

function validateForm() {
    saveForm.clearErrors()

    let valid = true

    if (!saveForm.name) {
        saveForm.setError('name', 'Name is required')
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
        intertiaJsRoute.post(
            route('back-office.genres.update', { slug: genre?.slug }),
            { ...saveForm.data(), _method: 'patch' },
            requestConfig
        )
    } else {
        saveForm.post(route('back-office.genres.save'), requestConfig)
    }
}

onMounted(async () => {
    await nextTick()

    window.dispatchEvent(
        new CustomEvent('set-breadcrumb', {
            detail: [
                { text: 'Genres', href: route('back-office.genres.index') },
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Name <span class="text-red-500">*</span>
                            </label>

                            <input v-model="saveForm.name" placeholder="Enter genre name"
                                class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                :class="saveForm.errors.name ? 'border-red-500' : 'border-gray-300'" />

                            <p v-if="saveForm.errors.name" class="text-red-500 text-sm mt-1">
                                {{ saveForm.errors.name }}
                            </p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1">
                                Brief
                            </label>

                            <textarea v-model="saveForm.brief" rows="4" placeholder="Enter brief description"
                                class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                :class="saveForm.errors.brief ? 'border-red-500' : 'border-gray-300'"></textarea>

                            <p v-if="saveForm.errors.brief" class="text-red-500 text-sm mt-1">
                                {{ saveForm.errors.brief }}
                            </p>
                        </div>

                    </div>
                </div>

                <div class="bg-white border rounded-xl p-5 shadow-sm space-y-4">
                    <h3 class="text-base font-semibold">
                        Audiences
                    </h3>

                    <div>
                        <label class="block text-sm font-medium mb-1">
                            Audience
                        </label>

                        <InfiniteScrollApiSelect
                            :form="saveForm"
                            fieldName="audience_ids"
                            :selectedItem="selectedAudiences"
                            :apiUrl="route('search.audiences')"
                            :multiple="true"
                            placeholder="Select Audiences" />

                        <p v-if="saveForm.errors.audience_ids" class="text-red-500 text-sm mt-1">
                            {{ saveForm.errors.audience_ids }}
                        </p>
                    </div>
                </div>

                <div class="bg-white border rounded-xl p-5 shadow-sm space-y-4">
                    <h3 class="text-base font-semibold">
                        Prompt Instruction
                    </h3>

                    <div>
                        <label class="block text-sm font-medium mb-1">
                            Master AI Configuration Instruction
                        </label>

                        <textarea v-model="saveForm.prompt_instruction" rows="8"
                            placeholder="Enter the master instruction for AI form generation for this genre. This controls required form sections, descriptions and genre-specific generation rules."
                            class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            :class="saveForm.errors.prompt_instruction ? 'border-red-500' : 'border-gray-300'"></textarea>

                        <p v-if="saveForm.errors.prompt_instruction" class="text-red-500 text-sm mt-1">
                            {{ saveForm.errors.prompt_instruction }}
                        </p>
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
