<script setup>
import Layout from '@/pages/layouts/AuthLayout.vue'

import { computed, onMounted, nextTick } from 'vue'
import { Head, useForm, router as intertiaJsRoute } from '@inertiajs/vue3'

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome"
import { library as FontAwesomeLibrary } from '@fortawesome/fontawesome-svg-core'
import {
    faSave, faSpinner, faInfo
} from '@fortawesome/free-solid-svg-icons'

FontAwesomeLibrary.add(faSave, faSpinner, faInfo)

defineOptions({ layout: Layout })

const { language } = defineProps({
    language: Object,
})

const isUpdate = computed(() => !!language?.slug)

const pageTitle = computed(() => {
    return isUpdate.value ? `Edit ${language?.name || ''}` : 'Create Language'
})

const saveForm = useForm({
    name: language?.name || '',
    brief: language?.brief || '',
})

const formErrors = computed(() => saveForm.errors)

const validateForm = () => {
    const errors = {}

    if (!saveForm.name?.trim()) {
        errors.name = 'The name is required.'
    }

    return errors
}

const breadcrumbs = [
    { text: 'Languages', href: route('back-office.languages.index') },
]

const requestConfig = {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
        saveForm.reset()
        window.dispatchEvent(
            new CustomEvent('flash-me', {
                detail: {
                    message: isUpdate.value ? 'Language updated successfully.' : 'Language created successfully.',
                    type: 'success',
                },
            })
        )
    },
}

const handleSubmit = () => {
    const clientErrors = validateForm()

    if (Object.keys(clientErrors).length) {
        saveForm.setError(clientErrors)
        return
    }

    if (isUpdate.value) {
        intertiaJsRoute.patch(route('back-office.languages.update', { slug: language.slug }), saveForm.data(), requestConfig)
        return
    }

    intertiaJsRoute.post(route('back-office.languages.save'), saveForm.data(), requestConfig)
}

onMounted(async () => {
    await nextTick()

    window.dispatchEvent(
        new CustomEvent('set-breadcrumb', {
            detail: [
                ...breadcrumbs,
                { text: pageTitle.value, active: true },
            ],
        })
    )
})
</script>

<template>
    <Head :title="pageTitle" />

    <div class="w-full space-y-6">

        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold">
                {{ pageTitle }}
            </h2>

            <a :href="route('back-office.languages.index')"
                class="text-white px-4 py-2 rounded-md flex items-center gap-2 transition">
                <FontAwesomeIcon icon="info" />
            </a>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-6">

            <div class="flex flex-col lg:flex-row gap-6">

                <div class="flex-1 space-y-4 bg-white border border-gray-200 rounded-xl shadow-sm p-5">

                    <div class="flex justify-between items-center">
                        <h3 class="text-sm font-semibold">
                            Basic Information
                        </h3>
                    </div>

                    <div class="space-y-4">

                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" v-model="saveForm.name"
                                class="w-full border border-gray-200 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                            <p v-if="formErrors?.name && saveForm.name?.length == 0" class="text-sm text-red-600 mt-1">
                                {{ formErrors.name }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Brief
                            </label>
                            <textarea v-model="saveForm.brief" rows="4"
                                class="w-full border border-gray-200 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                        </div>

                    </div>

                </div>

            </div>

            <div class="flex justify-end">
                <button type="submit" :disabled="saveForm.processing"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md flex items-center gap-2 transition disabled:opacity-60 disabled:cursor-not-allowed">
                    <FontAwesomeIcon v-if="saveForm.processing" icon="spinner" spin />
                    <FontAwesomeIcon icon="save" />
                    {{ saveForm.processing ? 'Saving...' : isUpdate ? 'Update Language' : 'Save Language' }}
                </button>
            </div>

        </form>

    </div>
</template>