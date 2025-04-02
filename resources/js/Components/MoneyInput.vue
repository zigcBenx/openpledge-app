<template>
    <div class="relative border-none rounded-md shadow-sm w-full !bg-transparent mt-2.5 !pl-0 !border-none">
      <input
        v-model="inputValue"
        type="text"
        class="w-full pl-5 pr-10 py-2 bg-transparent dark:text-lavender-mist h-12 focus:ring-0 bg-lavender-mist dark:focus:border-green focus:border-green rounded-md transition-all"
        :min="minDonation"
        :max="maxDonation"
        @input="handleInput"
        @blur="formatInput"
      />
      <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none dark:text-spun-pearl text-tundora">
        {{ currency }}
      </span>
      <div v-if="inputValue !== ''" class="text-red-500 text-sm mt-1">
        <p v-if="!isValidInput">
          Donation must be between {{ minDonation }} and {{ maxDonation }} {{ currency }}.
        </p>
      </div>
    </div>
  </template>
  
  <script setup>
import { computed, defineProps, defineEmits, ref, watch } from 'vue';

const props = defineProps({
  modelValue: Number|String,
  minDonation: { type: Number, required: true },
  maxDonation: { type: Number, required: true },
  currency: { type: String, required: true },
});

const emit = defineEmits(['update:modelValue']);
const inputValue = ref(props.modelValue?.toString() ?? '');

// Watch for modelValue changes and update inputValue (without formatting during typing)
watch(
  () => props.modelValue,
  (newValue) => {
    inputValue.value = newValue
  }
);

const isValidInput = computed(() => {
  const value = Number(inputValue.value);
  return value >= props.minDonation && value <= props.maxDonation;
});

const handleInput = (event) => {
  let value = event.target.value.replace(/[^0-9.]/g, '');
  const parts = value.split('.');
  
  // Handle extra decimal points
  if (parts.length > 2) {
    value = parts[0] + '.' + parts.slice(1).join('').replace(/\./g, '');
  }

  if (parts[1]?.length > 2) {
    value = parts[0] + '.' + parts[1].slice(0, 2);
  }

  if (value === '0' || value === '00') {
    value = '';
  }

  inputValue.value = value;

  // Emit the value immediately when the user types
  const numericValue = parseFloat(value);
  if (!isNaN(numericValue) && isValidInput.value) {
    emit('update:modelValue', numericValue);
  }
};

// Format the input when the input field loses focus (on blur)
const formatInput = () => {
  if (inputValue.value === '') return;
  let numericValue = parseFloat(inputValue.value);
  if (isNaN(numericValue)) {
    inputValue.value = '';
  } else {
    inputValue.value = numericValue.toFixed(2); // Format to 2 decimal places
    emit('update:modelValue', numericValue);
  }
};
</script>

  