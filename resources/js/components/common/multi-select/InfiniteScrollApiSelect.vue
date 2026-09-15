<script setup>
import { ref, computed, onMounted, nextTick, watch } from "vue"
import Multiselect from "vue-multiselect"
import "vue-multiselect/dist/vue-multiselect.css"

import { fetchFromApi } from '@/composables/useApiClient'
import { apiCacheKey, apiCacheTTL } from '@/composables/useApiCache'

const {
    selectedItem,
    fieldName,
    form,
    apiUrl,
    error,
    multiple,
    debounce,
    placeholder,
    selectedLabelKey,
    selectedValueKey,
    apiLabelKey,
    apiValueKey,
    compactDesign,
    useDarkTheme,
    defaultLabel,
} = defineProps({
    selectedItem: { type: [String, Number, Object, Array], default: null },
    fieldName: { type: String, required: true },
    form: { type: Object, required: true },
    apiUrl: { type: String, required: true },
    error: { type: [String, Boolean], default: null },
    multiple: { type: Boolean, default: false },
    debounce: { type: Number, default: 300 },
    placeholder: { type: String, default: "Select" },
    selectedLabelKey: { type: String, default: "name" },
    selectedValueKey: { type: String, default: "id" },
    apiLabelKey: { type: String, default: "name" },
    apiValueKey: { type: String, default: "id" },
    compactDesign: { type: Boolean, default: false },
    useDarkTheme: { type: Boolean, default: false },
    defaultLabel: { type: String, default: "Select item" },
})

const options = ref([])
const loading = ref(false)
const loadingMore = ref(false)
const page = ref(1)
const lastPage = ref(1)
const searchQuery = ref("")
const vselectRef = ref(null)
const proxyModel = ref(multiple ? [] : null)

let searchTimeout = null

const getCacheParamsKey = (params = {}) => {
    return new URLSearchParams(
        Object.entries(params)
            .map(([key, value]) => {
                const resolvedValue = typeof value === 'function'
                    ? value()
                    : value

                return [key, resolvedValue]
            })
            .filter(([, value]) => value !== undefined && value !== null)
            .sort(([a], [b]) => a.localeCompare(b))
    ).toString()
}

const getMultiSelectCacheOptions = (params = {}) => {
    const cacheParamsKey = getCacheParamsKey(params)

    return {
        key: cacheParamsKey
            ? `${apiCacheKey.API_MULTI_SELECT}:${apiUrl}:${cacheParamsKey}`
            : `${apiCacheKey.API_MULTI_SELECT}:${apiUrl}`,
        ttl: apiCacheTTL.API_MULTI_SELECT,
    }
}

const getLabel = item => item?.[apiLabelKey] ?? defaultLabel
const getValue = item => item?.[apiValueKey] ?? null

const formattedOptions = computed(() =>
    options.value.map(item => ({
        label: getLabel(item),
        value: getValue(item),
        raw: item,
    }))
)

const normalizeItems = raw =>
    !raw ? [] : Array.isArray(raw) ? raw : Object.values(raw)

const valuesDiffer = (a, b) => {
    if (Array.isArray(a) && Array.isArray(b)) {
        return a.length !== b.length || a.some((value, index) => value !== b[index])
    }

    return a !== b
}

const updateForm = val => {
    if (!form || !fieldName) return

    let next

    if (multiple) {
        next = Array.isArray(val)
            ? val.map(v => v?.raw?.[selectedValueKey] ?? v?.value ?? v)
            : []
    } else {
        next = val
            ? val?.raw?.[selectedValueKey] ?? val?.value ?? val
            : null
    }

    if (valuesDiffer(form[fieldName], next)) {
        form[fieldName] = next
    }
}

const normalizeItem = async item => {
    if (!item) return multiple ? [] : null

    if (typeof item === "object") {
        if (multiple && Array.isArray(item)) {
            return item.map(v => ({
                label: v?.[selectedLabelKey] ?? v?.[apiLabelKey] ?? defaultLabel,
                value: v?.[selectedValueKey] ?? v?.[apiValueKey] ?? null,
                raw: v,
            }))
        }

        return {
            label: item?.[selectedLabelKey] ?? item?.[apiLabelKey] ?? defaultLabel,
            value: item?.[selectedValueKey] ?? item?.[apiValueKey] ?? null,
            raw: item,
        }
    }

    return await fetchItemByValue(item)
}

