<template>
    <div class="relative inline-flex items-center">
        <input type="radio" :id="id || name" :value="value" :name="name" :checked="modelValue === value" @change="handleChange"
            :disabled="disabled" class="appearance-none w-5 h-5 rounded-full border-2 transition-all
          dark:border-spun-pearl border-oil
          dark:bg-oil bg-lavender-mist
          checked:dark:bg-green checked:bg-green
          checked:border-green
          focus:checked:bg-openpledge-yellow
          dark:focus:checked:bg-openpledge-yellow
          disabled:opacity-50 disabled:cursor-not-allowed
          focus:ring-0 focus:ring-offset-0" />
        <label v-if="label" :for="id || name" :class="labelClass" class="ml-2 text-oil dark:text-lavender-mist cursor-pointer select-none">
            {{ label }}
        </label>
    </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    modelValue: [String, Number, Boolean],
    value: {
        type: [String, Number, Boolean],
        required: true
    },
    name: {
        type: String,
        required: true
    },
    id: String,
    label: String,
    disabled: Boolean,
    labelClass: String
});

const emit = defineEmits(['update:modelValue']);

const handleChange = (event) => {
    emit('update:modelValue', props.value);
};
</script>

<style scoped>
input[type="radio"]:checked {
    background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3ccircle cx='8' cy='8' r='3'/%3e%3c/svg%3e");
    background-size: 100% 100%;
    background-position: center;
    background-repeat: no-repeat;
}
</style>
