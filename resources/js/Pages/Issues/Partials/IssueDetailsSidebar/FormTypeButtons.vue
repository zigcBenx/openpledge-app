<script setup>
  import Button from '@/Components/Button.vue';
import { ref } from 'vue';

  const emit = defineEmits(['change']);

  const props = defineProps({
    type: {
      type: String
    }
  });

  const buttonType = ref(props.type);

  const changeType = (type) => {
    buttonType.value = type;
    emit('change', type);
  }

</script>
<template>
  <div class="flex p-6 gap-4 border-b dark:border-oil border-lavender-mist">
    <Button 
      size="lg" 
      :buttonClass="{ 
        '!dark:text-green !text-dark-green': buttonType === 'pledge',
        'bg-transparent border-none !hover:border-2 hover:border-solid border-2 dark:text-spun-pearl text-tundora': buttonType !== 'pledge'
      }"
      :color="buttonType === 'pledge' ? 'primary' : 'secondary'"
      @click="changeType('pledge')"
    >
      Pledge
    </Button>
    <Button 
      size="lg" 
      :buttonClass="{ 
        '!dark:text-green !text-dark-green': buttonType === 'solve',
        'bg-transparent border-none !hover:ring-2 hover:ring-solid ring-2 dark:text-spun-pearl text-tundora': buttonType !== 'solve'
      }"
      :color="buttonType === 'solve' ? 'primary' : 'secondary'"
      @click="changeType('solve')"
    >
      Solve
    </Button>
  </div>
</template>