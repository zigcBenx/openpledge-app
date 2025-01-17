<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useToast } from 'vue-toastification';
import { usePage } from '@inertiajs/vue3';
import Radio from '@/Components/Form/Radio.vue';
import Button from '@/Components/Button.vue';
import CountrySelect from '@/Components/Custom/CountrySelect.vue';
import { toTitleCase } from '@/utils/toTitleCase';

const StripeBusinessType = {
  Individual: 'individual',
  Company: 'company'
};

const toast = useToast();
const stripeUrl = ref('');
const isRedirecting = ref(false);
const isConnected = ref(false);
const country = ref(null);
const businessType = ref(null);
const isFillingOutForm = ref(true);

onMounted(() => {
  if (usePage().props.auth.user.stripe_id) {
      isConnected.value = true;
    }
});

const connectStripe = async () => {
  isFillingOutForm.value = false;

  try {
    const createAccountLinkResponse = await axios.post(route('stripe.create.account.link'), { 
      country_code: country.value.code, 
      business_type: businessType.value
    });

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
        <div class="flex justify-center dark:text-white sm:rounded-lg h-fit">
          <div class="w-1/2 p-4 border border-gray-200 rounded-lg shadow sm:p-8 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
              <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white">Connect Stripe</h5>
            </div>
            <div v-if="isConnected">
              <p class="text-gray-500 mb-4">Your Stripe account is connected successfully!</p>
              <Button @click="openStripeDashboard" :disabled="isRedirecting">Open Stripe Dashboard</Button>
            </div>
            <div v-else>
              <div v-if="isFillingOutForm" class="pt-4 pb-8">
                <h2 class="leading-none text-gray-900 dark:text-white mb-4">
                  Which country do you operate in?
                </h2>
                <CountrySelect v-model="country" />
                <div class="flex flex-col gap-4 mt-8">
                  <h2 class="leading-none text-gray-900 dark:text-white mb-2">
                    Are you connecting with Stripe as an {{ StripeBusinessType.Individual }} or a {{ StripeBusinessType.Company }}?
                  </h2>
                  <Radio
                    v-model="businessType"
                    :name="StripeBusinessType.Individual"
                    :value="StripeBusinessType.Individual"
                    :label="toTitleCase(StripeBusinessType.Individual)"
                    :id="StripeBusinessType.Individual"
                    labelClass="text-sm"
                  />
                  <Radio
                    v-model="businessType"
                    :name="StripeBusinessType.Company"
                    :value="StripeBusinessType.Company"
                    :label="toTitleCase(StripeBusinessType.Company)"
                    :id="StripeBusinessType.Company"
                    labelClass="text-sm"
                  />
                </div>
                <Button @click="connectStripe" :disabled="!country || !businessType" class="mt-8">Connect</Button>
              </div>
              <div v-else>
                <p class="text-gray-500 mb-4">
                  You are being redirected to Stripe for onboarding.<br>
                  <template v-if="businessType === StripeBusinessType.Individual">
                    During this process, you will be asked to provide some personal information, such as your name, address, and bank account details.
                  </template>
                  <template v-else-if="businessType === StripeBusinessType.Company">
                    During this process, you will be asked to provide some company information, such as your company name, company details, and bank account information.
                  </template>
                  <br>
                  Please follow the instructions on the screen to connect your account. 
                  <br>
                  <span class="font-bold">Once completed, Stripe will automatically redirect you back to our app.</span>
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
