<template>
    <div class="max-w-4xl mx-auto flex flex-col h-full">
        <!-- Scrollable Content Area -->
        <div class="flex-1 overflow-y-auto pb-24">
        <!-- Progress Indicator -->
        <div class="mb-8 flex items-center justify-center space-x-2">
            <div class="w-3 h-3 bg-grayish dark:bg-gunmetal rounded-full"></div>
            <div class="w-3 h-3 rounded-full" :class="currentStep >= 2 ? 'bg-green' : 'bg-grayish dark:bg-gunmetal'"></div>
            <div class="w-3 h-3 rounded-full" :class="currentStep >= 3 ? 'bg-green' : 'bg-grayish dark:bg-gunmetal'"></div>
        </div>

        <!-- Step 1: GitHub Login -->
        <GitHubAuthStep
            v-if="currentStep === 1"
            :is-authenticated="isGitHubAuthenticated"
            flow-type="maintainer"
            @github-login-clicked="handleGitHubLogin"
        />

        <!-- Step 2: Repository Connection -->
        <div v-if="currentStep === 2">
            <!-- Success State: App Installed -->
            <div v-if="appInstalled">
                <div class="text-center mb-8">
                    <div class="w-20 h-20 mx-auto mb-6 bg-mint-green dark:bg-shade-green rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-dark-green dark:text-green" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>

                    <h2 class="text-3xl font-bold text-rich-black dark:text-seashell mb-2">
                        {{ connectedRepositories.length === 1 ? 'Repository Connected!' : 'Repositories Connected!' }}
                    </h2>
                    <p class="text-lg text-mondo dark:text-spun-pearl mb-6">
                        {{ connectedRepositories.length }} {{ connectedRepositories.length === 1 ? 'repository' : 'repositories' }} connected to OpenPledge
                    </p>

                    <!-- Repository List -->
                    <div v-if="loadingRepositories" class="mb-6">
                        <div class="animate-pulse space-y-3">
                            <div class="h-16 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                            <div class="h-16 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                        </div>
                    </div>
                    <div v-else-if="connectedRepositories.length > 0" class="mb-8 max-h-64 overflow-y-auto space-y-3">
                        <div
                            v-for="repo in connectedRepositories"
                            :key="repo.id"
                            class="bg-pale-aqua dark:bg-tropical-rain-forest border border-ocean-green dark:border-ocean-green rounded-lg p-4 text-left"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-rich-black dark:text-seashell truncate">
                                        {{ repo.title }}
                                    </p>
                                </div>
                                <a
                                    :href="repo.github_url"
                                    target="_blank"
                                    class="ml-3 text-tundora dark:text-spun-pearl hover:text-ocean-green dark:hover:text-green transition-colors flex-shrink-0"
                                    title="View on GitHub"
                                >
                                    <i class="fa-brands fa-github"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <button
                        @click="nextStep"
                        class="inline-flex items-center justify-center px-8 py-4 bg-dark-green dark:bg-turquoise text-white dark:text-rich-black font-semibold rounded-lg hover:bg-ocean-green dark:hover:bg-green transition-all duration-200 text-lg mb-4"
                    >
                        Configure {{ connectedRepositories.length === 1 ? 'Repository' : 'Repositories' }}
                    </button>

                    <div>
                        <button
                            @click="skipConfiguration"
                            class="text-sm text-mondo dark:text-spun-pearl hover:text-rich-black dark:hover:text-seashell transition-colors duration-200"
                        >
                            Skip configuration, I'll do it later
                        </button>
                    </div>
                </div>
            </div>

            <!-- Install State: Not Yet Installed -->
            <div v-else>
                <div class="text-center mb-8">
                    <div class="w-16 h-16 mx-auto mb-4 bg-mint-green dark:bg-shade-green rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-dark-green dark:text-green" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 0C4.477 0 0 4.484 0 10.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0110 4.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.203 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.942.359.31.678.921.678 1.856 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0020 10.017C20 4.484 15.522 0 10 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-rich-black dark:text-seashell mb-2">
                        Connect Your First Repository
                    </h2>
                    <p class="text-lg text-mondo dark:text-spun-pearl mb-2">
                        Install the OpenPledge GitHub App to enable funding on your repositories
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        You'll be able to connect more repositories later
                    </p>
                </div>

                <div class="bg-pale-aqua dark:bg-tropical-rain-forest border border-ocean-green dark:border-ocean-green rounded-xl p-6 mb-8">
                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 bg-ocean-green dark:bg-turquoise rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white dark:text-rich-black" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-rich-black dark:text-seashell mb-1">How it works</h3>
                            <p class="text-rich-black dark:text-seashell text-sm">
                                You'll be redirected to GitHub where you can select which repositories to connect.
                                OpenPledge will be able to monitor issues and manage funding. You maintain full control and can modify permissions anytime.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <a
                        :href="githubAppInstallUrl"
                        @click="handleInstallApp"
                        class="inline-flex items-center justify-center px-8 py-4 bg-dark-green dark:bg-turquoise text-white dark:text-rich-black font-semibold rounded-lg hover:bg-ocean-green dark:hover:bg-green transition-all duration-200 text-lg mb-4"
                    >
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 0C4.477 0 0 4.484 0 10.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0110 4.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.203 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.942.359.31.678.921.678 1.856 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0020 10.017C20 4.484 15.522 0 10 0z" clip-rule="evenodd" />
                        </svg>
                        Install GitHub App
                    </a>

                    <div>
                        <button
                            @click="skipInstallation"
                            class="text-sm text-mondo dark:text-spun-pearl hover:text-rich-black dark:hover:text-seashell transition-colors duration-200"
                        >
                            Skip for now, I'll explore on my own
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Step 3: Repository Settings -->
        <div v-else-if="currentStep === 3">
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-mint-green dark:bg-shade-green rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-dark-green dark:text-green" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-rich-black dark:text-seashell mb-2">
                    Configure {{ connectedRepositories.length === 1 ? 'Repository' : 'Repositories' }}
                </h2>
                <p class="text-lg text-mondo dark:text-spun-pearl mb-2">
                    Set pledge acceptance rules for your {{ connectedRepositories.length === 1 ? 'repository' : 'repositories' }}
                </p>
                <p class="text-sm text-tundora dark:text-spun-pearl">
                    You can always change these settings later in your profile
                </p>
            </div>

            <!-- Repositories List with Settings -->
            <div class="max-w-3xl mx-auto max-h-96 overflow-y-auto space-y-4 mb-8">
                <div
                    v-for="repo in connectedRepositories"
                    :key="repo.id"
                    class="bg-white dark:bg-charcoal-gray rounded-lg p-4 border border-gray-200 dark:border-gray-700"
                >
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2 mb-1">
                                <h3 class="text-base font-medium text-rich-black dark:text-seashell truncate">
                                    {{ repo.title }}
                                </h3>
                                <a
                                    :href="repo.github_url"
                                    target="_blank"
                                    class="text-tundora dark:text-spun-pearl hover:text-ocean-green dark:hover:text-green transition-colors flex-shrink-0"
                                    title="View on GitHub"
                                >
                                    <i class="fa-brands fa-github text-sm"></i>
                                </a>
                            </div>
                        </div>
                        <button
                            @click="openSettings(repo)"
                            class="ml-4 p-2 text-tundora dark:text-spun-pearl hover:text-ocean-green dark:hover:text-green transition-colors flex-shrink-0"
                            title="Configure Settings"
                        >
                            <i class="fa fa-cog"></i>
                        </button>
                    </div>

                    <!-- Settings Preview -->
                    <div class="text-xs">
                        <span
                            v-if="repo.settings?.allowed_labels?.includes('Pledgeable')"
                            class="text-ocean-green dark:text-green"
                        >
                            ✓ Requires "Pledgeable" label
                        </span>
                        <span v-else class="text-tundora dark:text-spun-pearl">
                            No restrictions
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Repository Settings Modal -->
        <RepositorySettingsModal
            v-if="showSettingsModal"
            :repository="selectedRepository"
            @close="closeSettings"
            @updated="handleSettingsUpdated"
        />


        </div>

        <!-- Fixed Navigation Bar at Bottom -->
        <div v-if="currentStep !== 2" class="fixed bottom-0 left-0 right-0 bg-white dark:bg-charcoal-gray border-t border-gray-200 dark:border-gray-700 px-6 py-4">
            <div class="max-w-4xl mx-auto flex justify-between items-center">
                <button
                    @click="goBack"
                    class="px-6 py-3 text-mondo dark:text-spun-pearl hover:text-rich-black dark:hover:text-seashell transition-colors duration-200"
                >
                    ← Back
                </button>

                <button
                    v-if="currentStep < totalSteps"
                    @click="nextStep"
                    :disabled="!canProceed"
                    class="px-8 py-3 bg-green text-rich-black font-semibold rounded-lg hover:bg-turquoise disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                >
                    Continue
                </button>
                <button
                    v-else
                    @click="completeFlow"
                    :disabled="!canComplete"
                    class="px-8 py-3 bg-green text-rich-black font-semibold rounded-lg hover:bg-turquoise disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                >
                    Finish Setup
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { usePage } from '@inertiajs/vue3';
import GitHubAuthStep from './GitHubAuthStep.vue';
import RepositorySettingsModal from '@/Pages/Profile/Partials/RepositorySettingsModal.vue';

