<template>
    <div class="max-w-4xl mx-auto flex flex-col h-full">
        <!-- Scrollable Content Area -->
        <div class="flex-1 overflow-y-auto pb-24">
            <!-- Progress Indicator -->
            <div class="mb-8 flex items-center justify-center space-x-2">
                <div class="w-3 h-3 bg-grayish dark:bg-gunmetal rounded-full"></div>
                <div class="w-3 h-3 rounded-full" :class="currentStep >= 2 ? 'bg-purple-heart' : 'bg-grayish dark:bg-gunmetal'"></div>
                <div class="w-3 h-3 rounded-full" :class="currentStep >= 3 ? 'bg-purple-heart' : 'bg-grayish dark:bg-gunmetal'"></div>
                <div class="w-3 h-3 rounded-full" :class="currentStep >= 4 ? 'bg-purple-heart' : 'bg-grayish dark:bg-gunmetal'"></div>
                <div class="w-3 h-3 rounded-full" :class="currentStep >= 5 ? 'bg-purple-heart' : 'bg-grayish dark:bg-gunmetal'"></div>
            </div>

            <!-- Step 1: Authentication Choice -->
            <div v-if="currentStep === 1 && !isAuthenticated">
                <div class="text-center mb-8">
                    <div class="w-20 h-20 mx-auto mb-6 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-rich-black dark:text-seashell mb-2">
                        Welcome to OpenPledge
                    </h2>
                    <p class="text-lg text-mondo dark:text-spun-pearl">
                        Let's get you set up to support open source projects
                    </p>
                </div>

                <div class="max-w-md mx-auto space-y-4">
                    <div class="bg-pale-aqua dark:bg-tropical-rain-forest border border-ocean-green dark:border-ocean-green rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-rich-black dark:text-seashell mb-2">
                            Do you already have an OpenPledge account?
                        </h3>
                        <p class="text-sm text-mondo dark:text-spun-pearl mb-6">
                            You can sign in with your existing account or create a new one
                        </p>

                        <div class="space-y-3">
                            <a
                                :href="route('login')"
                                @click="handleAuthRedirect('login')"
                                class="block w-full py-4 px-6 bg-white dark:bg-charcoal-gray border-2 border-ocean-green dark:border-ocean-green rounded-lg text-center font-semibold text-rich-black dark:text-seashell hover:bg-pale-aqua dark:hover:bg-tropical-rain-forest transition-all duration-200"
                            >
                                Yes, I have an account
                            </a>

                            <a
                                :href="route('register')"
                                @click="handleAuthRedirect('register')"
                                class="block w-full py-4 px-6 bg-dark-green dark:bg-turquoise text-white dark:text-rich-black rounded-lg text-center font-semibold hover:bg-ocean-green dark:hover:bg-green transition-all duration-200"
                            >
                                No, create new account
                            </a>
                        </div>
                    </div>

                    <button
                        @click="emit('back')"
                        class="w-full text-sm text-mondo dark:text-spun-pearl hover:text-rich-black dark:hover:text-seashell transition-colors duration-200"
                    >
                        ← Back to goal selection
                    </button>
                </div>
            </div>

            <!-- Step 2: Welcome Success -->
            <div v-else-if="currentStep === 2">
                <div class="text-center mb-8">
                    <div class="w-20 h-20 mx-auto mb-6 bg-mint-green dark:bg-shade-green rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-dark-green dark:text-green" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>

                    <img
                        v-if="userAvatar"
                        :src="userAvatar"
                        :alt="userName"
                        class="w-24 h-24 rounded-full mx-auto mb-4 border-4 border-ocean-green dark:border-green shadow-lg"
                    />

                    <h2 class="text-3xl font-bold text-rich-black dark:text-seashell mb-2">
                        Welcome, {{ userName }}!
                    </h2>
                    <p class="text-lg text-mondo dark:text-spun-pearl">
                        You're all set to start supporting amazing open source projects
                    </p>
                </div>

                <div class="flex justify-center mt-12">
                    <button
                        @click="nextStep"
                        class="px-8 py-3 bg-purple-heart text-white font-semibold rounded-lg hover:bg-purple-600 transition-all duration-200"
                    >
                        Continue
                    </button>
                </div>
            </div>

            <!-- Step 3: Company & Recognition -->
            <div v-else-if="currentStep === 3">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                            <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-rich-black dark:text-seashell mb-2">
                        About You
                    </h2>
                </div>

                <div class="max-w-2xl mx-auto space-y-6">
                    <!-- Company Question -->
                    <div class="bg-white dark:bg-charcoal-gray rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-rich-black dark:text-seashell mb-4">
                            Are you pledging as a company?
                        </h3>
                        <div class="space-y-3">
                            <label
                                class="flex items-center space-x-3 cursor-pointer p-4 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 border-2 transition-all duration-200"
                                :class="isCompany === false ? 'border-purple-heart bg-purple-50 dark:bg-purple-900/30' : 'border-gray-200 dark:border-gray-700'"
                            >
                                <input
                                    type="radio"
                                    :value="false"
                                    v-model="isCompany"
                                    class="h-4 w-4 text-purple-heart focus:ring-purple-heart border-gray-300"
                                >
                                <div>
                                    <span class="font-medium text-rich-black dark:text-seashell">Individual</span>
                                    <p class="text-sm text-mondo dark:text-spun-pearl">I'm pledging as an individual</p>
                                </div>
                            </label>

                            <label
                                class="flex items-center space-x-3 cursor-pointer p-4 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 border-2 transition-all duration-200"
                                :class="isCompany === true ? 'border-purple-heart bg-purple-50 dark:bg-purple-900/30' : 'border-gray-200 dark:border-gray-700'"
                            >
                                <input
                                    type="radio"
                                    :value="true"
                                    v-model="isCompany"
                                    class="h-4 w-4 text-purple-heart focus:ring-purple-heart border-gray-300"
                                >
                                <div>
                                    <span class="font-medium text-rich-black dark:text-seashell">Company</span>
                                    <p class="text-sm text-mondo dark:text-spun-pearl">I'm pledging on behalf of my company</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Company Form (conditional) -->
                    <div v-if="isCompany" class="bg-white dark:bg-charcoal-gray rounded-lg p-6 border border-gray-200 dark:border-gray-700 space-y-4">
                        <h3 class="text-lg font-semibold text-rich-black dark:text-seashell mb-4">
                            Company Information
                        </h3>

                        <div>
                            <label class="block text-sm font-medium text-rich-black dark:text-seashell mb-1">Company Name *</label>
                            <input
                                v-model="companyName"
                                type="text"
                                required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-heart focus:border-transparent bg-white dark:bg-oil text-rich-black dark:text-seashell"
                                placeholder="Enter company name"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-rich-black dark:text-seashell mb-1">Address *</label>
                            <input
                                v-model="companyAddress"
                                type="text"
                                required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-heart focus:border-transparent bg-white dark:bg-oil text-rich-black dark:text-seashell"
                                placeholder="Enter address"
                            />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-rich-black dark:text-seashell mb-1">City *</label>
                                <input
                                    v-model="companyCity"
                                    type="text"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-heart focus:border-transparent bg-white dark:bg-oil text-rich-black dark:text-seashell"
                                    placeholder="Enter city"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-rich-black dark:text-seashell mb-1">Postal Code *</label>
                                <input
                                    v-model="companyPostalCode"
                                    type="text"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-heart focus:border-transparent bg-white dark:bg-oil text-rich-black dark:text-seashell"
                                    placeholder="Enter postal code"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-rich-black dark:text-seashell mb-1">State (Optional)</label>
                            <input
                                v-model="companyState"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-heart focus:border-transparent bg-white dark:bg-oil text-rich-black dark:text-seashell"
                                placeholder="Enter state"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-rich-black dark:text-seashell mb-1">Country *</label>
                            <CountrySelect
                                v-model="companyCountry"
                                class="mt-1 dark:text-white dark:bg-oil bg-white dark:placeholder-spun-pearl dark:border-mondo focus:border-spun-pearl border-gray-300"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-rich-black dark:text-seashell mb-1">VAT ID *</label>
                            <input
                                v-model="companyVatId"
                                type="text"
                                required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-heart focus:border-transparent bg-white dark:bg-oil text-rich-black dark:text-seashell"
                                placeholder="Enter VAT ID"
                            />
                        </div>
                    </div>

                    <!-- Public Recognition -->
                    <div class="bg-white dark:bg-charcoal-gray rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-rich-black dark:text-seashell mb-2">
                            Public Recognition
                        </h3>
                        <p class="text-sm text-mondo dark:text-spun-pearl mb-4">
                            Choose how you'd like to be recognized for your contributions
                        </p>

                        <div class="space-y-3">
                            <label
                                class="flex items-start space-x-3 cursor-pointer p-4 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 border-2 transition-all duration-200"
                                :class="!isPledgingAnonymously ? 'border-purple-heart bg-purple-50 dark:bg-purple-900/30' : 'border-gray-200 dark:border-gray-700'"
                            >
                                <input
                                    type="radio"
                                    :value="false"
                                    v-model="isPledgingAnonymously"
                                    class="mt-1 h-4 w-4 text-purple-heart focus:ring-purple-heart border-gray-300"
                                >
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-lg">👤</span>
                                        <span class="font-medium text-rich-black dark:text-seashell">Public</span>
                                    </div>
                                    <p class="text-sm text-mondo dark:text-spun-pearl mt-1">
                                        Display my {{ isCompany ? 'company' : 'name' }} next to pledges and on the leaderboard
                                    </p>
                                </div>
                            </label>

                            <label
                                class="flex items-start space-x-3 cursor-pointer p-4 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 border-2 transition-all duration-200"
                                :class="isPledgingAnonymously ? 'border-purple-heart bg-purple-50 dark:bg-purple-900/30' : 'border-gray-200 dark:border-gray-700'"
                            >
                                <input
                                    type="radio"
                                    :value="true"
                                    v-model="isPledgingAnonymously"
                                    class="mt-1 h-4 w-4 text-purple-heart focus:ring-purple-heart border-gray-300"
                                >
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-lg">🕶️</span>
                                        <span class="font-medium text-rich-black dark:text-seashell">Anonymous</span>
                                    </div>
                                    <p class="text-sm text-mondo dark:text-spun-pearl mt-1">
                                        Keep my identity private - show as "Anonymous Supporter"
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 4: Repositories to Support -->
            <div v-else-if="currentStep === 4">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 mx-auto mb-4 bg-mint-green dark:bg-shade-green rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-dark-green dark:text-green" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-rich-black dark:text-seashell mb-2">
                        Which Projects?
                    </h2>
                    <p class="text-lg text-mondo dark:text-spun-pearl mb-2">
                        List repositories you'd like to support
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
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-heart focus:border-transparent bg-white dark:bg-oil text-rich-black dark:text-seashell resize-none"
                        ></textarea>
                        <p class="text-xs text-tundora dark:text-spun-pearl mt-2">
                            💡 Don't worry if you're not sure yet - you can discover and support projects anytime
                        </p>
                    </div>
                </div>
            </div>

            <!-- Step 5: Final Step -->
            <div v-else-if="currentStep === 5">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 mx-auto mb-4 bg-mint-green dark:bg-shade-green rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-dark-green dark:text-green" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-rich-black dark:text-seashell mb-2">
                        All Set!
                    </h2>
                    <p class="text-lg text-mondo dark:text-spun-pearl mb-2">
                        You're ready to start making an impact
                    </p>
                    <p class="text-sm text-tundora dark:text-spun-pearl">
                        Start exploring projects and make your first pledge
                    </p>
                </div>

            </div>
        </div>

        <!-- Fixed Navigation Bar at Bottom -->
        <div class="fixed bottom-0 left-0 right-0 bg-white dark:bg-charcoal-gray border-t border-gray-200 dark:border-gray-700 px-6 py-4">
            <div class="max-w-4xl mx-auto flex justify-between items-center">
                <button
                    v-if="currentStep > 1"
                    @click="goBack"
                    class="px-6 py-3 text-mondo dark:text-spun-pearl hover:text-rich-black dark:hover:text-seashell transition-colors duration-200"
                >
                    ← Back
                </button>
                <div v-else></div>

                <button
                    v-if="currentStep === 2"
                    @click="nextStep"
                    class="px-8 py-3 bg-purple-heart text-white font-semibold rounded-lg hover:bg-purple-600 transition-all duration-200"
                >
                    Continue
                </button>
                <button
                    v-else-if="currentStep === 3 || currentStep === 4"
                    @click="nextStep"
                    :disabled="isCompany && currentStep === 3 && !isCompanyFormValid"
                    class="px-8 py-3 bg-purple-heart text-white font-semibold rounded-lg hover:bg-purple-600 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Continue
                </button>
                <button
                    v-else-if="currentStep === 5"
                    @click="completeFlow"
                    class="px-8 py-3 bg-purple-heart text-white font-semibold rounded-lg hover:bg-purple-600 transition-all duration-200"
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
import CountrySelect from '@/Components/Custom/CountrySelect.vue';

