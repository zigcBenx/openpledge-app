<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Button from '@/Components/Button.vue';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import { useDark } from '@vueuse/core';

const isDark = useDark();

defineProps({
  canResetPassword: Boolean,
  status: String,
});

const form = useForm({
  email: '',
  password: '',
  remember: true,
});

const submit = () => {
  form.transform(data => ({
    ...data,
    remember: form.remember ? 'on' : '',
  })).post(route('login'), {
    onFinish: () => form.reset('password'),
  });
};
</script>

<template>
  <Head title="Log in"/>
  <AuthenticationCard>
    <template #logo>
      <AuthenticationCardLogo/>
    </template>

    <div v-if="status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
      {{ status }}
    </div>

    <div class="border-b border-whitish-gray dark:border-oil py-4 mb-7">
      <ApplicationMark :isDark="isDark"/>
    </div>
    <p class="text-xl mb-6 dark:text-whitish-gray">Log in</p>
    <div class="flex items-center justify-end mt-4">
      <a href="/auth/github"
         class="flex items-center justify-center w-full py-5 h-9 rounded-full font-medium text-sm focus:outline-none focus:ring-0 transition duration-150 ease-in-out dark:bg-turquoise bg-dark-green text-white dark:text-black dark:text-dark-black dark:hover:border-green">
        <i class="fa-brands fa-github mr-1 text-lg"></i>
        <p class="mt-1">Login with GitHub</p>
      </a>
    </div>

    <p class="text-gray-500 text-xs text-center my-6">OR</p>

    <form @submit.prevent="submit">
      <div>
        <InputLabel for="email" value="Email"/>
        <TextInput
            id="email"
            v-model="form.email"
            type="email"
            class="mt-1 block w-full"
            placeholder="Enter email"
            required
            autofocus
            autocomplete="username"
        />
        <InputError class="mt-2" :message="form.errors.email"/>
      </div>

      <div class="mt-4">
        <InputLabel for="password" value="Password"/>
        <TextInput
            id="password"
            v-model="form.password"
            type="password"
            class="mt-1 block w-full"
            placeholder="Enter password"
            required
            autocomplete="current-password"
        />
        <InputError class="mt-2" :message="form.errors.password"/>
      </div>

      <div class="mt-2 flex items-center justify-end">
        <Link v-if="canResetPassword" :href="route('password.request')"
              class="text-xs text-tundora dark:text-spun-pearl hover:text-gray-900 dark:hover:text-gray-100">
          Forgot password?
        </Link>
      </div>
      <div class="flex items-center justify-end mt-4">
        <Button type="submit" color="secondary" class="text-xs" :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing">
          Log in
        </Button>
      </div>
    </form>
    <div class="border-t border-whitish-gray dark:border-oil mt-6">
      <p class="mt-5 text-xs text-spun-pearl">Don't have an account? <a href="/register" class="text-green font-bold">Sign
        Up</a></p>
    </div>
  </AuthenticationCard>
</template>
