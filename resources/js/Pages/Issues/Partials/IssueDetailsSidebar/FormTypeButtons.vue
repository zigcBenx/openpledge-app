<script setup>
  import Button from '@/Components/Button.vue';

  defineProps({
    type: {
      type: String
    }
  });
</script>
<template>
  <div class="flex p-6 gap-4 border-b dark:border-oil border-lavender-mist">
    <Button 
      size="lg" 
      :buttonClass="{ 
        '!dark:text-green !text-dark-green': type === 'pledge',
        'bg-transparent border-none !hover:border-2 hover:border-solid border-2 dark:text-spun-pearl text-tundora': type !== 'pledge'
      }"
      :color="type === 'pledge' ? 'primary' : 'secondary'"
      @click="type = 'pledge'"
    >
      Pledge
    </Button>
    <Button 
      size="lg" 
      :buttonClass="{ 
        '!dark:text-green !text-dark-green': type === 'solve',
        'bg-transparent border-none !hover:border-2 hover:border-solid border-2 dark:text-spun-pearl text-tundora': type !== 'solve'
      }"
      :color="type === 'solve' ? 'primary' : 'secondary'"
      @click="type = 'solve'"
    >
      Solve
    </Button>
  </div>
</template>