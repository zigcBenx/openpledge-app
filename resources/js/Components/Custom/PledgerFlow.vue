<template>
    <div class="max-w-4xl mx-auto">
        <!-- Progress Indicator -->
        <div class="mb-8 flex items-center justify-center space-x-2">
            <div class="w-3 h-3 bg-grayish dark:bg-gunmetal rounded-full"></div>
            <div class="w-3 h-3 bg-purple-heart rounded-full"></div>
            <div class="w-3 h-3 bg-grayish dark:bg-gunmetal rounded-full"></div>
            <div class="w-3 h-3 bg-grayish dark:bg-gunmetal rounded-full"></div>
        </div>

        <!-- Step 1: Support Preferences -->
        <div v-if="currentStep === 1">
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    What would you like to support?
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-300">
                    Tell us about your interests so we can recommend the right projects
                </p>
            </div>

            <div class="space-y-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Technology Stack</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Select technologies your organization uses or is interested in supporting</p>
                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
                        <div
                            v-for="tech in technologies"
                            :key="tech.id"
                            @click="toggleTechnology(tech.id)"
                            class="cursor-pointer p-3 border-2 rounded-lg transition-all duration-200 hover:shadow-sm"
                            :class="selectedTechnologies.includes(tech.id) 
                                ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/30' 
                                : 'border-gray-200 dark:border-gray-700 hover:border-blue-300'"
                        >
                            <div class="text-center">
                                <span class="text-xl mb-1 block">{{ tech.emoji }}</span>
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ tech.name }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Project Categories</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">What types of projects would you like to support?</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div
                            v-for="category in projectCategories"
                            :key="category.value"
                            @click="toggleCategory(category.value)"
                            class="cursor-pointer p-4 border-2 rounded-lg transition-all duration-200 hover:shadow-sm"
                            :class="selectedCategories.includes(category.value) 
                                ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/30' 
                                : 'border-gray-200 dark:border-gray-700 hover:border-purple-300'"
                        >
                            <div class="flex items-center space-x-3">
                                <span class="text-2xl">{{ category.emoji }}</span>
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ category.title }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ category.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Specific Repositories (Optional)</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Know any specific repositories you'd like to support? List them here.</p>
                    <textarea
                        v-model="specificRepositories"
                        rows="4"
                        placeholder="facebook/react&#10;microsoft/vscode&#10;vuejs/vue"
                        class="w-full px-4 py-3 border border-grayish dark:border-gunmetal rounded-lg focus:ring-2 focus:ring-green focus:border-transparent bg-transparent text-rich-black dark:text-seashell resize-none"
                    ></textarea>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        One repository per line (format: owner/repo-name)
                    </p>
                </div>
            </div>
        </div>

        <!-- Step 2: Budget & Recognition -->
        <div v-else-if="currentStep === 2">
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    Budget & Recognition
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-300">
                    Set your funding preferences and recognition settings
                </p>
            </div>

            <div class="space-y-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Monthly Budget Range</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">What's your approximate monthly budget for supporting open source?</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                        <div
                            v-for="budget in budgetRanges"
                            :key="budget.value"
                            @click="selectedBudget = budget.value"
                            class="cursor-pointer p-4 border-2 rounded-lg transition-all duration-200 hover:shadow-sm text-center"
                            :class="selectedBudget === budget.value 
                                ? 'border-green-500 bg-green-50 dark:bg-green-900/30' 
                                : 'border-gray-200 dark:border-gray-700 hover:border-green-300'"
                        >
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ budget.label }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">{{ budget.description }}</div>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Public Recognition</h3>
                    <div class="space-y-3">
                        <label
                            v-for="option in recognitionOptions"
                            :key="option.value"
                            class="flex items-start space-x-3 cursor-pointer p-4 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 border-2 transition-all duration-200"
                            :class="selectedRecognition === option.value 
                                ? 'border-green-500 bg-green-50 dark:bg-green-900/30' 
                                : 'border-gray-200 dark:border-gray-700'"
                        >
                            <input
                                type="radio"
                                :value="option.value"
                                v-model="selectedRecognition"
                                class="mt-1 h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                            >
                            <div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-lg">{{ option.emoji }}</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ option.title }}</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ option.description }}</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Communication Preferences -->
        <div v-else-if="currentStep === 3">
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    Communication Preferences
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-300">
                    How would you like to stay informed about your impact?
                </p>
            </div>

            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Email Updates</h3>
                    <div class="space-y-2">
                        <label
                            v-for="pref in communicationPreferences"
                            :key="pref.value"
                            class="flex items-start space-x-3 cursor-pointer p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800"
                        >
                            <input
                                type="checkbox"
                                :value="pref.value"
                                v-model="selectedCommunicationPrefs"
                                class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                            >
                            <div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ pref.title }}</span>
                                <p class="text-xs text-gray-600 dark:text-gray-300">{{ pref.description }}</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 4: Company Information & Team -->
        <div v-else-if="currentStep === 4">
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    Organization & Team Setup
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-300">
                    Final step - set up your organization and team permissions
                </p>
            </div>

            <div class="space-y-8">
                <!-- Company Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Company Information</h3>
                    <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-xl p-4 mb-6">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-purple-900 dark:text-purple-100 text-sm mb-1">Required for payments</h4>
                                <p class="text-purple-800 dark:text-purple-200 text-xs">
                                    This information helps us process payments and provide proper receipts.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Organization Name *
                            </label>
                            <input
                                v-model="companyName"
                                type="text"
                                placeholder="Acme Corporation"
                                class="w-full px-3 py-2 border border-grayish dark:border-gunmetal rounded-lg focus:ring-2 focus:ring-green focus:border-transparent bg-transparent text-rich-black dark:text-seashell text-sm"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Contact Email *
                            </label>
                            <input
                                v-model="contactEmail"
                                type="email"
                                placeholder="contact@acme.com"
                                class="w-full px-3 py-2 border border-grayish dark:border-gunmetal rounded-lg focus:ring-2 focus:ring-green focus:border-transparent bg-transparent text-rich-black dark:text-seashell text-sm"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Address *
                            </label>
                            <input
                                v-model="companyAddress"
                                type="text"
                                placeholder="123 Main Street"
                                class="w-full px-3 py-2 border border-grayish dark:border-gunmetal rounded-lg focus:ring-2 focus:ring-green focus:border-transparent bg-transparent text-rich-black dark:text-seashell text-sm"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                City *
                            </label>
                            <input
                                v-model="companyCity"
                                type="text"
                                placeholder="San Francisco"
                                class="w-full px-3 py-2 border border-grayish dark:border-gunmetal rounded-lg focus:ring-2 focus:ring-green focus:border-transparent bg-transparent text-rich-black dark:text-seashell text-sm"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Postal Code *
                            </label>
                            <input
                                v-model="companyPostalCode"
                                type="text"
                                placeholder="94105"
                                class="w-full px-3 py-2 border border-grayish dark:border-gunmetal rounded-lg focus:ring-2 focus:ring-green focus:border-transparent bg-transparent text-rich-black dark:text-seashell text-sm"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Country *
                            </label>
                            <select
                                v-model="companyCountry"
                                class="w-full px-3 py-2 border border-grayish dark:border-gunmetal rounded-lg focus:ring-2 focus:ring-green focus:border-transparent bg-transparent text-rich-black dark:text-seashell text-sm"
                            >
                                <option value="">Select Country</option>
                                <option value="US">United States</option>
                                <option value="CA">Canada</option>
                                <option value="GB">United Kingdom</option>
                                <option value="DE">Germany</option>
                                <option value="FR">France</option>
                                <option value="NL">Netherlands</option>
                                <option value="SE">Sweden</option>
                                <option value="NO">Norway</option>
                                <option value="DK">Denmark</option>
                                <option value="AU">Australia</option>
                                <option value="JP">Japan</option>
                                <option value="SG">Singapore</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            VAT ID (Optional)
                        </label>
                        <input
                            v-model="companyVatId"
                            type="text"
                            placeholder="DE123456789"
                            class="w-full px-3 py-2 border border-grayish dark:border-gunmetal rounded-lg focus:ring-2 focus:ring-green focus:border-transparent bg-transparent text-rich-black dark:text-seashell text-sm"
                        >
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Include if you need VAT-compliant invoices
                        </p>
                    </div>
                </div>

                <!-- Team Management -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Team Members</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Add team members who can help distribute funds and manage pledges
                    </p>
                    
                    <div class="space-y-3">
                        <div
                            v-for="(member, index) in teamMembers"
                            :key="index"
                            class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg"
                        >
                            <input
                                v-model="member.email"
                                type="email"
                                placeholder="developer@acme.com"
                                class="flex-1 px-3 py-2 border border-grayish dark:border-gunmetal rounded focus:ring-2 focus:ring-green focus:border-transparent bg-transparent text-rich-black dark:text-seashell text-sm"
                            >
                            <select
                                v-model="member.role"
                                class="px-3 py-2 border border-grayish dark:border-gunmetal rounded focus:ring-2 focus:ring-green focus:border-transparent bg-transparent text-rich-black dark:text-seashell text-sm"
                            >
                                <option value="member">Member</option>
                                <option value="admin">Admin</option>
                            </select>
                            <button
                                @click="removeTeamMember(index)"
                                class="p-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                v-if="teamMembers.length > 1"
                            >
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        
                        <button
                            @click="addTeamMember"
                            class="flex items-center space-x-2 px-4 py-2 text-sm text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 border border-dashed border-purple-300 dark:border-purple-700 rounded-lg hover:border-purple-400 dark:hover:border-purple-600 transition-colors duration-200"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            <span>Add team member</span>
                        </button>
                    </div>
                    
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mt-4">
                        <div class="flex items-start space-x-3">
                            <div class="w-5 h-5 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-3 h-3 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-blue-800 dark:text-blue-200 text-xs">
                                    <strong>Members</strong> can view pledges and project status. <strong>Admins</strong> can distribute funds, add/remove team members, and modify company settings.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="flex justify-between items-center mt-12">
            <button
                @click="goBack"
                class="px-6 py-3 text-mondo dark:text-spun-pearl hover:text-rich-black dark:hover:text-seashell transition-colors duration-200"
            >
                ← Back
            </button>

            <div class="space-x-4">
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
                    Start Supporting!
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";

