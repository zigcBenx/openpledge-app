<script setup>
  defineProps({
    class: {
      type: String
    },
    url: {
        type: String,
        required: true
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) =>
          ["md", "sm"].includes(value),
    },
    alt: {
      type: String
    }
  });
</script>

<template>
  <img 
    :class="['w-8 h-8 rounded-full object-covers', {
      '!w-4 !h-4': size === 'sm'
    }]" 
    :src="url" 
    :alt="alt || 'Avatar'"
  />
</template>