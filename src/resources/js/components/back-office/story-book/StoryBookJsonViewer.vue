<script setup>
import { computed } from "vue";

const props = defineProps({
    label: {
        type: String,
        default: null,
    },
    value: {
        type: [Object, Array, String, Number, Boolean],
        default: null,
    },
});

const isPlainObject = computed(
    () =>
        props.value !== null &&
        typeof props.value === "object" &&
        !Array.isArray(props.value),
);

const isArray = computed(() => Array.isArray(props.value));

const entries = computed(() =>
    isPlainObject.value ? Object.entries(props.value) : [],
);

const items = computed(() => (isArray.value ? props.value : []));

const isEmpty = computed(() => {
    if (props.value === null || props.value === undefined || props.value === "") {
        return true;
    }

    if (isPlainObject.value) {
        return entries.value.length === 0;
    }

    if (isArray.value) {
        return items.value.length === 0;
    }

    return false;
});

const humanize = (key) =>
    String(key)
        .replace(/[_-]+/g, " ")
        .replace(/\b\w/g, (character) => character.toUpperCase());
</script>

<template>
    <div v-if="!isEmpty">
        <p v-if="label" class="text-sm font-semibold text-gray-700">
            {{ label }}
        </p>

        <ul
            v-if="isPlainObject"
            class="space-y-2"
            :class="label ? 'mt-2' : ''"
        >
            <li
                v-for="[key, nestedValue] in entries"
                :key="key"
                class="rounded-lg border border-gray-100 bg-gray-50 p-3"
            >
                <StoryBookJsonViewer
                    :label="humanize(key)"
                    :value="nestedValue"
                />
            </li>
        </ul>

        <ul
            v-else-if="isArray"
            class="space-y-2"
            :class="label ? 'mt-2' : ''"
        >
            <li
                v-for="(nestedValue, index) in items"
                :key="index"
                class="rounded-lg border border-gray-100 bg-gray-50 p-3"
            >
                <p class="text-xs font-semibold text-gray-400">
                    {{ index + 1 }}
                </p>

                <StoryBookJsonViewer :value="nestedValue" />
            </li>
        </ul>

        <p
            v-else
            class="text-sm text-gray-700 whitespace-pre-wrap"
            :class="label ? 'mt-1' : ''"
        >
            {{ value }}
        </p>
    </div>
</template>
