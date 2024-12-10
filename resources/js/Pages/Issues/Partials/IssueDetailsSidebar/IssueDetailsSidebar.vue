<script setup>
  import PledgesHistory from './PledgesHistory.vue';
  import PaymentForm from './PaymentForm.vue';
  import { pledgesHistory } from '@/assets/mockedData.js'

  defineProps({
    issue: Object,
    stripePublicKey: String,
    isAuthenticated: Boolean
  });

  const data = {
    pledges: pledgesHistory
  }
</script>
<template>
  <div class="mb-10">
    <PledgesHistory :issue="issue" />
    <PaymentForm 
      v-if="issue.state !== 'closed'" 
      :issue="issue" 
      :stripePublicKey="stripePublicKey" 
      :isAuthenticated="isAuthenticated" 
    />
  </div>
</template>