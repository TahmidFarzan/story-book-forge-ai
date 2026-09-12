<script setup>
import Layout from '@/pages/layouts/AuthLayout.vue'
import ModelPagination from '@/components/common/pagination/Pagination.vue'
import InfiniteScrollApiSelect from '@/components/common/multi-select/InfiniteScrollApiSelect.vue'

import { ref, computed, onMounted, onUnmounted, nextTick, inject } from 'vue'
import { Head, useForm, router as intertiaJsRoute } from '@inertiajs/vue3'

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome"
import { library as FontAwesomeLibrary } from '@fortawesome/fontawesome-svg-core'
import {
    faTrash, faFilter, faPlus, faPen, faSpinner,
    faCalendar, faHashtag, faWandMagicSparkles
} from '@fortawesome/free-solid-svg-icons'

import StoryCreateForm from '@/components/back-office/story/StoryCreateForm.vue'

import { formatDateTime } from '@/composables/useDateTime'
import { itemListFilterParameters } from '@/composables/useDataTable'

import {
    canCreateStory,
    canUpdateStory,
    canDeleteStory
} from '@/composables/useUserPermissions'

FontAwesomeLibrary.add(
    faTrash, faFilter, faPlus, faPen, faSpinner,
    faCalendar, faHashtag, faWandMagicSparkles
)

defineOptions({ layout: Layout })

const authUser = inject("authUser")

const deletingRow = ref(null)
const showDeleteModal = ref(false)
const deleteProcessing = ref(false)

const showCreateForm = ref(false)
const showFloatingButton = ref(false)

const { stories } = defineProps({
    stories: Object,
})

const paginationOnly = computed(() => {
    if (!stories) return {}
    const { data, ...rest } = stories
    return rest
})

const filterForm = useForm({
    per_page: null,
    created_by_id: null,
    date: '',
    search: '',
    status: null,
})

const applyFilter = () => {
    if (filterForm.processing) return

    const cleanParams = itemListFilterParameters(filterForm.data())

    intertiaJsRoute.get(route('back-office.stories.index'), cleanParams, {
        replace: true,
        preserveScroll: true,
        preserveState: true,
        onFinish: () => filterForm.processing = false,
    })
}

const clearFilters = () => {
    if (filterForm.processing) return

    filterForm.per_page = null
    filterForm.created_by_id = null
    filterForm.date = ''
    filterForm.search = ''
    filterForm.status = null

    intertiaJsRoute.get(route('back-office.stories.index'), {}, {
        replace: true,
        preserveScroll: true,
        preserveState: true,
        onFinish: () => filterForm.processing = false,
    })
}

const hasActiveFilters = computed(() => {
    return filterForm.per_page || filterForm.created_by_id || filterForm.date || filterForm.search || filterForm.status
})

const confirmDelete = (item) => {
    deletingRow.value = item
    showDeleteModal.value = true
}

const closeDeleteModal = () => {
    showDeleteModal.value = false
    deletingRow.value = null
}

const canCreate = () => canCreateStory(authUser?.value)
const canUpdate = (item) => canUpdateStory(authUser?.value, item)
const canDelete = (item) => canDeleteStory(authUser?.value, item)

const handleDelete = (item) => {
    if (!item || deleteProcessing.value) return

    deleteProcessing.value = true

    intertiaJsRoute.delete(route('back-office.stories.delete', { slug: item?.slug }), {
        onFinish: () => {
            closeDeleteModal()
            deleteProcessing.value = false
        }
    })
}

const getStatusColor = (status) => {
    const colors = {
        'Draft': 'bg-gray-100 text-gray-700 border-gray-300',
        'Ongoing': 'bg-blue-50 text-blue-700 border-blue-300',
        'Pending': 'bg-yellow-50 text-yellow-700 border-yellow-300',
        'Complete': 'bg-green-50 text-green-700 border-green-300',
        'Failed': 'bg-red-50 text-red-700 border-red-300',
    }
    return colors[status] || 'bg-gray-100 text-gray-600 border-gray-300'
}

const getStepStatusColor = (status) => {
    const colors = {
        'Draft': 'bg-gray-100 text-gray-600',
        'Ongoing': 'bg-blue-50 text-blue-600',
        'Pending': 'bg-yellow-50 text-yellow-600',
        'Complete': 'bg-green-50 text-green-600',
        'Failed': 'bg-red-50 text-red-600',
        'Cancelled': 'bg-gray-100 text-gray-500',
    }
    return colors[status] || 'bg-gray-100 text-gray-600'
}

const openCreateForm = () => {
    showCreateForm.value = true
}

const handleFormClose = () => {
    showCreateForm.value = false
}

const handleFormSuccess = () => {
    showCreateForm.value = false
}

const handleScroll = () => {
    showFloatingButton.value = window.scrollY > 200
}

onMounted(async () => {
    const urlParams = new URLSearchParams(window.location.search)

    filterForm.per_page = urlParams.get('per_page') || ''
    filterForm.created_by_id = urlParams.get('created_by_id') || ''
    filterForm.date = urlParams.get('date') || ''
    filterForm.search = urlParams.get('search') || ''
    filterForm.status = urlParams.get('status') || null

    window.addEventListener('scroll', handleScroll, { passive: true })
    handleScroll()

    await nextTick()

    window.dispatchEvent(
        new CustomEvent('set-breadcrumb', {
            detail: [
                { text: 'Stories', active: true },
            ],
        })
    )
})

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll)
})
</script>

