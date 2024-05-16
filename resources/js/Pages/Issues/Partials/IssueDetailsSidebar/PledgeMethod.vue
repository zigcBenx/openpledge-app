<script setup>
  import Button from '@/Components/Button.vue';
  import Icon from '@/Components/Icon.vue';
  import { PAYMENT_FORM_METHODS } from '@/constants';

  defineProps({
    pledgeMethod: Object
  });

  const emit = defineEmits(['onChange']);

  const classes = {
    'button-block': 'text-left rounded-md p-4 mt-2.5 cursor-pointer hover:dark:bg-gunmetal hover:bg-lavender-mist transition-all',
    'button-block-hover': 'dark:bg-shade-green bg-pale-aqua'
  }

</script>
<template>
  <div class="p-6 border-b dark:border-oil border-lavender-mist">
    <p class="dark:text-lavender-mist text-oil">Pledge method</p>

    <button 
      :class="[classes['button-block'], { [classes['button-block-hover']]: pledgeMethod === PAYMENT_FORM_METHODS.INFINITE }]"
      @click="emit('onChange', PAYMENT_FORM_METHODS.INFINITE)"
    >
      <Icon name="link" class="dark:fill-green fill-dark-green" />
      <h4 class="mb-2.5 mt-3 text-dark-green dark:text-green">Infinite Pledge</h4>
      <p class="text-dark-green dark:text-green">Pledged amount will be linked to this issue untill the issue issolved.</p>
    </button>

    <button
      :class="[classes['button-block'], { [classes['button-block-hover']]: pledgeMethod === PAYMENT_FORM_METHODS.EXPIRE_DATE }]"
      @click="emit('onChange', PAYMENT_FORM_METHODS.EXPIRE_DATE)"
    >
      <Icon name="clock" class="dark:fill-green fill-dark-green" />
      <h4 class="mb-2.5 mt-3 text-dark-green dark:text-green">Pledge with Expiry Date</h4>
      <p class="text-dark-green dark:text-green">After selected period of time, if issue is not solved, you will get a refund.</p>
    </button>
  </div>
</template>