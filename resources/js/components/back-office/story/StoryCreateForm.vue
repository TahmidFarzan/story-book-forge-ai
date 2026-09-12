<script setup>
import InfiniteScrollApiSelect from '@/components/common/multi-select/InfiniteScrollApiSelect.vue'

import { ref, computed, onMounted, nextTick } from 'vue'
import { useForm } from '@inertiajs/vue3'

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome"
import { library as FontAwesomeLibrary } from '@fortawesome/fontawesome-svg-core'
import {
    faXmark, faSave, faSpinner, faCheck, faLock,
    faArrowRight, faArrowLeft, faWandMagicSparkles,
    faBrain, faUser, faGlobe, faBook, faCog
} from '@fortawesome/free-solid-svg-icons'

FontAwesomeLibrary.add(
    faXmark, faSave, faSpinner, faCheck, faLock,
    faArrowRight, faArrowLeft, faWandMagicSparkles,
    faBrain, faUser, faGlobe, faBook, faCog
)

const emit = defineEmits(['close', 'success'])

const props = defineProps({
    novel: { type: Object, default: null },
})

const isUpdate = computed(() => !!props.novel?.slug)

const pageTitle = computed(() => {
    return isUpdate.value
        ? `Edit ${props.novel?.name}`
        : 'New Novel'
})

const STEP_DEFINITIONS = [
    { id: 1, name: 'Configuration', icon: 'cog', description: 'Basic generation settings' },
    { id: 2, name: 'World Building', icon: 'globe', description: 'World and setting details' },
    { id: 3, name: 'Characters', icon: 'user', description: 'Character development' },
    { id: 4, name: 'Plot & Outline', icon: 'book', description: 'Story structure' },
    { id: 5, name: 'Review & Generate', icon: 'wand-magic-sparkles', description: 'Final review' },
]

const activeStep = ref(1)
const completedSteps = ref(new Set())
const submittingStep = ref(null)

const step1Form = useForm({
    is_18_plus: false,
    enable_mature_content: false,
    additional_information: null,
    novel_continuity: null,
    language_id: null,
    genre_ids: [],
    novel_type_ids: [],
    audience_ids: [],
    ai_brain_id: null,
})

const isStepAccessible = (stepId) => {
    if (stepId === 1) return true
    return completedSteps.value.has(stepId - 1)
}

const isStepCompleted = (stepId) => completedSteps.value.has(stepId)

const getStepState = (stepId) => {
    if (isStepCompleted(stepId)) return 'completed'
    if (stepId === activeStep.value) return 'active'
    if (isStepAccessible(stepId)) return 'accessible'
    return 'locked'
}

function validateStep1() {
    step1Form.clearErrors()

    let valid = true

    if (!step1Form.ai_brain_id) {
        step1Form.setError('ai_brain_id', 'AI Brain selection is required')
        valid = false
    }

    if (!step1Form.novel_continuity) {
        step1Form.setError('novel_continuity', 'Novel continuity is required')
        valid = false
    }

    if (!step1Form.language_id) {
        step1Form.setError('language_id', 'Language is required')
        valid = false
    }

    if (!step1Form.genre_ids) {
        step1Form.setError('genre_ids', 'Genres is required')
        valid = false
    }

    if (!step1Form.novel_type_ids) {
        step1Form.setError('novel_type_ids', 'Novel types is required')
        valid = false
    }

    if (step1Form.enable_mature_content && !step1Form.is_18_plus) {
        step1Form.setError('enable_mature_content', 'Mature content requires 18+ setting')
        valid = false
    }

    return valid
}

function submitStep1() {
    if (step1Form.processing) return
    if (!validateStep1()) return

    submittingStep.value = 1
    step1Form.processing = true

    step1Form.post(route('back-office.novels.save'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            completedSteps.value.add(1)
            activeStep.value = 2
            step1Form.clearErrors()
        },
        onError: (errors) => {
            step1Form.clearErrors()
            step1Form.setError(errors)
        },
        onFinish: () => {
            submittingStep.value = null
            step1Form.processing = false
        }
    })
}

