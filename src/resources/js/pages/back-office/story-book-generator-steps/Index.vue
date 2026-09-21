<script setup>
import Layout from '@/pages/layouts/AuthLayout.vue'
import ModelPagination from '@/components/common/pagination/Pagination.vue'
import InfiniteScrollApiSelect from '@/components/common/multi-select/InfiniteScrollApiSelect.vue'

import { ref, computed, onMounted, nextTick, inject } from 'vue'
import { Head, useForm, router as intertiaJsRoute } from '@inertiajs/vue3'

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome"
import { library as FontAwesomeLibrary } from '@fortawesome/fontawesome-svg-core'
import {
    faFilter, faInfo,
    faSpinner
} from '@fortawesome/free-solid-svg-icons'

import { formatDateTime } from '@/composables/useDateTime'
import { itemListFilterParameters } from '@/composables/useDataTable'

FontAwesomeLibrary.add(faFilter, faInfo, faSpinner)

defineOptions({ layout: Layout })

const authUser = inject("authUser")

const { storyBookGeneratorSteps } = defineProps({
    storyBookGeneratorSteps: Object,
})

const paginationOnly = computed(() => {
    if (!storyBookGeneratorSteps) return {}
    const { data, ...rest } = storyBookGeneratorSteps
    return rest
})

const filterForm = useForm({
    per_page: null,
    date: '',
    search: '',
})

const applyFilter = () => {
    if (filterForm.processing) return

    const cleanParams = itemListFilterParameters(filterForm.data())

    intertiaJsRoute.get(route('back-office.story-book-generator-steps.index'), cleanParams, {
        replace: true,
        preserveScroll: true,
        preserveState: true,
        onFinish: () => filterForm.processing = false,
    })
}

onMounted(async () => {
    const urlParams = new URLSearchParams(window.location.search)

    filterForm.per_page = urlParams.get('per_page') || ''
    filterForm.date = urlParams.get('date') || ''
    filterForm.search = urlParams.get('search') || ''

    await nextTick()

    window.dispatchEvent(
        new CustomEvent('set-breadcrumb', {
            detail: [
                { text: 'Story Book Generator Steps', active: true },
            ],
        })
    )
})
</script>

<template>
    <Head :title="'Story Book Generator Steps'" />

    <div class="w-full space-y-6">

        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold">
                Story Book Generator Steps
            </h2>
        </div>

        <form @submit.prevent="applyFilter" class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <InfiniteScrollApiSelect :form="filterForm" fieldName="per_page"
                    :selectedItem="filterForm.per_page" :apiUrl="route('search.per-pages')" :multiple="false"
                    placeholder="Per Page" />

                <input type="date" v-model="filterForm.date"
                    class="w-full border border-gray-200 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />

                <input type="search" v-model="filterForm.search" placeholder="Search by name or ai prompt..."
                    class="w-full border border-gray-200 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />

            </div>

            <div class="flex justify-end">
                <button type="submit" :disabled="filterForm.processing"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md flex items-center gap-2 transition disabled:opacity-60 disabled:cursor-not-allowed">
                    <FontAwesomeIcon v-if="filterForm.processing" icon="spinner" spin />
                    <FontAwesomeIcon icon="filter" />
                    {{ filterForm.processing ? 'Applying...' : 'Apply Filter' }}
                </button>
            </div>
        </form>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">

                    <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">#</th>
                            <th class="px-4 py-3 text-left">Name</th>
                            <th class="px-4 py-3 text-left">Ai Prompt</th>
                            <th class="px-4 py-3 text-left">Previous Step</th>
                            <th class="px-4 py-3 text-left">Next Step</th>
                            <th class="px-4 py-3 text-center">Dependencies</th>
                            <th class="px-4 py-3 text-left">Created At</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        <tr v-for="(item, index) in storyBookGeneratorSteps?.data" :key="item.id" class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3">{{ index + 1 }}</td>

                            <td class="px-4 py-3 font-medium">
                                {{ item.name || 'N/A' }}
                            </td>

                            <td class="px-4 py-3 text-gray-500">
                                {{ item.ai_prompt?.name || 'N/A' }}
                            </td>

                            <td class="px-4 py-3 text-gray-500">
                                <a v-if="item.previous_step" :href="route('back-office.story-book-generator-steps.details', { slug: item.previous_step.slug })"
                                    class="text-blue-600 hover:underline">
                                    {{ item.previous_step.name }}
                                </a>
                                <span v-else>N/A</span>
                            </td>

                            <td class="px-4 py-3 text-gray-500">
                                <a v-if="item.next_step" :href="route('back-office.story-book-generator-steps.details', { slug: item.next_step.slug })"
                                    class="text-blue-600 hover:underline">
                                    {{ item.next_step.name }}
                                </a>
                                <span v-else>N/A</span>
                            </td>

                            <td class="px-4 py-3 text-center text-gray-500">
                                {{ item.depend_on_step_ids?.length || 0 }}
                            </td>

                            <td class="px-4 py-3 text-gray-500">
                                {{ item.created_at ? formatDateTime(item.created_at) : 'N/A' }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">

                                    <a :href="route('back-office.story-book-generator-steps.details', { slug: item.slug })"
                                        class="p-2 rounded-md text-blue-600 hover:bg-blue-50 border"
                                        title="View Details">
                                        <FontAwesomeIcon icon="info" />
                                    </a>

                                </div>
                            </td>
                        </tr>

                        <tr v-if="!storyBookGeneratorSteps?.data?.length">
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                No story book generator steps found
                            </td>
                        </tr>
                    </tbody>

                </table>
            </div>
        </div>

        <ModelPagination :pagination="paginationOnly" />
    </div>
</template>