const emit = defineEmits(["completed", "back"]);

const currentStep = ref(1);

// Step 1 - Support Preferences
const selectedTechnologies = ref([]);
const selectedCategories = ref([]);
const specificRepositories = ref('');

// Step 2 - Budget & Recognition
const selectedBudget = ref(null);
const selectedRecognition = ref(null);

// Step 3 - Communication
const selectedCommunicationPrefs = ref([]);

// Step 4 - Company & Team
const companyName = ref('');
const contactEmail = ref('');
const companyAddress = ref('');
const companyCity = ref('');
const companyPostalCode = ref('');
const companyCountry = ref('');
const companyVatId = ref('');
const teamMembers = ref([{ email: '', role: 'admin' }]);

const technologies = [
    { id: 'javascript', name: 'JavaScript', emoji: '🟨' },
    { id: 'typescript', name: 'TypeScript', emoji: '🔷' },
    { id: 'python', name: 'Python', emoji: '🐍' },
    { id: 'java', name: 'Java', emoji: '☕' },
    { id: 'react', name: 'React', emoji: '⚛️' },
    { id: 'vue', name: 'Vue.js', emoji: '💚' },
    { id: 'angular', name: 'Angular', emoji: '🅰️' },
    { id: 'nodejs', name: 'Node.js', emoji: '🟢' },
    { id: 'docker', name: 'Docker', emoji: '🐳' },
    { id: 'kubernetes', name: 'Kubernetes', emoji: '☸️' },
    { id: 'aws', name: 'AWS', emoji: '☁️' },
    { id: 'rust', name: 'Rust', emoji: '🦀' },
];