const goToStep = (stepId) => {
    if (isStepAccessible(stepId) || isStepCompleted(stepId)) {
        activeStep.value = stepId
    }
}

const goNext = () => {
    if (activeStep.value < 5 && isStepAccessible(activeStep.value + 1)) {
        activeStep.value++
    }
}

const goPrev = () => {
    if (activeStep.value > 1) {
        activeStep.value--
    }
}

onMounted(async () => {
    await nextTick()
})
</script>

<template>
    <div class="flex flex-col h-full">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold flex items-center gap-2">
                <FontAwesomeIcon icon="wand-magic-sparkles" class="text-purple-600" />
                {{ pageTitle }}
            </h2>

            <button type="button" @click="emit('close')"
                class="p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                <FontAwesomeIcon icon="xmark" />
            </button>
        </div>

        <div class="px-6 pt-4 border-b border-gray-200">
            <nav class="hidden md:flex overflow-x-auto pb-px">
                <button v-for="step in STEP_DEFINITIONS" :key="step.id" type="button"
                    @click="goToStep(step.id)"
                    :disabled="!isStepAccessible(step.id) && !isStepCompleted(step.id)"
                    class="flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition disabled:opacity-40 disabled:cursor-not-allowed"
                    :class="{
                        'border-blue-600 text-blue-600': getStepState(step.id) === 'active',
                        'border-green-500 text-green-600': getStepState(step.id) === 'completed',
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': getStepState(step.id) === 'accessible',
                        'border-transparent text-gray-300': getStepState(step.id) === 'locked',
                    }">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold"
                        :class="{
                            'bg-blue-600 text-white': getStepState(step.id) === 'active',
                            'bg-green-500 text-white': getStepState(step.id) === 'completed',
                            'bg-gray-200 text-gray-600': getStepState(step.id) === 'accessible',
                            'bg-gray-100 text-gray-400': getStepState(step.id) === 'locked',
                        }">
                        <FontAwesomeIcon v-if="isStepCompleted(step.id)" icon="check" class="text-xs" />
                        <span v-else>{{ step.id }}</span>
                    </span>

                    <span>{{ step.name }}</span>

                    <FontAwesomeIcon v-if="!isStepAccessible(step.id) && !isStepCompleted(step.id)"
                        icon="lock" class="text-xs text-gray-300" />
                </button>
            </nav>

            <nav class="md:hidden max-h-48 overflow-y-auto -mx-2 px-2">
                <button v-for="step in STEP_DEFINITIONS" :key="step.id" type="button"
                    @click="goToStep(step.id)"
                    :disabled="!isStepAccessible(step.id) && !isStepCompleted(step.id)"
                    class="w-full flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition disabled:opacity-40 disabled:cursor-not-allowed text-left"
                    :class="{
                        'bg-blue-50 text-blue-700': getStepState(step.id) === 'active' || getStepState(step.id) === 'completed',
                        'text-gray-600 hover:bg-gray-50': getStepState(step.id) === 'accessible',
                        'text-gray-300': getStepState(step.id) === 'locked',
                    }">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold flex-shrink-0"
                        :class="{
                            'bg-blue-600 text-white': getStepState(step.id) === 'active',
                            'bg-green-500 text-white': getStepState(step.id) === 'completed',
                            'bg-gray-200 text-gray-600': getStepState(step.id) === 'accessible',
                            'bg-gray-100 text-gray-400': getStepState(step.id) === 'locked',
                        }">
                        <FontAwesomeIcon v-if="isStepCompleted(step.id)" icon="check" class="text-xs" />
                        <span v-else>{{ step.id }}</span>
                    </span>

                    <span class="flex-1">{{ step.name }}</span>

                    <FontAwesomeIcon v-if="!isStepAccessible(step.id) && !isStepCompleted(step.id)"
                        icon="lock" class="text-xs text-gray-300 flex-shrink-0" />
                </button>
            </nav>
        </div>

        <div class="flex-1 overflow-y-auto px-6 py-6">

            <div v-if="activeStep === 1" class="space-y-6">
                <div class="bg-white border rounded-xl p-5 shadow-sm space-y-4">
                    <h3 class="text-base font-semibold flex items-center gap-2">
                        <FontAwesomeIcon icon="cog" class="text-blue-600" />
                        Generation Configuration
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">



                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Novel Continuity <span class="text-red-500">*</span>
                            </label>

                            <InfiniteScrollApiSelect :form="step1Form" fieldName="novel_continuity"
                                :selectedItem="step1Form.novel_continuity" :apiUrl="route('search.novel-continuities')"
                                :multiple="false" placeholder="Select continuity"
                                :error="step1Form.errors.novel_continuity" />

                            <p v-if="step1Form.errors.novel_continuity" class="text-red-500 text-sm mt-1">
                                {{ step1Form.errors.novel_continuity }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Language <span class="text-red-500">*</span>
                            </label>

                            <InfiniteScrollApiSelect :form="step1Form" fieldName="language_id"
                                :selectedItem="step1Form.language_id" :apiUrl="route('search.languages')"
                                :multiple="false" placeholder="Select languages" />

                            <p v-if="step1Form.errors.language_id" class="text-red-500 text-sm mt-1">
                                {{ step1Form.errors.language_id }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Genres <span class="text-red-500">*</span>
                            </label>

                            <InfiniteScrollApiSelect :form="step1Form" fieldName="genre_ids"
                                :selectedItem="step1Form.genre_ids" :apiUrl="route('search.genres')"
                                :multiple="true" placeholder="Select genres" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Novel Types <span class="text-red-500">*</span>
                            </label>

                            <InfiniteScrollApiSelect :form="step1Form" fieldName="novel_type_ids"
                                :selectedItem="step1Form.novel_type_ids" :apiUrl="route('search.novel-types')"
                                :multiple="true" placeholder="Select novel types" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Audiences
                            </label>

                            <InfiniteScrollApiSelect :form="step1Form" fieldName="audience_ids"
                                :selectedItem="step1Form.audience_ids" :apiUrl="route('search.audiences')"
                                :multiple="true" placeholder="Select audiences" />
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1">
                                Additional Information
                            </label>

                            <textarea v-model="step1Form.additional_information" rows="3"
                                placeholder="Any additional context or instructions for the AI..."
                                class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none border-gray-300"></textarea>
                        </div>

                    </div>

                    <div class="flex flex-wrap gap-6 pt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" v-model="step1Form.is_18_plus"
                                class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500" />
                            <span class="text-sm">18+ Content</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer"
                            :class="{ 'opacity-50': !step1Form.is_18_plus }">
                            <input type="checkbox" v-model="step1Form.enable_mature_content"
                                :disabled="!step1Form.is_18_plus"
                                class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500" />
                            <span class="text-sm">Enable Mature Content</span>
                        </label>

                        <p v-if="step1Form.errors.enable_mature_content" class="text-red-500 text-sm w-full">
                            {{ step1Form.errors.enable_mature_content }}
                        </p>
                    </div>
                </div>

                <div class="bg-white border rounded-xl p-5 shadow-sm space-y-4">
                    <h3 class="text-base font-semibold flex items-center gap-2">
                        <FontAwesomeIcon icon="brain" class="text-purple-600" />
                        AI Brain Configuration
                    </h3>

                    <p class="text-sm text-gray-500">
                        Select the AI model that will be used for generating your novel.
                    </p>

                    <div class="border-2 border-dashed border-purple-200 rounded-xl p-4 bg-gradient-to-br from-purple-50 to-blue-50">
                        <InfiniteScrollApiSelect :form="step1Form" fieldName="ai_brain_id"
                            :selectedItem="step1Form.ai_brain_id" :apiUrl="route('search.ai-brains')"
                            :multiple="false" placeholder="Select AI Brain"
                            :error="step1Form.errors.ai_brain_id"
                            class="ai-brain-select" />
                    </div>

                    <p v-if="step1Form.errors.ai_brain_id" class="text-red-500 text-sm">
                        {{ step1Form.errors.ai_brain_id }}
                    </p>
                </div>
            </div>

            <div v-if="activeStep === 2" class="flex items-center justify-center h-64">
                <div class="text-center space-y-3">
                    <FontAwesomeIcon icon="globe" class="text-4xl text-gray-300" />
                    <p class="text-gray-400 text-sm">World Building - Coming Soon</p>
                </div>
            </div>

            <div v-if="activeStep === 3" class="flex items-center justify-center h-64">
                <div class="text-center space-y-3">
                    <FontAwesomeIcon icon="user" class="text-4xl text-gray-300" />
                    <p class="text-gray-400 text-sm">Characters - Coming Soon</p>
                </div>
            </div>

            <div v-if="activeStep === 4" class="flex items-center justify-center h-64">
                <div class="text-center space-y-3">
                    <FontAwesomeIcon icon="book" class="text-4xl text-gray-300" />
                    <p class="text-gray-400 text-sm">Plot & Outline - Coming Soon</p>
                </div>
            </div>

            <div v-if="activeStep === 5" class="flex items-center justify-center h-64">
                <div class="text-center space-y-3">
                    <FontAwesomeIcon icon="wand-magic-sparkles" class="text-4xl text-gray-300" />
                    <p class="text-gray-400 text-sm">Review & Generate - Coming Soon</p>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 flex justify-between items-center">
            <button type="button" @click="goPrev" :disabled="activeStep === 1"
                class="px-4 py-2 text-sm rounded-md border border-gray-300 hover:bg-gray-50 transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-2">
                <FontAwesomeIcon icon="arrow-left" />
                Previous
            </button>

            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-400">
                    Step {{ activeStep }} of {{ STEP_DEFINITIONS.length }}
                </span>
            </div>

            <div>
                <button v-if="activeStep === 1" type="button" @click="submitStep1"
                    :disabled="step1Form.processing"
                    class="px-5 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-md flex items-center gap-2 transition disabled:opacity-60 disabled:cursor-not-allowed">
                    <FontAwesomeIcon v-if="submittingStep === 1" icon="spinner" spin />
                    <FontAwesomeIcon v-else icon="save" />
                    {{ submittingStep === 1 ? 'Saving...' : 'Save & Continue' }}
                </button>

                <button v-else type="button" @click="goNext"
                    :disabled="!isStepAccessible(activeStep + 1) || activeStep === 5"
                    class="px-5 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-md flex items-center gap-2 transition disabled:opacity-40 disabled:cursor-not-allowed">
                    Next
                    <FontAwesomeIcon icon="arrow-right" />
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.ai-brain-select :deep(.multiselect) {
    min-height: 44px;
}

.ai-brain-select :deep(.multiselect__tags) {
    min-height: 44px;
    padding: 8px 40px 0 12px;
    border-color: #a78bfa;
    border-width: 1px;
    border-radius: 0.5rem;
    background: white;
}

.ai-brain-select :deep(.multiselect__single) {
    padding: 4px 0 0 0;
    margin-bottom: 0;
    color: #1f2937;
}

.ai-brain-select :deep(.multiselect__placeholder) {
    padding: 4px 0 0 0;
    color: #9ca3af;
}

.ai-brain-select :deep(.multiselect__select) {
    height: 44px;
}

.ai-brain-select :deep(.multiselect__content-wrapper) {
    border-color: #a78bfa;
}
</style>
