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
            v-model:input="form.amount" 
            inputClass="!bg-transparent" 
            wrapperClass="w-full !bg-transparent mt-2.5 !pl-0 !border-none" 
            icon="dollar" 
            currency="USD"
            required
            iconClass="fill-green"
            @onInput="form.amount = preventStringInputWithNumber(form.amount)"
          />
          <small v-if="form.amount < minAmount" :class="classes.error">Minimum is {{ minAmount }}.</small>
          <small v-if="form.errors?.amount" :class="classes.error">{{form.errors?.amount[0]}}</small>
        </label>

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
              :loading="loading" 
              :disabled="!form.pledgeExpirationDate?.value && (form.pledgeMethod === PAYMENT_FORM_METHODS.EXPIRE_DATE)" 
              class="mt-8" 
              :plain="true" 
              size="lg" 
              color="primary" 
              @click="handleFormSubmit()"
            >
              Pledge This Issue
            </Button>
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
import { preventStringInputWithNumber } from '@/utils/preventStringInputWithNumber.js';
import { PAYMENT_FORM_METHODS } from '@/constants';
import dayjs from '@/libs/dayjs.js'
import SolveIssue from './SolveIssue.vue';
import { ref, onMounted } from "vue"
import { useDark, useDebounce } from '@vueuse/core';
import { useToast } from 'vue-toastification';
import { router } from '@inertiajs/vue3'
import { validateEmail } from '@/utils/validateEmail.js';
import DatePicker from '@/Components/Form/DatePicker.vue';
import confetti from 'canvas-confetti';

const stripe = ref(null);
const elements = ref(null);
const paymentId = ref(null);
const isDark = useDark();
const toast = useToast()

const form = reactive({
  type: 'pledge',
  pledgeMethod: PAYMENT_FORM_METHODS.INFINITE,
  pledgeExpirationDate: {
    value: '',
    error: false
  },
  pledgeExpirationYear: '',
  amount: '',
  cardSave: false,
  email: '',
  paymentId: '',
  errors: {}
});

onMounted(() => {
  if (props.isAuthenticated) {
    paymentIntent();
  }
});

const debouncedPaymentIntent = ref(useDebounce(() => {
  if (form.email && validateEmail(form.email)) {
    paymentIntent();
  }
}, 500));

watch(() => form.email, (newEmail) => {
  if (!props.isAuthenticated && newEmail) {
    debouncedPaymentIntent.value;
  }
});

const props = defineProps({
  'minAmount': String,
  issue: Object,
  stripePublicKey: String,
  isAuthenticated: Boolean
});

const paymentIntent = () => {
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
        '.Input--empty': {
          boxShadow: 'none',
          border: '1px solid rgb(172 168 179)'
        },
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
  }).catch(error => {
    console.log(error);
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

  if (form.pledgeExpirationDate.value) {
    const date = dayjs(form.pledgeExpirationDate.value);
    form.pledgeExpirationDate = date.format('YYYY-MM-DD');
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
          toast.error('Something went wrong!')
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

            paymentIntent();
            toast.success('Pledge submitted! You are awesome!')
            router.reload();
          }
        }).catch(error => {
          form.errors = error.response?.data?.errors;
        })
        .finally(() => {
          loading.value = false
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
