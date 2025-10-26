<template>
  <div class="dark:bg-charcoal-gray bg-seashell mt-4 rounded-md">
    <FormTypeButtons :type="form.type" @change="updateFormType" />

    <span v-if="form.type === 'solve'">
      <SolveIssue :issue="issue" :isAuthenticated="isAuthenticated" />
    </span>
    <span v-else>
    <PledgeMethod :pledgeMethod="form.pledgeMethod" @onChange="handleMethodChange" />

    <div>
      <div :class="{ 'opacity-60 pointer-events-none': loading }" class="p-6 flex flex-col gap-6">
        <div v-if="form.pledgeMethod === PAYMENT_FORM_METHODS.EXPIRE_DATE">
          <label class="dark:text-lavender-mist text-oil text-sm">
            <p class="mb-2.5">Pledge expiration</p>
            <DatePicker
              v-model="form.pledgeExpirationDate.value"
              :min-date="minExpiryDate"
              :is-open="isDatePickerOpen"
              @update:is-open="isDatePickerOpen = $event"
              placeholder="Select expiry date"
              @update:modelValue="handlePledgeExpireDateValidation"
            />
            <small v-if="form.pledgeExpirationDate.error" :class="classes.error">
              Expiration date must be at least 3 weeks from today.
            </small>
          </label>
        </div>
        <label class="dark:text-lavender-mist text-oil text-sm">
          Pledge amount
          <MoneyInput
            v-model="form.amount"
            class="w-full !bg-transparent mt-2.5 !pl-0 !border-none"
            currency="USD"
            required
            :min-donation="5"
            :max-donation="100000"
          />
        </label>

        <label class="dark:text-lavender-mist text-oil text-sm" v-if="hasCompany">
          <Checkbox v-model:checked="form.shouldBillCompany" />
            <span class="ml-2 text-sm">
              Bill my company
            </span>
        </label>

          <div v-if="netAmount" class="dark:bg-rich-black bg-light-sea-shade rounded-md p-6 text-white">
              <div class="border-0 border-b border-gray-200 mb-4 pb-4 flex justify-between">
                  <p>Service fee</p>
                  <p>15%</p>
              </div>
              <div class="flex justify-between">
                  <p>Pledged amount:</p>
                  <p>{{ netAmount }}$</p>
              </div>
              <div class="mt-4">
                  <Checkbox v-model:checked="coverTransactionCost" />
                  <span class="ml-2 text-xs">
                    Cover the 15% transaction fee, so all of my pledge goes to the contributors.
                  </span>
              </div>
          </div>

        <label class="dark:text-lavender-mist text-oil text-sm" v-if="!isAuthenticated">
          <p class="mb-2.5">Contact details</p>
          <Input
            v-model:input="form.email"
            inputClass="w-full !bg-transparent"
            type="email"
            placeholder="Email"
            icon="letter"
            required
            iconClass="dark:text-spun-pearl text-tundora"
          />
          <small v-if="form.errors?.email" :class="classes.error">{{form.errors?.email[0]}}</small>
        </label>

        <div class="flex flex-col gap-2">
          <label class="dark:text-lavender-mist text-oil text-sm">
            <p class="mb-2.5">Payment method</p>
          </label>
          <form id="payment-form">
            <div id="payment-element">
                <!-- Stripe will create form elements here -->
            </div>
            <Button
              v-if="canReceiveDonations"
              :loading="loading"
              :disabled="!isPledgeAmountValid || !isStripePaymentFormValid"
              class="mt-8"
              :plain="true"
              size="lg"
              color="primary"
              @click="handleFormSubmit()"
            >
              Pledge This Issue
            </Button>

            <div v-else>
              <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md">
                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                  This repository requires issues to have the "Pledgeable" label to receive donations.
                </p>
              </div>
              <Button
                class="mt-4"
                :plain="true"
                size="lg"
                color="primary"
                @click="requestPledgeableLabel()"
              >
                <i class="fa-brands fa-github mr-2"></i>
                Request "Pledgeable" Label
              </Button>
            </div>
            <small v-if="loading" class="text-white">Payment is beeing processed...</small>
          </form>
        </div>

        <p class="text-sm dark:text-spun-pearl text-tundora">
          By adding a pledge to this issue you agree to OpenPledge’s <br />
          <span class="font-medium">Terms & Conditions</span> and <span class="font-medium">Privacy Policy</span>
        </p>
      </div>
    </div>
    </span>
  </div>
