<script setup>
import { onMounted, ref, defineModel, defineEmits } from 'vue';
import Icon from '@/Components/Icon.vue';

const input = defineModel('input');
const emit = defineEmits(['onBlur', 'onInput']);

defineProps({
    inputClass: String,
    wrapperClass: String,
    icon: String,
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

const inputRef = ref(null);

onMounted(() => {
    if (inputRef?.value?.hasAttribute('autofocus')) {
        inputRef.value.focus();
    }
});

defineExpose({ focus: () => inputRef.value.focus() });
</script>

<template>
    <div :class="['relative border-none rounded-md shadow-sm pl-4s', wrapperClass]">
        <div v-if="icon" class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
          <Icon class="dark:text-spun-pearl text-tundora" :name="icon" />
        </div>
        <input
            v-model="input"
            :ref="inputRef"
            :class="['pr-9 placeholder-spun-pearl dark:text-lavender-mist h-12 focus:ring-0 dark:bg-oil bg-lavender-mist dark:focus:border-green focus:border-green rounded-md transition-all w-full', inputClass, {
                'pl-8': icon
            }]"
            @input="emit('onInput')"
            type="number"
            inputmode="decimal"
            step="0.01"
            :min="minDonation"
            :max="maxDonation"
            :disabled="disabled"
        >
        <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none dark:text-spun-pearl text-tundora">
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