<script setup>
  import PledgesHistory from './PledgesHistory.vue';
  import PaymentForm from './PaymentForm.vue';
  import { pledgesHistory } from '@/assets/mockedData.js'

  defineProps({
    issue: Object,
    stripePublicKey: String
  });

  const data = {
    pledges: pledgesHistory
  }
</script>
<template>
  <div class="mb-10">
    <PledgesHistory :data="data.pledges" />
    <PaymentForm :issue="issue" :stripePublicKey="stripePublicKey" />
  </div>
</template>