const emit = defineEmits(["completed", "back"]);

// Check if user is already authenticated (skip GitHub login step)
const isUserAuthenticated = computed(() => usePage().props.auth.user !== null);

// Check if resuming from GitHub auth - stay at step 1 to show welcome message
const resumingFromAuth = localStorage.getItem('onboarding_step') === '2';
// Priority: resumingFromAuth (stay at 1) > wasAlreadyAuthenticated (skip to 2) > not authenticated (start at 1)
const wasAlreadyAuthenticated = isUserAuthenticated.value && !resumingFromAuth;
const currentStep = ref(resumingFromAuth ? 1 : (wasAlreadyAuthenticated ? 2 : 1));
const isGitHubAuthenticated = ref(isUserAuthenticated.value);

// Track if GitHub App was installed
const appInstalled = ref(false);
const connectedRepositories = ref([]);
const loadingRepositories = ref(false);

onMounted(async () => {
    if (resumingFromAuth) {
        // Show success message at step 1
        localStorage.removeItem('onboarding_step');
    }

    // Check if returning from GitHub App installation
    if (localStorage.getItem('maintainer_onboarding_installing') === 'true') {
        localStorage.removeItem('maintainer_onboarding_installing');
        // Show success message and stay at step 2
        appInstalled.value = true;
        currentStep.value = 2;
        // Fetch repositories that were just connected
        await fetchRepositories();
    }
});

