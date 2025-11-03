<template>
    <div class="text-center mb-8">
        <!-- Show success message if authenticated -->
        <div v-if="isAuthenticated">
            <div class="w-20 h-20 mx-auto mb-6 bg-mint-green dark:bg-shade-green rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-dark-green dark:text-green" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>

            <div class="mb-4">
                <img
                    :src="userAvatar"
                    :alt="userName"
                    class="w-24 h-24 rounded-full mx-auto border-4 border-green shadow-lg"
                />
            </div>

            <h2 class="text-3xl font-bold text-rich-black dark:text-seashell mb-2">
                Welcome, {{ userName }}!
            </h2>
            <p class="text-lg text-mondo dark:text-spun-pearl mb-8">
                Your GitHub account has been successfully connected
            </p>
        </div>

        <!-- Show login form if not authenticated -->
        <div v-else>
            <div class="w-16 h-16 mx-auto mb-4" :class="iconBgClass">
                <svg class="w-8 h-8" :class="iconColorClass" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 0C4.477 0 0 4.484 0 10.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0110 4.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.203 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.942.359.31.678.921.678 1.856 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0020 10.017C20 4.484 15.522 0 10 0z" clip-rule="evenodd" />
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-rich-black dark:text-seashell mb-2">
                Connect with GitHub
            </h2>
            <p class="text-lg text-mondo dark:text-spun-pearl mb-6">
                {{ description }}
            </p>

            <div class="max-w-2xl mx-auto rounded-xl p-6 mb-8" :class="infoBgClass">
                <p class="text-sm text-rich-black dark:text-seashell text-left">
                    <span class="font-semibold">Why GitHub?</span> {{ whyGitHub }}
                </p>
            </div>

            <a
                :href="route('github.auth.redirect')"
                @click="handleGitHubLogin"
                class="inline-flex items-center justify-center px-8 py-4 bg-dark-green dark:bg-turquoise text-white dark:text-rich-black font-semibold rounded-lg hover:bg-ocean-green dark:hover:bg-green transition-all duration-200 text-lg mb-8"
            >
                <i class="fa-brands fa-github mr-2 text-xl"></i>
                Login with GitHub
                <svg v-if="loading" class="animate-spin ml-2" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle opacity="0.2" cx="12" cy="12" r="6" stroke="currentColor" stroke-width="2"/>
                    <path d="M18 12C18 8.68629 15.3137 6 12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </a>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    isAuthenticated: {
        type: Boolean,
        required: true
    },
    flowType: {
        type: String,
        required: true,
        validator: (value) => ['contributor', 'maintainer'].includes(value)
    }
});

const emit = defineEmits(['github-login-clicked']);

const loading = ref(false);

const user = computed(() => usePage().props.auth?.user);
const userName = computed(() => user.value?.name || 'User');
const userAvatar = computed(() => {
    if (user.value?.profile_photo_url) {
        return user.value.profile_photo_url;
    }
    return `https://ui-avatars.com/api/?name=${encodeURIComponent(userName.value)}&color=7F9CF5&background=EBF4FF`;
});

// Different styling based on flow type
const iconBgClass = computed(() => {
    return props.flowType === 'contributor'
        ? 'bg-mint-green dark:bg-shade-green rounded-full flex items-center justify-center'
        : 'bg-pale-aqua dark:bg-tropical-rain-forest rounded-full flex items-center justify-center';
});

const iconColorClass = computed(() => {
    return props.flowType === 'contributor'
        ? 'text-dark-green dark:text-green'
        : 'text-ocean-green dark:text-turquoise';
});

const infoBgClass = computed(() => {
    return props.flowType === 'contributor'
        ? 'bg-pale-aqua dark:bg-tropical-rain-forest border border-ocean-green dark:border-ocean-green'
        : 'bg-mint-green dark:bg-shade-green border border-green dark:border-green';
});

const description = computed(() => {
    return props.flowType === 'contributor'
        ? 'We need to verify your GitHub account to match you with issues and process payments.'
        : 'We need to connect your GitHub account to access your repositories and manage funding.';
});

const whyGitHub = computed(() => {
    return props.flowType === 'contributor'
        ? 'We verify your pull requests and contributions through GitHub to ensure you get paid for your work.'
        : 'We connect to your repositories to enable funding on issues and verify your maintainer status.';
});

const handleGitHubLogin = () => {
    loading.value = true;
    emit('github-login-clicked');
};
</script>
