<script setup>
import Layout from '@/pages/layouts/AuthLayout.vue'
import RecentActivities from '@/components/back-office/activity-log/RecentModelActivityLogs.vue'

import { computed, onMounted, nextTick } from 'vue'
import { Head } from '@inertiajs/vue3'

import { formatDateTime } from '@/composables/useDateTime'

defineOptions({ layout: Layout })

const { storyBookGeneratorStep, dependOnSteps } = defineProps({
    storyBookGeneratorStep: Object,
    dependOnSteps: Array,
})

const pageTitle = computed(() => `Details of ${storyBookGeneratorStep?.name}`)

const stepDetailsUrl = (slug) => route('back-office.story-book-generator-steps.details', { slug })

onMounted(async () => {
    await nextTick()

    window.dispatchEvent(
        new CustomEvent('set-breadcrumb', {
            detail: [
                { text: 'Story Book Generator Steps', href: route('back-office.story-book-generator-steps.index') },
                { text: pageTitle.value, active: true }
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
                Story Book Generator Step Details
            </h2>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 space-y-4">
            <h3 class="text-base font-semibold border-b pb-2">
                Basic Information
            </h3>

            <div class="grid grid-cols-1 gap-4 text-sm">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border border-gray-200 rounded-lg p-4 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Name</span>
                            <span class="font-medium">{{ storyBookGeneratorStep?.name || 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Slug</span>
                            <span class="font-medium">{{ storyBookGeneratorStep?.slug || 'N/A' }}</span>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Ai Prompt</span>
                            <a v-if="storyBookGeneratorStep?.ai_prompt"
                                :href="route('back-office.ai-prompts.details', { slug: storyBookGeneratorStep.ai_prompt.slug })"
                                class="font-medium text-blue-600 hover:underline">
                                {{ storyBookGeneratorStep.ai_prompt.name }}
                            </a>
                            <span v-else class="font-medium">N/A</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 space-y-4">
            <h3 class="text-base font-semibold border-b pb-2">
                Step Flow
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="border border-gray-200 rounded-lg p-4 space-y-2">
                    <div class="text-gray-500 mb-1">Previous Step</div>
                    <a v-if="storyBookGeneratorStep?.previous_step"
                        :href="stepDetailsUrl(storyBookGeneratorStep.previous_step.slug)"
                        class="font-medium text-blue-600 hover:underline">
                        {{ storyBookGeneratorStep.previous_step.name }}
                    </a>
                    <span v-else class="font-medium">N/A</span>
                </div>

                <div class="border border-gray-200 rounded-lg p-4 space-y-2">
                    <div class="text-gray-500 mb-1">Next Step</div>
                    <a v-if="storyBookGeneratorStep?.next_step"
                        :href="stepDetailsUrl(storyBookGeneratorStep.next_step.slug)"
                        class="font-medium text-blue-600 hover:underline">
                        {{ storyBookGeneratorStep.next_step.name }}
                    </a>
                    <span v-else class="font-medium">N/A</span>
                </div>

                <div class="border border-gray-200 rounded-lg p-4 space-y-2">
                    <div class="text-gray-500 mb-1">Dependencies ({{ dependOnSteps?.length || 0 }})</div>
                    <ul v-if="dependOnSteps?.length" class="space-y-1">
                        <li v-for="dep in dependOnSteps" :key="dep.id">
                            <a :href="stepDetailsUrl(dep.slug)" class="text-blue-600 hover:underline">
                                {{ dep.name }}
                            </a>
                        </li>
                    </ul>
                    <span v-else class="font-medium">None</span>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 space-y-4">
            <h3 class="text-base font-semibold border-b pb-2">
                System Information
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">

                <div class="border border-gray-200 rounded-lg p-4 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Created At</span>
                        <span class="font-medium">
                            {{ storyBookGeneratorStep?.created_at ? formatDateTime(storyBookGeneratorStep.created_at) : 'N/A' }}
                        </span>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg p-4 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Updated At</span>
                        <span class="font-medium">
                            {{ storyBookGeneratorStep?.updated_at ? formatDateTime(storyBookGeneratorStep.updated_at) : 'N/A' }}
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500">Updated By</span>
                        <span class="font-medium">
                            {{ storyBookGeneratorStep?.latest_activity_log?.causer?.name || 'N/A' }}
                        </span>
                    </div>
                </div>

            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 space-y-4">
            <h3 class="text-base font-semibold border-b pb-2">
                Activity Logs
            </h3>

            <RecentActivities :model-slug="'story-book-generator-step'" :model="storyBookGeneratorStep" />
        </div>
    </div>
</template>