</template>
<script setup>
import { reactive, computed, watch } from 'vue';
import Button from '@/Components/Button.vue';
import PledgeMethod from './PledgeMethod.vue';
import FormTypeButtons from './FormTypeButtons.vue';
import Input from '@/Components/Input.vue';
import MoneyInput from '@/Components/MoneyInput.vue';
import { PAYMENT_FORM_METHODS } from '@/constants';
import dayjs from '@/libs/dayjs.js'
import SolveIssue from './SolveIssue.vue';
import { ref } from "vue"
import { useDark, useDebounceFn } from '@vueuse/core';
import { useToast } from 'vue-toastification';
import { router, usePage } from '@inertiajs/vue3'
import { validateEmail } from '@/utils/validateEmail.js';
import DatePicker from '@/Components/Form/DatePicker.vue';
import confetti from 'canvas-confetti';
import Checkbox from "@/Components/Checkbox.vue";

const props = defineProps({
  'minAmount': String,
  issue: Object,
  stripePublicKey: String
});

const stripe = ref(null);
const elements = ref(null);
const paymentId = ref(null);
const isDark = useDark();
const toast = useToast();
const isPledgeAmountValid = ref(false);
const page = usePage();
const isAuthenticated = page.props.auth.user !== null;
const isStripePaymentFormValid = ref(false);
const coverTransactionCost = ref(false);
const originalAmount = ref(null)

// Check if issue can receive donations based on repository settings
const canReceiveDonations = computed(() => {
    const repository = props.issue?.repository;
    if (!repository?.settings || !repository.settings.allowed_labels) {
        return true;
    }

    // Check if repository requires "Pledgeable" label
    if (repository.settings.allowed_labels.includes('Pledgeable')) {
        // Check if issue has the "Pledgeable" label
        const issueLabels = props.issue?.labels?.map(label => label.name) || [];
        return issueLabels.includes('pledgeable');
    }

    return true;
});

const requestPledgeableLabel = () => {
    const repository = props.issue?.repository;
    if (!repository) return;

    const githubUrl = `${props.issue.github_url}`;

    toast.info(`Please comment on this GitHub issue to request the "Pledgeable" label`, {
        timeout: 8000,
        hideProgressBar: false,
        closeButton: true,
        enableHtml: true
    });
};

watch(coverTransactionCost, (newValue) => {
    if (newValue) {
        if (originalAmount.value === null) {
            originalAmount.value = form.amount
        }
        form.amount = (originalAmount.value / 0.85).toFixed(2)
    } else {
        form.amount = originalAmount.value
    }
});

const hasCompany = computed(() => {
  return !!page.props.auth.user?.company;
});

const form = reactive({
  type: 'pledge',
  pledgeMethod: PAYMENT_FORM_METHODS.INFINITE,
  pledgeExpirationDate: {
    value: '',
    error: false
  },
  pledgeExpirationYear: '',
  amount: 0,
  cardSave: false,
  email: '',
  paymentId: '',
  errors: {},
  shouldBillCompany: hasCompany.value
});

const fee = computed(() => {
    return 15
})
const netAmount = computed (() => {
    if (!form.amount) return null
    return (form.amount - form.amount * fee.value * 0.01).toFixed(2)
})

const debouncedPaymentIntent = useDebounceFn(() => {
  paymentIntent();
}, 500);

watch([() => form.amount, () => form.email], ([newAmount, newEmail]) => {
  if (newAmount && newAmount > 0) {
    isPledgeAmountValid.value = true;
  } else {
    document.getElementById('payment-element').innerHTML = '';
    isPledgeAmountValid.value = false;
  }

  if ((isAuthenticated || (newEmail && validateEmail(newEmail))) && isPledgeAmountValid.value) {
    debouncedPaymentIntent();
  }
});

