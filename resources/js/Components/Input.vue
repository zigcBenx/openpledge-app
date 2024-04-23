<template>
  <div class="relative w-full inline-block">
      <div v-if="icon && iconPosition === 'left'" class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
          <Icon :name="icon" :class="iconClass" />
      </div>
      <input 
        :type="type" 
        :placeholder="placeholder" 
        :class="[`w-80 pl-3.5 search-cancel:appearance-none search-cancel:w-6 search-cancel:h-6 search-cancel:bg-[url('/images/close.svg')] placeholder-spun-pearl dark:text-lavender-mist h-12 focus:ring-0 dark:bg-oil bg-lavender-mist dark:focus:border-green focus:border-green rounded-md transition-all`, {
          'ps-11': icon && iconPosition === 'left',
          'pr-12': icon && iconPosition === 'right'
        }, inputClass]"
      />
      <div v-if="icon && iconPosition === 'right'" class="z-10 absolute inset-y-0 right-3 flex items-center pointer-events-none">
          <Icon :name="icon" :class="iconClass" />
      </div>
  </div>
</template>
<script setup>
  import Icon from '@/Components/Icon.vue';

  const props = defineProps({
    inputClass: {
      type: String
    },
    iconClass: {
      type: String
    },
    placeholder: {
      type: String
    },
    icon: {
      type: String
    },
    iconPosition: {
      type: String,
      default: 'left',
      validator: (value) =>
        ["left", "right"].includes(value),
    },
    type: {
      type: String
    }
  });
</script>