const formatItem = item => ({
    label: item?.[selectedLabelKey] ?? item?.[apiLabelKey] ?? defaultLabel,
    value: item?.[selectedValueKey] ?? item?.[apiValueKey] ?? null,
    raw: item,
})

const fetchItemByValue = async value => {
    if (value === null || value === undefined || value === '') {
        return multiple ? [] : null
    }

    const loadedMatch = options.value.find(item => item?.[apiValueKey] == value)

    if (loadedMatch) {
        return formatItem(loadedMatch)
    }

    let found = null
    let p = 1
    let totalPages = 1

    do {
        const params = { page: p }
        const data = await fetchFromApi(apiUrl, params, getMultiSelectCacheOptions(params))

        const items = normalizeItems(data?.items)

        totalPages = data?.last_page || 1
        found = items.find(item => item?.[apiValueKey] == value)

        if (found) break

        p++
    } while (p <= totalPages)

    if (!found) return multiple ? [] : null

    return formatItem(found)
}

const fetchPage = async (pageNumber = 1, reset = false) => {
    if (loading.value || loadingMore.value) return
    if (!reset && pageNumber > lastPage.value) return

    const dropdown =
        vselectRef.value?.$el?.querySelector(".multiselect__content-wrapper")

    const scrollTop = dropdown ? dropdown.scrollTop : 0

    reset ? loading.value = true : loadingMore.value = true

    try {
        const params = {
            search: searchQuery.value,
            page: pageNumber,
        }
        const data = await fetchFromApi(apiUrl, params, getMultiSelectCacheOptions(params))

        const items = normalizeItems(data?.items)

        lastPage.value = data?.last_page || 1

        options.value = reset ? items : [...options.value, ...items]
    } finally {
        loading.value = false
        loadingMore.value = false

        nextTick(() => {
            if (dropdown && !reset) {
                dropdown.scrollTop = scrollTop
            }
        })
    }
}

const resetAndFetch = async () => {
    options.value = []
    proxyModel.value = multiple ? [] : null
    searchQuery.value = ""
    page.value = 1
    lastPage.value = 1

    updateForm(proxyModel.value)

    await fetchPage(1, true)
}

watch(proxyModel, val => {
    updateForm(val)
}, { deep: true })

watch(
    () => apiUrl,
    async () => {
        await resetAndFetch()
    }
)

const applySelectedItem = async newValue => {
    const current = !multiple
        ? proxyModel.value?.value ?? null
        : Array.isArray(proxyModel.value)
            ? proxyModel.value.map(item => item?.value ?? null)
            : []

    if (!valuesDiffer(current, newValue)) return

    const normalized = await normalizeItem(newValue)

    const sourceEmpty = multiple
        ? !Array.isArray(newValue) || newValue.length === 0
        : newValue === null || newValue === undefined || newValue === ''

    const resolvedEmpty = multiple
        ? !Array.isArray(normalized) || normalized.length === 0
        : normalized === null

    if (!sourceEmpty && resolvedEmpty) return

    proxyModel.value = normalized

    updateForm(proxyModel.value)
}

watch(
    () => selectedItem,
    newValue => {
        applySelectedItem(newValue)
    },
    { deep: true }
)

const onSearchDebounced = search => {
    searchQuery.value = search

    if (searchTimeout) clearTimeout(searchTimeout)

    searchTimeout = setTimeout(() => {
        page.value = 1
        lastPage.value = 1
        fetchPage(1, true)
    }, debounce)
}

const loadMoreManual = async () => {
    if (loading.value || loadingMore.value) return
    if (page.value >= lastPage.value) return

    const nextPage = page.value + 1

    await fetchPage(nextPage, false)

    page.value = nextPage
}

const handleScroll = e => {
    const el = e.target

    if (
        el.scrollTop + el.clientHeight >= el.scrollHeight - 20 &&
        !loading.value &&
        !loadingMore.value &&
        page.value < lastPage.value
    ) {
        loadMoreManual()
    }
}

const onDropdownOpen = () => {
    nextTick(() => {
        const dropdown =
            vselectRef.value?.$el?.querySelector(".multiselect__content-wrapper")

        if (!dropdown) return

        dropdown.removeEventListener("scroll", handleScroll)
        dropdown.addEventListener("scroll", handleScroll)
    })
}

