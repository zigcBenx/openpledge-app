<template>
  <div class="relative w-full inline-block">
      <div v-if="icon && iconPosition === 'left' && type !== 'payment'" class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
          <Icon :name="icon" :class="iconClass" />
      </div>
      <input 
        :value="input || modelValue"
        :type="type" 
        :placeholder="placeholder" 
        :class="[
          `w-80 pl-3.5 search-cancel:appearance-none search-cancel:w-6 search-cancel:h-6 search-cancel:bg-[url('/images/close.svg')] placeholder-spun-pearl dark:text-lavender-mist h-12 focus:ring-0 dark:bg-oil bg-lavender-mist dark:focus:border-green focus:border-green rounded-md transition-all`, 
          { 'ps-11': icon && iconPosition === 'left', 'pr-12': icon && iconPosition === 'right' },
          inputClass
        ]"
        :required="required"
        :maxlength="maxlength"
        @input="handleInput($event)"
        @blur="$emit('onBlur')"
        :disabled="disabled"
      />
      <div v-if="icon && iconPosition === 'right' && type !== 'payment'" class="z-10 absolute inset-y-0 right-3 flex items-center pointer-events-none">
          <Icon :name="icon" :class="iconClass" />
      </div>
      <div v-if="type === 'payment'" class="z-10 absolute inset-y-0 right-3 flex gap-1.5 items-center pointer-events-none !z-0">
        <Icon name="master" />
        <Icon name="visa" />
      </div>
  </div>
</template>

<script setup>
  import Icon from '@/Components/Icon.vue';
  import { defineProps, defineEmits } from 'vue';

  const props = defineProps({
    modelValue: String,
    input: String,
    inputClass: String,
    iconClass: String,
    placeholder: String,
    required: Boolean,
    maxlength: Number,
    icon: String,
    iconPosition: {
      type: String,
      default: 'left',
      validator: (value) => ["left", "right"].includes(value),
    },
    type: String,
    disabled: Boolean
  });

  const emit = defineEmits(['update:modelValue', 'update:input', 'onBlur', 'onInput']);

  const handleInput = (event) => {
    const value = event.target.value;
    emit('update:modelValue', value);
    emit('update:input', value);
    emit('onInput', event);
  };
</script>