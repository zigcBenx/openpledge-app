<template>
    <div v-if="loadingStripe" class="flex justify-center items-center h-full mt-4">
        <div class="flex items-center space-x-2">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <div>Payment form is being loaded...</div>
        </div>
    </div>
    <div class="row mt-4" :class="{'hidden': loadingStripe}">
      <div class="col">
        <div id="payment-element"></div>
        <span v-if="stripeStatusMessage" class="text-red">{{ stripeStatusMessage }}</span>
      </div>
    </div>
    <div class="flex flex-row justify-end px-6 py-4 text-end">
        <SecondaryButton @click="$emit('cancel')">
            Cancel
        </SecondaryButton>

        <PrimaryButton
                v-if="!loadingStripe"
                type="submit"
                class="ms-3"
                @click="handleSubmit"
                :loading="loadingPayment"
        >
            Pay 💸
        </PrimaryButton>
    </div>

    <p v-if="stipeLoadError" class="text-red">{{ stipeLoadError }}</p>
</template>

<script>
import { loadStripe } from '@stripe/stripe-js'
import { useToast } from "vue-toastification";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { useDark, useToggle } from '@vueuse/core';

export default {
  name: 'StripePayment',
  props: {
    donation: {
      type: Number,
      default: 0
    }
  },
  components: { PrimaryButton, SecondaryButton },
  data () {
    return {
      // cards: [
      //   { id: '1', number: '4567' },
      //   { id: '2', number: '8912' },
      //   { id: '3', number: '3456' }
      // ],
      // paymentProcessing: false,
      // stripe: null,
      // cardElement: {
      //   number: null,
      //   cvc: null,
      //   expDate: null
      // },
      stripeKey: import.meta.env.VITE_STRIPE_KEY,
      elements: null,
      clientSecret: '',
      stripeStatusMessage: '',
      loadingStripe: true,
      loadingPayment: false,
      stipeLoadError: '',
      isDark: useDark(),
    }
  },
  async mounted () {
    console.log(import.meta.env.VITE_STRIPE_KEY)
    this.stripe = await loadStripe(import.meta.env.VITE_STRIPE_KEY)
    this.initialize()
  },

  methods: {
    async initialize () {
        await this.fetchPaymentIntent()
        const appearance = {
            theme: 'stripe',
            labels: 'floating',
            rules: {
                '.Input': {
                    backgroundColor: '#fff',
                    border: '2px solid #fff',
                }
            } // TODO: customize payment form design https://stripe.com/docs/elements/appearance-api?platform=web#detail-rules
        }
        this.elements = this.stripe.elements({ clientSecret: this.clientSecret, appearance })

        const paymentElement = this.elements.create('payment')
        paymentElement.mount('#payment-element')
        this.loadingStripe = false
    },
    async fetchPaymentIntent () {
        const toast = useToast()
      await axios.post('/get-payment-intent', { donation: this.donation }).then((response) => {
        this.clientSecret = response.data.clientSecret
      }).catch((exception) => {
        this.stipeLoadError = exception.message

        toast.error('Something went wrong!')
      })
    },
    async handleSubmit () {
      this.loadingPayment = true
      const elements = this.elements
      const { error, paymentIntent } = await this.stripe.confirmPayment({
        elements,
        confirmParams: {
          return_url: window.location.origin + '?success'
        },
        redirect: "if_required",
      })
      // This point will only be reached if there is an immediate error when
      // confirming the payment. Otherwise, your customer will be redirected to
      // your `return_url`. For some payment methods like iDEAL, your customer will
      // be redirected to an intermediate site first to authorize the payment, then
      // redirected to the `return_url`.
        if (error && (error.type === 'card_error' || error.type === 'validation_error')) {
            this.stripeStatusMessage = error.message
        }else if (paymentIntent && paymentIntent.status === "succeeded") {
            this.$emit('success')
        } else {
            this.stripeStatusMessage = 'An unexpected error occured.'
        }

      this.loadingPayment = false
    }
  }
}
</script>