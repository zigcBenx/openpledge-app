<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: String,
    color: {
        type: String,
        default: "primary",
        validator: (value) =>
            ["primary"].includes(value),
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
    <input
        ref="input"
        class="w-full dark:text-white dark:bg-oil bg-white dark:placeholder-spun-pearl dark:border-mondo p-3 focus:border-spun-pearl border-gray-300 rounded-md"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    >
</template>
