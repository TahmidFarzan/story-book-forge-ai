<script setup>
import Layout from '@/pages/layouts/AuthLayout.vue'

import { ref, computed, inject, onMounted, nextTick } from 'vue'
import { Head, router as intertiaJsRoute } from '@inertiajs/vue3'

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome"
import { library as FontAwesomeLibrary } from '@fortawesome/fontawesome-svg-core'
import {
    faPen, faTrash, faSpinner, faInfo
} from '@fortawesome/free-solid-svg-icons'

import { formatDateTime } from '@/composables/useDateTime'
import { canUpdateLanguage, canDeleteLanguage } from '@/composables/useUserPermissions'
import RecentActivities from '@/components/back-office/activity-log/RecentModelActivityLogs.vue'

FontAwesomeLibrary.add(faPen, faTrash, faSpinner, faInfo)

defineOptions({ layout: Layout })

const authUser = inject("authUser")

const { language } = defineProps({
    language: Object,
})

const pageTitle = computed(() => {
    return language?.name ? `Details of ${language?.name}` : 'Language Details'
})

const showDeleteModal = ref(false)
const deleteProcessing = ref(false)

const canUpdate = (language) => canUpdateLanguage(authUser?.value, language)
const canDelete = (language) => canDeleteLanguage(authUser?.value, language)

const confirmDelete = () => {
    showDeleteModal.value = true
}

const closeDeleteModal = () => {
    showDeleteModal.value = false
}

const handleDelete = () => {
    if (!language || deleteProcessing.value) return

    deleteProcessing.value = true

    intertiaJsRoute.delete(route('back-office.languages.delete', { slug: language?.slug }), {
        onFinish: () => {
            closeDeleteModal()
            deleteProcessing.value = false
        }
    })
}

onMounted(async () => {
    await nextTick()

    window.dispatchEvent(
        new CustomEvent('set-breadcrumb', {
            detail: [
                { text: 'Languages', href: route('back-office.languages.index') },
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

            <div class="flex gap-2">

                <a v-if="canUpdate(language)" :href="route('back-office.languages.edit', { slug: language.slug })"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md flex items-center gap-2 transition">
                    <FontAwesomeIcon icon="pen" />
                    Edit
                </a>

                <button v-if="canDelete(language)" type="button" @click="confirmDelete"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md flex items-center gap-2 transition">
                    <FontAwesomeIcon icon="trash" />
                    Delete
                </button>

            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 space-y-4">
                    <h3 class="text-sm font-semibold">
                        Basic Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Name</p>
                            <p class="text-sm font-semibold">
                                {{ language?.name || 'N/A' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Slug</p>
                            <p class="text-sm">
                                {{ language?.slug || 'N/A' }}
                            </p>
                        </div>

                        <div class="md:col-span-2">
                            <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Brief</p>
                            <p class="text-sm text-gray-700 whitespace-pre-line">
                                {{ language?.brief || 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 space-y-4">
                    <h3 class="text-sm font-semibold">
                        System Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Created At</p>
                            <p class="text-sm">
                                {{ language?.created_at ? formatDateTime(language?.created_at) : 'N/A' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Updated At</p>
                            <p class="text-sm">
                                {{ language?.updated_at ? formatDateTime(language?.updated_at) : 'N/A' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Created By</p>
                            <p class="text-sm">
                                {{ language?.createdBy?.name || 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="space-y-6">

                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 space-y-4">
                    <h3 class="text-sm font-semibold">
                        Activity Logs
                    </h3>

                    <RecentActivities :model="language" model-slug="language" />
                </div>

            </div>

        </div>

        <Teleport to="body">
            <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0"
                enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="showDeleteModal"
                    class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">

                    <Transition enter-active-class="transition ease-out duration-200"
                        enter-from-class="opacity-0 scale-95 translate-y-4"
                        enter-to-class="opacity-100 scale-100 translate-y-0"
                        leave-active-class="transition ease-in duration-150"
                        leave-from-class="opacity-100 scale-100 translate-y-0"
                        leave-to-class="opacity-0 scale-95 translate-y-4">
                        <div v-if="showDeleteModal" class="bg-white rounded-xl shadow-lg w-[380px] p-6 space-y-4">
                            <h3 class="text-lg font-semibold text-red-600">
                                Delete Language
                            </h3>

                            <p class="text-sm font-medium">
                                {{ language?.name }}
                            </p>

                            <p class="text-sm text-gray-500">
                                This action cannot be undone.
                            </p>

                            <div class="flex justify-end gap-2 pt-2">
                                <button type="button" @click="closeDeleteModal"
                                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                                    Cancel
                                </button>

                                <button type="button" @click="handleDelete" :disabled="deleteProcessing"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                                    <FontAwesomeIcon v-if="deleteProcessing" icon="spinner" spin />
                                    {{ deleteProcessing ? 'Deleting...' : 'Delete' }}
                                </button>
                            </div>
                        </div>
                    </Transition>

                </div>
            </Transition>
        </Teleport>

    </div>
</template>