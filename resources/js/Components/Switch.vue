<template>
    <div class="relative inline-block w-14 h-7 align-middle select-none">
        <input type="checkbox" :id="uniqueId" :checked="modelValue"
            @change="$emit('update:modelValue', $event.target.checked)" class="sr-only peer" :disabled="disabled" />
        <label :for="uniqueId" class="flex items-center w-full h-full rounded-full cursor-pointer transition-all duration-200 
             bg-gunmetal peer-checked:bg-green after:content-[''] after:absolute after:top-0.5 after:left-0.5 
             after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all after:duration-200 
             peer-checked:after:translate-x-7 dark:hover:ring-green hover:ring-green hover:ring-2">
        </label>
    </div>
</template>

<script setup>
import { ref, defineProps } from 'vue';

defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    disabled: {
        type: Boolean,
        default: false
    }
});

const uniqueId = ref(`switch-${Math.random().toString(36).slice(2, 11)}`);
</script>