onMounted(async () => {
    await applySelectedItem(selectedItem)

    await fetchPage(1, true)
})
</script>

<template>
    <div class="multi-select-cointainer" :class="[
        error ? 'border border-red-500 rounded-md' : '',
        compactDesign ? 'multi-select-compact' : '',
        useDarkTheme ? 'multi-select-dark' : ''
    ]">
        <Multiselect ref="vselectRef" v-model="proxyModel" :options="formattedOptions" :multiple="multiple"
            :loading="loading" :searchable="true" :clear-on-select="!multiple" :close-on-select="!multiple"
            :placeholder="placeholder" label="label" track-by="value" @search-change="onSearchDebounced"
            @open="onDropdownOpen">
            <template #option="{ option }">
                <span>{{ option?.label ?? defaultLabel }}</span>
            </template>

            <template #singleLabel="{ option }">
                <span>{{ option?.label ?? defaultLabel }}</span>
            </template>

            <template #tag="{ option, remove }">
                <span class="multiselect__tag">
                    <span>{{ option?.label ?? defaultLabel }}</span>
                    <i class="multiselect__tag-icon" @click="remove(option)"></i>
                </span>
            </template>

            <template #afterList>
                <div v-if="loadingMore" class="text-center py-2 text-xs text-gray-400">
                    Loading...
                </div>
                <div v-else class="text-center py-1 text-xs text-gray-400">
                    Page {{ page }} / {{ lastPage }}
                </div>
            </template>
        </Multiselect>
    </div>
</template>

<style scoped>
.multi-select-compact :deep(.multiselect) {
    min-height: 31px;
    font-size: 13px;
}

.multi-select-compact :deep(.multiselect__select) {
    height: 31px;
    padding: 4px 8px;
}

.multi-select-compact :deep(.multiselect__tags) {
    min-height: 31px;
    padding: 4px 32px 0 8px;
    font-size: 13px;
}

.multi-select-compact :deep(.multiselect__single) {
    margin-bottom: 0;
    padding-top: 2px;
    font-size: 13px;
}

.multi-select-compact :deep(.multiselect__input) {
    font-size: 13px;
    margin-bottom: 0;
    padding: 0;
}

.multi-select-compact :deep(.multiselect__placeholder) {
    margin-bottom: 0;
    padding-top: 2px;
    font-size: 13px;
}

.multi-select-compact :deep(.multiselect__option) {
    min-height: 30px;
    padding: 6px 10px;
    font-size: 13px;
}

.multi-select-compact :deep(.multiselect__tag) {
    margin-bottom: 2px;
    padding: 3px 22px 3px 8px;
    font-size: 12px;
}

.multi-select-dark :deep(.multiselect) {
    color: #e5e7eb;
}

.multi-select-dark :deep(.multiselect__tags) {
    background: #111827;
    border-color: #374151;
    color: #e5e7eb;
}

.multi-select-dark :deep(.multiselect__single) {
    background: transparent;
    color: #e5e7eb;
}

.multi-select-dark :deep(.multiselect__input) {
    background: transparent;
    color: #e5e7eb;
}

.multi-select-dark :deep(.multiselect__placeholder) {
    color: #9ca3af;
}

.multi-select-dark :deep(.multiselect__content-wrapper) {
    background: #111827;
    border-color: #374151;
}

.multi-select-dark :deep(.multiselect__option) {
    background: #111827;
    color: #e5e7eb;
}

.multi-select-dark :deep(.multiselect__option--highlight) {
    background: #1f2937;
    color: #ffffff;
}

.multi-select-dark :deep(.multiselect__option--selected) {
    background: #374151;
    color: #ffffff;
}

.multi-select-dark :deep(.multiselect__tag) {
    background: #374151;
    color: #ffffff;
}

.multi-select-dark :deep(.multiselect__tag-icon::after) {
    color: #ffffff;
}

.multi-select-dark :deep(.multiselect__spinner) {
    background: #111827;
}

:deep(.multiselect__content-wrapper) {
    max-height: 250px;
    overflow-y: auto;
}

:deep(.multiselect__option::after) {
    display: none !important;
    content: none !important;
}

:deep(.multiselect__option--highlight::after) {
    display: none !important;
    content: none !important;
}

:deep(.multiselect__option--selected::after) {
    display: none !important;
    content: none !important;
}
</style>
