<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Button from '@/Components/Button.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="border-b border-gray-100 py-4 mb-7">
            <ApplicationMark :isDark="false" />
        </div>

        <p class="text-xl mb-3">Sign up to Open Pledge</p>
        <p class="text-xs text-gray-500 mb-8">If you intend to contribute and earn pledges, please sign up using your GitHub account.</p>

        <div class="flex items-center justify-end mt-4 border-t-white">
            <a href="/auth/github" class="flex items-center justify-center w-full py-5 h-9 rounded-full font-medium text-sm focus:outline-none focus:ring-0 transition duration-150 ease-in-out dark:bg-turquoise bg-dark-green text-white dark:text-dark-black dark:hover:border-green hover:border-green hover:border-2">
                <i class="fa-brands fa-github mr-1 text-lg"></i>
                <p>Sign Up with GitHub</p>
            </a>
        </div>

        <p class="text-gray-500 text-xs text-center my-6">OR</p>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Name" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autocomplete="username"
                    placeholder="Enter email"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                    placeholder="Enter password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <Button color="secondary" type="submit" class="text-xs" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Create Account
                </Button>
            </div>
        </form>

        <div class="border-t border-gray-100 mt-6">
            <p class="text-xs text-gray-500 mt-6">By creating account you agree to OpenPledge's <b>Terms & Conditions</b> and <b>Privacy Policy</b></p>
            <p class="mt-5 text-xs text-gray-400">Already have an account? <a href="/login" class="text-dark-green font-medium">Log In</a></p>
        </div>
    </AuthenticationCard>
</template>
