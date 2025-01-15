<script setup>
import { ref, watch } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useToast } from 'vue-toastification';
import { usePage } from '@inertiajs/vue3';
import Button from '@/Components/Button.vue';
import CountrySelect from '@/Components/Custom/CountrySelect.vue';

const toast = useToast();
const stripeUrl = ref('');
const isRedirecting = ref(false);
const isConnected = ref(false);
const country = ref(null);

watch(country, () => {
  connectStripe(country.value.code);
});

onMounted(() => {
  if (usePage().props.auth.user.stripe_id) {
      isConnected.value = true;
    }
});

const connectStripe = async (countryCode) => {
  try {
    const createAccountLinkResponse = await axios.post(route('stripe.create.account.link'), { country_code: countryCode });
    isConnected.value = createAccountLinkResponse.data.isConnected;
    stripeUrl.value = createAccountLinkResponse.data.url;

    if (!isConnected.value) {
      setTimeout(() => {
        isRedirecting.value = true;
        window.location.href = stripeUrl.value;
      }, 4500);
    }
  } catch (error) {
    toast.error('Something went wrong!')
    console.error(error);
  }
};

const openStripeDashboard = async () => {
  isRedirecting.value = true;
  const response = await axios.get(route('stripe.dashboard.link'));
  window.open(response.data.url, '_blank');
  isRedirecting.value = false;
};
</script>

<template>
  <AppLayout title="Connect Stripe">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Connect Stripe
      </h2>
    </template>

    <div :class="{ 'opacity-50': isRedirecting }" class="pt-12 pb-4">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-center dark:text-white shadow-xl sm:rounded-lg h-fit">
          <div class="w-full p-4 border border-gray-200 rounded-lg shadow sm:p-8 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
              <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white">Connect Stripe</h5>
            </div>
            <div v-if="isConnected">
              <p class="text-gray-500 mb-4">Your Stripe account is connected successfully!</p>
              <Button @click="openStripeDashboard" :disabled="isRedirecting">Open Stripe Dashboard</Button>
            </div>
            <div v-else>
              <div v-if="!country" class="pt-4 pb-8">
                <h2 class="leading-none text-gray-900 dark:text-white mb-8">
                  Which country do you operate in?
                </h2>
                <CountrySelect v-model="country" />
              </div>
              <div v-else>
                <p class="text-gray-500 mb-4">
                  You are being redirected to Stripe for onboarding.<br>During this process, you will be asked to provide some personal and business information, such as your name, business name, and bank account details.<br>Please follow the instructions on the screen to connect your account. <br>Once completed, Stripe will automatically redirect you back to our app.
                </p>
                <template v-if="!isConnected">
                  <svg class="animate-spin" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle opacity="0.2" cx="12" cy="12" r="6" stroke="#FCFCFD" stroke-width="2"/>
                    <path d="M18 12C18 8.68629 15.3137 6 12 6" stroke="#FCFCFD" stroke-width="2" stroke-linecap="round"/>
                  </svg>
                </template>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
