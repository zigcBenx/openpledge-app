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
            flow-type="contributor"
            @github-login-clicked="handleGitHubLogin"
        />

        <!-- Step 2: Programming Languages -->
        <div v-if="currentStep === 2">
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-mint-green dark:bg-shade-green rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-dark-green dark:text-green" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-rich-black dark:text-seashell mb-2">
                    What technologies do you love?
                </h2>
                <p class="text-lg text-mondo dark:text-spun-pearl">
                    Select the programming languages and frameworks you enjoy working with
                </p>
            </div>

            <!-- Popular Languages Section -->
            <div v-if="popularLanguages.length > 0" class="mb-8">
                <h3 class="text-sm font-semibold text-mondo dark:text-spun-pearl uppercase tracking-wider mb-4">
                    Popular Languages
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    <div
                        v-for="lang in popularLanguages"
                        :key="lang.id"
                        @click="toggleLanguage(lang.id)"
                        class="cursor-pointer p-4 border-2 rounded-xl transition-all duration-200 hover:shadow-md"
                        :class="selectedLanguages.includes(lang.id)
                            ? 'border-green bg-mint-green dark:bg-shade-green'
                            : 'border-grayish dark:border-gunmetal hover:border-green'"
                    >
                        <div class="text-center">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ lang.name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other Languages Section -->
            <div v-if="otherLanguages.length > 0">
                <h3 class="text-sm font-semibold text-mondo dark:text-spun-pearl uppercase tracking-wider mb-4">
                    Other Languages
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    <div
                        v-for="lang in otherLanguages"
                        :key="lang.id"
                        @click="toggleLanguage(lang.id)"
                        class="cursor-pointer p-4 border-2 rounded-xl transition-all duration-200 hover:shadow-md"
                        :class="selectedLanguages.includes(lang.id)
                            ? 'border-green bg-mint-green dark:bg-shade-green'
                            : 'border-grayish dark:border-gunmetal hover:border-green'"
                    >
                        <div class="text-center">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ lang.name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-6">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Selected {{ selectedLanguages.length }} language{{ selectedLanguages.length !== 1 ? 's' : '' }}
                </p>
            </div>
        </div>

        <!-- Step 3: Repositories to Contribute To -->
        <div v-if="currentStep === 3">
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-mint-green dark:bg-shade-green rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-dark-green dark:text-green" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-rich-black dark:text-seashell mb-2">
                    Which Projects Interest You?
                </h2>
                <p class="text-lg text-mondo dark:text-spun-pearl mb-2">
                    List repositories you'd like to contribute to
                </p>
                <p class="text-sm text-tundora dark:text-spun-pearl">
                    You can always add or change these later
                </p>
            </div>

            <div class="max-w-2xl mx-auto">
                <div class="bg-white dark:bg-charcoal-gray rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                    <label class="block text-sm font-medium text-rich-black dark:text-seashell mb-2">
                        Repository Names (Optional)
                    </label>
                    <textarea
                        v-model="specificRepositories"
                        rows="8"
                        placeholder="facebook/react&#10;microsoft/vscode&#10;vuejs/vue&#10;&#10;One repository per line (format: owner/repo-name)"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green focus:border-transparent bg-white dark:bg-oil text-rich-black dark:text-seashell resize-none"
                    ></textarea>
                    <p class="text-xs text-tundora dark:text-spun-pearl mt-2">
                        💡 Don't worry if you're not sure yet - you can discover and contribute to projects anytime
                    </p>
                </div>
            </div>
        </div>

        </div>

        <!-- Fixed Navigation Bar at Bottom -->
        <div class="fixed bottom-0 left-0 right-0 bg-white dark:bg-charcoal-gray border-t border-gray-200 dark:border-gray-700 px-6 py-4">
            <div class="max-w-4xl mx-auto flex justify-between items-center">
                <button
                    @click="goBack"
                    class="px-6 py-3 text-mondo dark:text-spun-pearl hover:text-rich-black dark:hover:text-seashell transition-colors duration-200"
                >
                    ← Back
                </button>

                <button
                    v-if="currentStep < 3"
                    @click="nextStep"
                    :disabled="!canProceed"
                    class="px-8 py-3 bg-green text-rich-black font-semibold rounded-lg hover:bg-turquoise disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                >
                    Continue
                </button>
                <button
                    v-else
                    @click="completeFlow"
                    class="px-8 py-3 bg-green text-rich-black font-semibold rounded-lg hover:bg-turquoise transition-all duration-200"
                >
                    Start Contributing!
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { usePage } from '@inertiajs/vue3';
import GitHubAuthStep from './GitHubAuthStep.vue';

const emit = defineEmits(["completed", "back"]);

// Check if user is already authenticated (skip GitHub login step)
const isUserAuthenticated = computed(() => usePage().props.auth.user !== null);

// Check if resuming from GitHub auth - stay at step 1 to show welcome message
const resumingFromAuth = localStorage.getItem('onboarding_step') === '2';
// Priority: resumingFromAuth (stay at 1) > wasAlreadyAuthenticated (skip to 2) > not authenticated (start at 1)
const wasAlreadyAuthenticated = isUserAuthenticated.value && !resumingFromAuth;
const currentStep = ref(resumingFromAuth ? 1 : (wasAlreadyAuthenticated ? 2 : 1));
const isGitHubAuthenticated = ref(isUserAuthenticated.value);

const selectedLanguages = ref([]);
const loading = ref(false);
const allLanguages = ref([]);
const specificRepositories = ref('');

const popularLanguages = computed(() => {
    return allLanguages.value.filter(lang => lang.popular);
});

const otherLanguages = computed(() => {
    return allLanguages.value.filter(lang => !lang.popular);
});

onMounted(async () => {
    if (resumingFromAuth) {
        // Show success message at step 1
        localStorage.removeItem('onboarding_step');
    }

    // Fetch programming languages from API
    try {
        const response = await axios.get(route('programming-languages.index'));
        allLanguages.value = response.data;
    } catch (error) {
        console.error('Failed to fetch programming languages:', error);
    }
});

const canProceed = computed(() => {
    if (currentStep.value === 1) return isGitHubAuthenticated.value; // GitHub login step - only enabled after auth
    if (currentStep.value === 2) return selectedLanguages.value.length > 0;
    return true;
});

const toggleLanguage = (langId) => {
    const index = selectedLanguages.value.indexOf(langId);
    if (index > -1) {
        selectedLanguages.value.splice(index, 1);
    } else {
        selectedLanguages.value.push(langId);
    }
};

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
    const repositoryList = specificRepositories.value
        .split('\n')
        .map(repo => repo.trim())
        .filter(repo => repo.length > 0 && repo.includes('/'));

    const formData = {
        programmingLanguages: selectedLanguages.value,
        specificRepositories: repositoryList
    };
    emit("completed", formData);
};

const handleGitHubLogin = () => {
    loading.value = true;
    // Store onboarding state in localStorage
    localStorage.setItem('onboarding_in_progress', 'true');
    localStorage.setItem('onboarding_goal', 'userIsContributor');
    localStorage.setItem('onboarding_step', '2');
};
</script>