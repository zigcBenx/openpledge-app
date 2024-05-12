<script setup>
  import { reactive, computed, watch } from 'vue';
  import Button from '@/Components/Button.vue';
  import PledgeMethod from './PledgeMethod.vue';
  import FormTypeButtons from './FormTypeButtons.vue';
  import Input from '@/Components/Input.vue';
  import Checkbox from '@/Components/Checkbox.vue';
  import MoneyInput from '@/Components/MoneyInput.vue';
  import { validateEmail } from '@/utils/validateEmail.js';
  import { preventStringInputWithNumber } from '@/utils/preventStringInputWithNumber.js';
  import { validateCreditCard } from '@/utils/validateCreditCard.js';

  const form = reactive({
    type: 'pledge',
    pledgeMethod: 'infinite',
    amount: '',
    name: '',
    cardNumber: {
      value: '',
      error: false
    },
    cvc: {
      value: '',
      error: false
    },
    email: {
      value: '',
      error: false
    }
  });

  const handleEmailValidation = () => form.email.error = !validateEmail(form.email.value);
  const handleCvcValidation = () => form.cvc.error = form.cvc.value.length !== 3;
  const handleCardNumberValidation = () => form.cardNumber.error = !validateCreditCard(form.cardNumber.value);
  
</script>
<template>
  <div class="dark:bg-charcoal-gray bg-seashell mt-4 rounded-md">
    <FormTypeButtons :type="form.type" />
    <PledgeMethod :pledgeMethod="form.pledgeMethod" />

    <div>
      <div class="p-6 flex flex-col gap-6">
        <label class="dark:text-lavender-mist text-oil text-sm">
          Pledge amount
          <MoneyInput 
            v-model="form.amount" 
            inputClass="!bg-transparent" 
            wrapperClass="w-full !bg-transparent mt-2.5 !pl-0 !border-none" 
            icon="dollar" 
            currency="USD"
            required
            iconClass="fill-green" 
          />
          <small class="mt-1.5 block dark:text-spun-pearl text-tundora">Minimum is $25.</small>
        </label>

        <label class="dark:text-lavender-mist text-oil text-sm">
          <p class="mb-2.5">Contact details</p>
          <Input 
            v-model:input="form.email.value" 
            inputClass="w-full !bg-transparent" 
            type="email" 
            placeholder="Email" 
            icon="letter" 
            required
            iconClass="dark:text-spun-pearl text-tundora"
            @onBlur="handleEmailValidation"
          />
          <small v-if="form.email.error" class="mt-1.5 block dark:text-spun-pearl text-tundora">Email is invalid</small>
        </label>

        <div class="flex flex-col gap-2">
          <label class="dark:text-lavender-mist text-oil text-sm">
            <p class="mb-2.5">Payment method</p>
            <Input 
              v-model:input="form.name" 
              required
              inputClass="w-full !bg-transparent" 
              placeholder="Full name on card" 
            />
          </label>
          <div>
            <Input 
              v-model:input="form.cardNumber.value" 
              inputClass="w-full !bg-transparent border !border-slate-gray !focus:border-green" 
              type="payment" 
              maxlength="16" 
              required
              placeholder="Card number"
              @onInput="form.cardNumber.value = preventStringInputWithNumber(form.cardNumber.value)"
              @onBlur="handleCardNumberValidation"
            />
            <small v-if="form.cardNumber.error" class="mt-1.5 block dark:text-spun-pearl text-tundora">Card number is not valid</small>
          </div>
          <div class="flex gap-2">
            <Input inputClass="w-full !bg-transparent" placeholder="MM/YY" />
            <div>
              <Input 
                v-model:input="form.cvc.value" 
                inputClass="w-full !bg-transparent" 
                placeholder="CVC" 
                maxlength="3" 
                required
                @onInput="form.cvc.value = preventStringInputWithNumber(form.cvc.value)"
                @onBlur="handleCvcValidation"      
              />
              <small v-if="form.cvc.error" class="mt-1.5 block dark:text-spun-pearl text-tundora">CVC is invalid</small>
            </div>
          </div>
        </div>

        <label class="dark:text-seashell text-mondos text-sm flex items-center gap-3">
          <Checkbox class="!bg-transparent" /> Save this card for future OpenPledge donations.
        </label>

        <Button size="lg">Pledge This Issue</Button>
      </div>
    </div>
  </div>
</template>@/utils/preventStringInputWithNumber.js