const fetchRepositories = async () => {
    try {
        loadingRepositories.value = true;
        const response = await axios.get(route('profile.repositories'));
        connectedRepositories.value = response.data;
    } catch (error) {
        console.error('Failed to fetch repositories:', error);
    } finally {
        loadingRepositories.value = false;
    }
};

const totalSteps = 3;
const loading = ref(false);

// Settings modal state
const showSettingsModal = ref(false);
const selectedRepository = ref(null);

// GitHub App installation URL
const githubAppInstallUrl = import.meta.env.VITE_GITHUB_APP_INSTALLATION_URL || 'https://github.com/apps/openpledge-io/installations/new';

const openSettings = (repository) => {
    selectedRepository.value = repository;
    showSettingsModal.value = true;
};

const closeSettings = () => {
    showSettingsModal.value = false;
    selectedRepository.value = null;
};

const handleSettingsUpdated = (updatedSettings) => {
    // Update the repository in the list with new settings
    const index = connectedRepositories.value.findIndex(repo => repo.id === selectedRepository.value.id);
    if (index !== -1) {
        connectedRepositories.value[index].settings = updatedSettings;
    }
    closeSettings();
};

const canProceed = computed(() => {
    if (currentStep.value === 1) return isGitHubAuthenticated.value; // GitHub login step - only enabled after auth
    if (currentStep.value === 2) {
        return appInstalled.value; // Step 2 can continue only if app is installed
    }
    if (currentStep.value === 3) {
        return true; // Step 3 can always proceed (checkbox is optional)
    }
    return true;
});

const canComplete = computed(() => {
    return true; // Can always complete step 3
});


const nextStep = () => {
    if (canProceed.value) {
        currentStep.value++;
    }
};

const goBack = () => {
    if (currentStep.value === 1) {
        emit("back");
    } else {
        currentStep.value--;
    }
};

const completeFlow = () => {
    // Settings are already saved individually via the modal
    // Just complete the onboarding
    emit("completed", {
        configured: true,
        repositoriesConfigured: connectedRepositories.value.filter(r => r.settings?.allowed_labels?.includes('Pledgeable')).length
    });
};

const handleGitHubLogin = () => {
    loading.value = true;
    // Store onboarding state in localStorage
    localStorage.setItem('onboarding_in_progress', 'true');
    localStorage.setItem('onboarding_goal', 'userIsMaintainer');
    localStorage.setItem('onboarding_step', '2');
};

const handleInstallApp = () => {
    // Store that we're in the middle of onboarding installation
    localStorage.setItem('maintainer_onboarding_installing', 'true');
};

const skipInstallation = () => {
    // User wants to skip installation and explore
    emit("completed", { skipped: true });
};

const skipConfiguration = () => {
    // User wants to skip repository configuration
    emit("completed", { configured: false, skippedConfiguration: true });
};
</script>
