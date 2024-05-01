<template>
  <div class="flex gap-3.5">
    <Avatar :url="user.user_avatar" size="lg" />
    <div class="flex flex-col">
      <span class="text-mondo dark:text-seashell mb-1.5 text-sm">{{ user.username }}</span>
      <div class="flex gap-1 flex-wrap">
        <Pill 
          v-for="lang in languages" 
          :key="lang" 
          color="secondary"
          size="sm"
        >
          {{ lang }}  
        </Pill>
      </div>
    </div>
  </div>
</template>
<script setup>
  import Avatar from '@/Components/Avatar.vue';
  import Pill from '@/Components/Form/Pill.vue';

  defineProps({
    user: {
      type: Object
    },
    languages: {
      type: Array
    }
  })
</script>