const projectCategories = [
    { value: 'infrastructure', title: 'Infrastructure & DevOps', description: 'Tools that power development workflows', emoji: '🔧' },
    { value: 'frameworks', title: 'Web Frameworks', description: 'Frontend and backend frameworks', emoji: '🌐' },
    { value: 'libraries', title: 'Code Libraries', description: 'Reusable packages and utilities', emoji: '📦' },
    { value: 'security', title: 'Security Tools', description: 'Cybersecurity and privacy projects', emoji: '🔒' },
    { value: 'data', title: 'Data & Analytics', description: 'Data processing and analysis tools', emoji: '📊' },
    { value: 'mobile', title: 'Mobile Development', description: 'Mobile apps and development tools', emoji: '📱' },
];

const budgetRanges = [
    { value: '100-500', label: '$100-500', description: 'Small projects' },
    { value: '500-2000', label: '$500-2000', description: 'Medium projects' },
    { value: '2000-5000', label: '$2000-5000', description: 'Large projects' },
    { value: '5000+', label: '$5000+', description: 'Enterprise support' },
];

const recognitionOptions = [
    { 
        value: 'full', 
        title: 'Full Public Recognition', 
        description: 'Company name and logo displayed on supported projects',
        emoji: '🏆' 
    },
    { 
        value: 'name-only', 
        title: 'Name Only', 
        description: 'Company name mentioned without logo',
        emoji: '📝' 
    },
    { 
        value: 'anonymous', 
        title: 'Anonymous Support', 
        description: 'Support projects without public attribution',
        emoji: '🕶️' 
    },
];

