<template>
    <div class="relative w-full inline-block">
        <!-- Textarea with v-model compatibility -->
        <textarea :value="modelValue" @input="$emit('update:modelValue', $event.target.value)"
            :placeholder="placeholder" :class="[
                `w-80 pl-3.5 resize-none placeholder-spun-pearl dark:text-lavender-mist h-32 focus:ring-0 dark:bg-oil bg-lavender-mist dark:focus:border-green focus:border-green rounded-md transition-all`,
                textAreaClass
            ]" :maxlength="maxlength"></textarea>
    </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    modelValue: String,
    placeholder: String,
    maxlength: String,
    textAreaClass: String,
});

const emit = defineEmits(['update:modelValue']);
</script>
