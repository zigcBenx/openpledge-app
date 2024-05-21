<template>
  <div :class="['border-grayish dark:border-gunmetal border p-6 rounded-md', containerClass]">
    <h2 class="text-oil dark:text-lavender-mist mb-4 text-xl">{{title}}</h2>
    <p class="text-tundora dark:text-spun-pearl text-xs">{{subtitle}}</p>
    <ul class="flex flex-col gap-4 mt-7">
      <slot></slot>
    </ul>
  </div>
</template>
<script setup>
  defineProps({
    containerClass: {
      type: String
    },
    title: {
      type: String,
      required: true
    },
    subtitle: {
      type: String
    }
  })
</script>