const communicationPreferences = [
    { 
        value: 'project-updates', 
        title: 'Project Updates', 
        description: 'Receive updates on projects you support' 
    },
    { 
        value: 'impact-reports', 
        title: 'Impact Reports', 
        description: 'Monthly reports showing your impact' 
    },
    { 
        value: 'new-opportunities', 
        title: 'New Opportunities', 
        description: 'Get notified about new projects to support' 
    },
];

const canProceed = computed(() => {
    if (currentStep.value === 1) {
        return selectedTechnologies.value.length > 0 && selectedCategories.value.length > 0;
    }
    if (currentStep.value === 2) {
        return selectedBudget.value !== null && selectedRecognition.value !== null;
    }
    if (currentStep.value === 3) {
        return true; // Communication prefs are optional
    }
    return true;
});

const canComplete = computed(() => {
    return companyName.value && contactEmail.value && companyAddress.value && 
           companyCity.value && companyPostalCode.value && companyCountry.value &&
           teamMembers.value.every(member => member.email && member.role);
});

const toggleTechnology = (techId) => {
    const index = selectedTechnologies.value.indexOf(techId);
    if (index > -1) {
        selectedTechnologies.value.splice(index, 1);
    } else {
        selectedTechnologies.value.push(techId);
    }
};

const toggleCategory = (categoryValue) => {
    const index = selectedCategories.value.indexOf(categoryValue);
    if (index > -1) {
        selectedCategories.value.splice(index, 1);
    } else {
        selectedCategories.value.push(categoryValue);
    }
};

const addTeamMember = () => {
    teamMembers.value.push({ email: '', role: 'member' });
};

const removeTeamMember = (index) => {
    teamMembers.value.splice(index, 1);
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
        const repositoryList = specificRepositories.value
            .split('\n')
            .map(repo => repo.trim())
            .filter(repo => repo.length > 0 && repo.includes('/'));
            
        const formData = {
            technologies: selectedTechnologies.value,
            categories: selectedCategories.value,
            specificRepositories: repositoryList,
            budget: selectedBudget.value,
            recognition: selectedRecognition.value,
            communicationPreferences: selectedCommunicationPrefs.value,
            companyName: companyName.value,
            contactEmail: contactEmail.value,
            companyAddress: companyAddress.value,
            companyCity: companyCity.value,
            companyPostalCode: companyPostalCode.value,
            companyCountry: companyCountry.value,
            companyVatId: companyVatId.value,
            teamMembers: teamMembers.value.filter(member => member.email)
        };
        emit("completed", formData);
    }
};
</script>