const paymentIntent = () => {
  if (!isPledgeAmountValid.value) {
    return;
  }

  axios.post('/get-payment-intent', {
    amount: form.amount,
    email: form.email
  }).then(response => {
    stripe.value = Stripe(props.stripePublicKey);
    const appearance = {
      theme: isDark.value ? 'night' : 'stripe',
      variables: {
        colorPrimary: isDark.value ? 'rgb(240 239 241)' : 'rgb(24 124 101)',
        colorBackground: isDark.value ? 'rgb(32 31 35)' : 'rgb(252 252 253)',
        colorText: isDark.value ? 'rgb(240 239 241)' : 'rgb(24 124 101)',
        colorDanger: isDark.value ? 'rgb(172 168 179)' : 'rgb(102 97 112)',
        fontFamily: 'Ideal Sans, system-ui, sans-serif',
      },
      rules: {
        '.Input': {
          boxShadow: 'none',
          border: '1px solid rgb(172 168 179)'
        },
        '.Input:hover': {
          border: '1px solid rgb(55 195 162)'
        },
        '.Input--empty': {
          boxShadow: 'none',
          border: '1px solid rgb(55 195 162)'
        },
        '.Input--invalid': {
          boxShadow: 'none',
          border: '1px solid rgb(178 53 212)'
        }
      }
    };
    const options = {
      clientSecret: response.data.clientSecret,
      appearance
    }
    paymentId.value = response.data.paymentId;
    elements.value = stripe.value.elements(options,);
    const paymentElement = elements.value.create('payment');
    paymentElement.mount('#payment-element');
    paymentElement.on('change', (event) => {
      isStripePaymentFormValid.value = event.complete;
    });
  }).catch(error => {
    console.log(error);
    toast.error(error.response.data.message);
  });
};

const classes = {
  error: "mt-1.5 text-xs block dark:text-spun-pearl text-tundora"
}

const handleMethodChange = (value) => form.pledgeMethod = value;
const handlePledgeExpireDateValidation = () => {
  const expirationDate = dayjs(form.pledgeExpirationDate.value);
  form.pledgeExpirationDate.error = !expirationDate.isValid();
  isDatePickerOpen.value = false;
};

const getIsValidInfiniteForm = () => form.amount && !form.name && !form.cardNumber && !form.expireDate && !form.cvc && !form.email && form.country;

const isFormValid = computed(() => {
  if (form.pledgeMethod === PAYMENT_FORM_METHODS.EXPIRE_DATE) {
    return getIsValidInfiniteForm() && !form.pledgeExpirationDate.error && form.pledgeExpirationYear;
  }
  return getIsValidInfiniteForm();
});

const loading = ref(false)
const handleFormSubmit = async () => {
  loading.value = true
  form.issue_id = props.issue.id;
  form.paymentId = paymentId.value;
  const pledgeExpirationDate = form.pledgeExpirationDate;

  if (pledgeExpirationDate.value) {
    const expirationDate = dayjs(pledgeExpirationDate.value);
    form.pledgeExpirationDate = expirationDate.format('YYYY-MM-DD');
  } else {
    form.pledgeExpirationDate = null;
  }

  delete form.pledgeExpirationYear;

  await stripe.value.confirmPayment({
      elements: elements.value,
      redirect: "if_required"
  }).then(function(result) {
      form.paymentId = result.paymentIntent?.id;
      if (result.error) {
          console.error(result.error)
          toast.error(result.error.message)
          loading.value = false
          form.pledgeExpirationDate = pledgeExpirationDate;
      } else {
        axios.post(route('payment-process'), form).then(response => {
          if(response.data.success) {
            form.amount = '';
            form.cardSave = false;
            form.email = '';
            form.paymentId = '';
            form.pledgeExpirationDate = {
              value: '',
              error: false
            };
            form.pledgeExpirationYear = '';

            const animationEnd = Date.now() + 4000;

            const interval = setInterval(function() {
              const timeLeft = animationEnd - Date.now();

              if (timeLeft <= 0) {
                return clearInterval(interval);
              }

              confetti({
                particleCount: 200,
                angle: 90,
                spread: 180,
                origin: { x: 0.5, y: 1 },
                colors: ['#551e5b', '#88f5dc', '#152825'],
                gravity: 0.5,
                scalar: 1.4,
                startVelocity: 80,
                ticks: 200
              });
            }, 150);

            toast.success('Pledge submitted! You are awesome!')
            router.reload();
          }
        }).catch(error => {
          form.errors = error.response?.data?.errors;
        })
        .finally(() => {
          loading.value = false
          coverTransactionCost.value = false
        });
      }
  });
}

watch(isDark, (newVal, oldVal) => {
  if (newVal !== oldVal) {
    paymentIntent();
  }
});

const updateFormType = (value) => {
  form.type = value;
}

const minExpiryDate = dayjs().add(3, 'weeks').toDate();

const isDatePickerOpen = ref(false);
</script>
