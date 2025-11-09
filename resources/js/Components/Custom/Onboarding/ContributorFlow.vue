<template>
    <div class="max-w-4xl mx-auto flex flex-col h-full">
        <!-- Scrollable Content Area -->
        <div class="flex-1 overflow-y-auto pb-24">
        <!-- Progress Indicator -->
        <div class="mb-8 flex items-center justify-center space-x-2">
            <div class="w-3 h-3 bg-grayish dark:bg-gunmetal rounded-full"></div>
            <div class="w-3 h-3 rounded-full" :class="currentStep >= 2 ? 'bg-green' : 'bg-grayish dark:bg-gunmetal'"></div>
            <div class="w-3 h-3 rounded-full" :class="currentStep >= 3 ? 'bg-green' : 'bg-grayish dark:bg-gunmetal'"></div>
            <div class="w-3 h-3 rounded-full" :class="currentStep >= 4 ? 'bg-green' : 'bg-grayish dark:bg-gunmetal'"></div>
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

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-8">
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
                        <span class="text-2xl mb-2 block">{{ lang.emoji }}</span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ lang.name }}</span>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Selected {{ selectedLanguages.length }} language{{ selectedLanguages.length !== 1 ? 's' : '' }}
                </p>
            </div>
        </div>

        <!-- Step 3: Experience Level -->
        <div v-else-if="currentStep === 3">
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    What's your experience level?
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-300">
                    This helps us show you issues that match your skill level
                </p>
            </div>

            <div class="grid gap-4 max-w-2xl mx-auto">
                <div
                    v-for="level in experienceLevels"
                    :key="level.value"
                    @click="selectedExperience = level.value"
                    class="cursor-pointer p-6 border-2 rounded-xl transition-all duration-200 hover:shadow-md"
                    :class="selectedExperience === level.value 
                        ? 'border-green-500 bg-green-50 dark:bg-green-900/30' 
                        : 'border-gray-200 dark:border-gray-700 hover:border-green-300'"
                >
                    <div class="flex items-start space-x-4">
                        <div class="text-2xl">{{ level.emoji }}</div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ level.title }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ level.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 4: Interests -->
        <div v-else-if="currentStep === 4">
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    What types of issues interest you?
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-300">
                    We'll prioritize showing you issues that match your preferences
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-4xl mx-auto">
                <div
                    v-for="interest in issueTypes"
                    :key="interest.value"
                    @click="toggleInterest(interest.value)"
                    class="cursor-pointer p-6 border-2 rounded-xl transition-all duration-200 hover:shadow-md"
                    :class="selectedInterests.includes(interest.value) 
                        ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/30' 
                        : 'border-gray-200 dark:border-gray-700 hover:border-purple-300'"
                >
                    <div class="flex items-start space-x-4">
                        <div class="text-2xl">{{ interest.emoji }}</div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ interest.title }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ interest.description }}</p>
                        </div>
                    </div>
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
                    v-if="currentStep < 4"
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

onMounted(() => {
    if (resumingFromAuth) {
        // Show success message at step 1
        localStorage.removeItem('onboarding_step');
    }
});
const selectedLanguages = ref([]);
const selectedExperience = ref(null);
const selectedInterests = ref([]);
const loading = ref(false);

const popularLanguages = [
    { id: 'javascript', name: 'JavaScript', emoji: '🟨' },
    { id: 'typescript', name: 'TypeScript', emoji: '🔷' },
    { id: 'python', name: 'Python', emoji: '🐍' },
    { id: 'java', name: 'Java', emoji: '☕' },
    { id: 'csharp', name: 'C#', emoji: '💜' },
    { id: 'php', name: 'PHP', emoji: '🐘' },
    { id: 'go', name: 'Go', emoji: '🐹' },
    { id: 'rust', name: 'Rust', emoji: '🦀' },
    { id: 'swift', name: 'Swift', emoji: '🍎' },
    { id: 'kotlin', name: 'Kotlin', emoji: '💚' },
    { id: 'cpp', name: 'C++', emoji: '⚙️' },
    { id: 'ruby', name: 'Ruby', emoji: '💎' },
];

const experienceLevels = [
    {
        value: 'beginner',
        title: 'Beginner',
        description: 'New to programming or open source contributions',
        emoji: '🌱'
    },
    {
        value: 'intermediate',
        title: 'Intermediate',
        description: 'Comfortable with programming, some open source experience',
        emoji: '🌿'
    },
    {
        value: 'advanced',
        title: 'Advanced',
        description: 'Experienced developer with strong technical skills',
        emoji: '🌳'
    },
    {
        value: 'expert',
        title: 'Expert',
        description: 'Senior developer or maintainer with deep expertise',
        emoji: '🚀'
    }
];

const issueTypes = [
    {
        value: 'bug-fixes',
        title: 'Bug Fixes',
        description: 'Fix existing problems and improve stability',
        emoji: '🐛'
    },
    {
        value: 'features',
        title: 'New Features',
        description: 'Build new functionality and capabilities',
        emoji: '✨'
    },
    {
        value: 'documentation',
        title: 'Documentation',
        description: 'Improve docs, guides, and examples',
        emoji: '📚'
    },
    {
        value: 'performance',
        title: 'Performance',
        description: 'Optimize speed and efficiency',
        emoji: '⚡'
    },
    {
        value: 'ui-ux',
        title: 'UI/UX',
        description: 'Improve user interface and experience',
        emoji: '🎨'
    },
    {
        value: 'testing',
        title: 'Testing',
        description: 'Add tests and improve code quality',
        emoji: '🧪'
    }
];

const canProceed = computed(() => {
    if (currentStep.value === 1) return isGitHubAuthenticated.value; // GitHub login step - only enabled after auth
    if (currentStep.value === 2) return selectedLanguages.value.length > 0;
    if (currentStep.value === 3) return selectedExperience.value !== null;
    return true;
});

const canComplete = computed(() => {
    return selectedInterests.value.length > 0;
});

const toggleLanguage = (langId) => {
    const index = selectedLanguages.value.indexOf(langId);
    if (index > -1) {
        selectedLanguages.value.splice(index, 1);
    } else {
        selectedLanguages.value.push(langId);
    }
};

const toggleInterest = (interestValue) => {
    const index = selectedInterests.value.indexOf(interestValue);
    if (index > -1) {
        selectedInterests.value.splice(index, 1);
    } else {
        selectedInterests.value.push(interestValue);
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
    if (canComplete.value) {
        const formData = {
            programmingLanguages: selectedLanguages.value,
            experienceLevel: selectedExperience.value,
            interests: selectedInterests.value
        };
        emit("completed", formData);
    }
};

const handleGitHubLogin = () => {
    loading.value = true;
    // Store onboarding state in localStorage
    localStorage.setItem('onboarding_in_progress', 'true');
    localStorage.setItem('onboarding_goal', 'userIsContributor');
    localStorage.setItem('onboarding_step', '2');
};
</script>