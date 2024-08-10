<template>
  <div class="flex gap-3.5">
    <Avatar :url="userImageUrl" size="lg" />
    <div class="flex flex-col">
      <span class="text-mondo dark:text-seashell mb-1.5 text-sm">{{ userName }}</span>
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
    userName: {
      type: [Text, String]
    },
    userImageUrl: {
      type: [Text, String]
    },
    languages: {
      type: Array,
      default: () => {
        return []
      }
    }
  })
</script>