const emit = defineEmits(["completed", "back"]);

// Check if user is already authenticated
const isAuthenticated = computed(() => usePage().props.auth.user !== null);
const user = computed(() => usePage().props.auth.user);
const userName = computed(() => user.value?.name || 'there');
const userAvatar = computed(() => {
    if (!user.value) return null;
    if (user.value.profile_photo_path) {
        return `/storage/${user.value.profile_photo_path}`;
    }
    return user.value.profile_photo_url || null;
});

// Check if resuming from auth
const resumingFromAuth = localStorage.getItem('onboarding_step') === '2';

// Determine initial step
const currentStep = ref(resumingFromAuth ? 2 : (isAuthenticated.value ? 2 : 1));

onMounted(() => {
    if (resumingFromAuth) {
        localStorage.removeItem('onboarding_step');
    }
});

const totalSteps = 5;

// Form data
const isCompany = ref(false);
const companyName = ref('');
const companyAddress = ref('');
const companyCity = ref('');
const companyPostalCode = ref('');
const companyState = ref('');
const companyCountry = ref('');
const companyVatId = ref('');
const isPledgingAnonymously = ref(false);
const specificRepositories = ref('');

// Validation
const isCompanyFormValid = computed(() => {
    if (!isCompany.value) return true;
    return !!(
        companyName.value.trim() &&
        companyAddress.value.trim() &&
        companyCity.value.trim() &&
        companyPostalCode.value.trim() &&
        companyCountry.value &&
        companyVatId.value.trim()
    );
});

const handleAuthRedirect = (type) => {
    localStorage.setItem('onboarding_in_progress', 'true');
    localStorage.setItem('onboarding_goal', 'userIsPledger');
    localStorage.setItem('onboarding_step', '2');
};

const nextStep = () => {
    if (currentStep.value < totalSteps) {
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
        isCompany: isCompany.value,
        isPledgingAnonymously: isPledgingAnonymously.value,
        specificRepositories: repositoryList
    };

    // Add company data if pledging as company
    if (isCompany.value) {
        formData.companyName = companyName.value;
        formData.companyAddress = companyAddress.value;
        formData.companyCity = companyCity.value;
        formData.companyPostalCode = companyPostalCode.value;
        formData.companyState = companyState.value;
        formData.companyCountry = companyCountry.value;
        formData.companyVatId = companyVatId.value;
    }

    emit("completed", formData);
};
</script>
