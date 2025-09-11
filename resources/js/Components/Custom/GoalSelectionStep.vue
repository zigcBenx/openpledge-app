<template>
    <div class="text-center">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-rich-black dark:text-seashell mb-3">
                Welcome to OpenPledge! 
            </h1>
            <p class="text-lg text-mondo dark:text-spun-pearl mb-2">
                What brings you here today?
            </p>
            <p class="text-sm text-tundora dark:text-spun-pearl">
                Don't worry - you can always explore other options later
            </p>
        </div>

        <!-- Goal Selection Cards -->
        <div class="grid md:grid-cols-3 gap-6 max-w-5xl mx-auto">
            <!-- Contributor Card -->
            <div 
                @click="selectGoal('userIsContributor')"
                class="group cursor-pointer bg-white dark:bg-oil rounded-xl p-6 border border-grayish dark:border-gunmetal hover:border-green dark:hover:border-green transition-all duration-200"
                :class="{ 'border-green bg-mint-green dark:bg-shade-green dark:border-green': selectedGoal === 'userIsContributor' }"
            >
                <!-- Icon -->
                <div class="mb-4">
                    <div class="w-12 h-12 mx-auto bg-mint-green dark:bg-shade-green rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-dark-green dark:text-green" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                
                <!-- Content -->
                <h3 class="text-lg font-semibold text-rich-black dark:text-seashell mb-2">
                    Contributor
                </h3>
                <p class="text-sm text-mondo dark:text-spun-pearl mb-4 leading-relaxed">
                    Solve open-source issues and earn money for your contributions
                </p>
                
                <!-- Features List -->
                <ul class="text-left text-xs text-tundora dark:text-spun-pearl space-y-1">
                    <li class="flex items-center">
                        <svg class="w-3 h-3 text-green mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Browse high-value issues
                    </li>
                    <li class="flex items-center">
                        <svg class="w-3 h-3 text-green mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Get paid for merged PRs
                    </li>
                    <li class="flex items-center">
                        <svg class="w-3 h-3 text-green mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Build your reputation
                    </li>
                </ul>
            </div>

            <!-- Maintainer Card -->
            <div 
                @click="selectGoal('userIsMaintainer')"
                class="group cursor-pointer bg-white dark:bg-oil rounded-xl p-6 border border-grayish dark:border-gunmetal hover:border-ocean-green dark:hover:border-ocean-green transition-all duration-200"
                :class="{ 'border-ocean-green bg-pale-aqua dark:bg-tropical-rain-forest dark:border-ocean-green': selectedGoal === 'userIsMaintainer' }"
            >
                <!-- Icon -->
                <div class="mb-4">
                    <div class="w-12 h-12 mx-auto bg-pale-aqua dark:bg-tropical-rain-forest rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-ocean-green dark:text-turquoise" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                
                <!-- Content -->
                <h3 class="text-lg font-semibold text-rich-black dark:text-seashell mb-2">
                    Maintainer
                </h3>
                <p class="text-sm text-mondo dark:text-spun-pearl mb-4 leading-relaxed">
                    Connect your repositories and get funding for your open-source work
                </p>
                
                <!-- Features List -->
                <ul class="text-left text-xs text-tundora dark:text-spun-pearl space-y-1">
                    <li class="flex items-center">
                        <svg class="w-3 h-3 text-green mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Connect GitHub repos
                    </li>
                    <li class="flex items-center">
                        <svg class="w-3 h-3 text-green mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Receive project funding
                    </li>
                    <li class="flex items-center">
                        <svg class="w-3 h-3 text-green mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Grow your community
                    </li>
                </ul>
            </div>

            <!-- Pledger Card -->
            <div 
                @click="selectGoal('userIsPledger')"
                class="group cursor-pointer bg-white dark:bg-oil rounded-xl p-6 border border-grayish dark:border-gunmetal hover:border-purple-heart dark:hover:border-purple-heart transition-all duration-200"
                :class="{ 'border-purple-heart bg-periwinkle dark:bg-valhalla dark:border-purple-heart': selectedGoal === 'userIsPledger' }"
            >
                <!-- Icon -->
                <div class="mb-4">
                    <div class="w-12 h-12 mx-auto bg-periwinkle dark:bg-valhalla rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-heart dark:text-thistle" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                
                <!-- Content -->
                <h3 class="text-lg font-semibold text-rich-black dark:text-seashell mb-2">
                    Pledger
                </h3>
                <p class="text-sm text-mondo dark:text-spun-pearl mb-4 leading-relaxed">
                    Support open-source projects and issues you care about with funding
                </p>
                
                <!-- Features List -->
                <ul class="text-left text-xs text-tundora dark:text-spun-pearl space-y-1">
                    <li class="flex items-center">
                        <svg class="w-3 h-3 text-green mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Fund important issues
                    </li>
                    <li class="flex items-center">
                        <svg class="w-3 h-3 text-green mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Support your stack
                    </li>
                    <li class="flex items-center">
                        <svg class="w-3 h-3 text-green mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Get business recognition
                    </li>
                </ul>
            </div>
        </div>

        <!-- Auto-advance message -->
        <div class="mt-12 text-center" v-if="selectedGoal">
            <div class="inline-flex items-center px-4 py-2 bg-mint-green dark:bg-shade-green rounded-full">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-dark-green dark:text-green" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm text-dark-green dark:text-green">Setting up your experience...</span>
            </div>
        </div>

        <!-- Step indicator -->
        <div class="mt-6" v-if="!selectedGoal">
            <div class="flex items-center justify-center space-x-2">
                <div class="w-2 h-2 bg-green rounded-full"></div>
                <div class="w-2 h-2 bg-grayish dark:bg-gunmetal rounded-full"></div>
                <div class="w-2 h-2 bg-grayish dark:bg-gunmetal rounded-full"></div>
            </div>
            <p class="text-xs text-tundora dark:text-spun-pearl mt-2">Step 1 of 3</p>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";

const emit = defineEmits(["goal-selected"]);

const selectedGoal = ref(null);

const selectGoal = (goal) => {
    selectedGoal.value = goal;
    // Auto-advance after a short delay for better UX
    setTimeout(() => {
        emit("goal-selected", goal);
    }, 300);
};
</script>