<template>
    <Head :title="'Stories'" />

    <div class="w-full space-y-6">

        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold">
                Stories
            </h2>

            <button v-if="canCreate()" type="button" @click="openCreateForm"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md flex items-center gap-2 transition">
                <FontAwesomeIcon icon="plus" />
                Create
            </button>
        </div>

        <form @submit.prevent="applyFilter" class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <InfiniteScrollApiSelect :form="filterForm" fieldName="per_page"
                    :selectedItem="filterForm.per_page" :apiUrl="route('search.per-pages')" :multiple="false"
                    placeholder="Per Page" />

                <InfiniteScrollApiSelect :form="filterForm" fieldName="created_by_id"
                    :selectedItem="filterForm.created_by_id" :apiUrl="route('search.users')" :multiple="false"
                    placeholder="Created By" />

                <InfiniteScrollApiSelect :form="filterForm" fieldName="status"
                    :selectedItem="filterForm.status" :apiUrl="route('search.story-book-statuses')" :multiple="false"
                    placeholder="Status" />

                <input type="date" v-model="filterForm.date"
                    class="w-full border border-gray-200 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />

                <input type="search" v-model="filterForm.search" placeholder="Search by name..."
                    class="w-full border border-gray-200 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />

            </div>

            <div class="flex justify-end gap-2">
                <button v-if="hasActiveFilters" type="button" @click="clearFilters"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md flex items-center gap-2 transition text-sm">
                    Clear Filters
                </button>

                <button type="submit" :disabled="filterForm.processing"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md flex items-center gap-2 transition disabled:opacity-60 disabled:cursor-not-allowed">
                    <FontAwesomeIcon v-if="filterForm.processing" icon="spinner" spin />
                    <FontAwesomeIcon icon="filter" />
                    {{ filterForm.processing ? 'Applying...' : 'Apply Filter' }}
                </button>
            </div>
        </form>

        <div class="space-y-4">
            <div v-for="item in stories?.data" :key="item.id"
                class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition p-5">

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                    <div class="flex-1 space-y-2">
                        <div class="flex items-center gap-3">
                            <h3 class="text-base font-semibold text-gray-900">
                                {{ item.name || 'N/A' }}
                            </h3>

                            <span :class="getStatusColor(item.status)"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border">
                                {{ item.status || 'Draft' }}
                            </span>
                        </div>

                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                            <span class="flex items-center gap-1.5">
                                <FontAwesomeIcon icon="hashtag" class="text-xs" />
                                {{ item.id }}
                            </span>

                            <span class="flex items-center gap-1.5">
                                <FontAwesomeIcon icon="calendar" class="text-xs" />
                                {{ item.created_at ? formatDateTime(item.created_at) : 'N/A' }}
                            </span>

                            <span v-if="item.createdBy" class="flex items-center gap-1.5">
                                <FontAwesomeIcon icon="pen" class="text-xs" />
                                {{ item.createdBy?.name }}
                            </span>
                        </div>


                    </div>

                    <div class="flex items-center gap-2 md:flex-shrink-0">
                        <button v-if="canUpdate(item)" type="button" @click="openCreateForm"
                            class="p-2 rounded-md text-yellow-600 hover:bg-yellow-50 border transition"
                            title="Edit">
                            <FontAwesomeIcon icon="pen" />
                        </button>

                        <button v-if="canDelete(item)" type="button" @click="confirmDelete(item)"
                            class="p-2 rounded-md text-red-600 hover:bg-red-50 border transition"
                            title="Delete">
                            <FontAwesomeIcon icon="trash" />
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="!stories?.data?.length"
                class="bg-white border border-gray-200 rounded-xl shadow-sm p-12 text-center">
                <FontAwesomeIcon icon="wand-magic-sparkles" class="text-4xl text-gray-300 mb-3" />
                <p class="text-gray-500 text-sm">
                    No stories found
                </p>
            </div>
        </div>

        <ModelPagination :pagination="paginationOnly" />

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
                                Delete Story
                            </h3>

                            <p class="text-sm font-medium">
                                {{ deletingRow?.name }}
                            </p>

                            <p class="text-sm text-gray-500">
                                This action cannot be undone. All associated steps will also be deleted.
                            </p>

                            <div class="flex justify-end gap-2 pt-2">
                                <button type="button" @click="closeDeleteModal"
                                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                                    Cancel
                                </button>

                                <button type="button" @click="handleDelete(deletingRow)" :disabled="deleteProcessing"
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

        <Teleport to="body">
            <Transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0"
                enter-to-class="opacity-100" leave-active-class="transition ease-in duration-200"
                leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="showCreateForm"
                    class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-start justify-center z-50 overflow-y-auto py-8">

                    <Transition enter-active-class="transition ease-out duration-300"
                        enter-from-class="opacity-0 scale-95 translate-y-4"
                        enter-to-class="opacity-100 scale-100 translate-y-0"
                        leave-active-class="transition ease-in duration-200"
                        leave-from-class="opacity-100 scale-100 translate-y-0"
                        leave-to-class="opacity-0 scale-95 translate-y-4">
                        <div v-if="showCreateForm"
                            class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl mx-4 my-auto">

                            <StoryCreateForm
                                @close="handleFormClose"
                                @success="handleFormSuccess"
                            />

                        </div>
                    </Transition>

                </div>
            </Transition>
        </Teleport>

        <Transition enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-4 scale-95"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 translate-y-4 scale-95">
            <button v-if="canCreate() && showFloatingButton && !showCreateForm" type="button"
                @click="openCreateForm"
                class="fixed bottom-6 right-6 z-40 bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-full shadow-lg flex items-center gap-2 transition"
                title="Create Story">
                <FontAwesomeIcon icon="wand-magic-sparkles" />
                <span class="hidden sm:inline text-sm font-medium">New Generator</span>
            </button>
        </Transition>

    </div>
</template>

