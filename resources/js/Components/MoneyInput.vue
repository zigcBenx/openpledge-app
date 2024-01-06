<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: Number,
    currency: {
        type: String,
        default: 'EUR',
    },
    minDonation: {
        type: Number,
        default: 0,
    },
    maxDonation: {
        type: Number,
        default: Infinity,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <div class="relative border-none rounded-md shadow-sm pl-4 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
        <input
            ref="input"
            class="pr-9 border-none dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 w-full"
            :value="modelValue"
            @input="$emit('update:modelValue', Number($event.target.value))"
            type="number"
            inputmode="decimal"
            step="0.01"
            :min="minDonation"
            :max="maxDonation"
            :disabled="disabled"
        >
        <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
            {{ currency }}
        </span>
    </div>
</template>

<style scoped>
    /* remove arrows on numeric input*/
    input[type="number"] {
        -webkit-appearance: textfield;
        -moz-appearance: textfield;
        appearance: textfield;
    }

    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>