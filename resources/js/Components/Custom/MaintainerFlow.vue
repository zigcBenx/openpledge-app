<template>
    <div class="max-w-4xl mx-auto">
        <!-- Progress Indicator -->
        <div class="mb-8 flex items-center justify-center space-x-2">
            <div class="w-3 h-3 bg-grayish dark:bg-gunmetal rounded-full"></div>
            <div class="w-3 h-3 bg-green rounded-full"></div>
        </div>

        <!-- Step 1: Repository Connection -->
        <div v-if="currentStep === 1">
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 0C4.477 0 0 4.484 0 10.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0110 4.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.203 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.942.359.31.678.921.678 1.856 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0020 10.017C20 4.484 15.522 0 10 0z" clip-rule="evenodd" />
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    Connect Your Repository
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-300">
                    Let's get your GitHub repository set up to receive funding
                </p>
            </div>

            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6 mb-8">
                <div class="flex items-start space-x-4">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-blue-900 dark:text-blue-100 mb-1">How it works</h3>
                        <p class="text-blue-800 dark:text-blue-200 text-sm">
                            We'll help you connect your GitHub repository so that supporters can fund specific issues. 
                            You maintain full control over your project while receiving financial support from the community.
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Repository URL
                    </label>
                    <input
                        v-model="repositoryUrl"
                        type="url"
                        placeholder="https://github.com/username/repository"
                        class="w-full px-4 py-3 border border-grayish dark:border-gunmetal rounded-lg focus:ring-2 focus:ring-green focus:border-transparent bg-transparent text-rich-black dark:text-seashell"
                    >
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Make sure you have admin access to this repository
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Project Description
                    </label>
                    <textarea
                        v-model="projectDescription"
                        rows="3"
                        placeholder="Brief description of your project and its goals..."
                        class="w-full px-4 py-3 border border-grayish dark:border-gunmetal rounded-lg focus:ring-2 focus:ring-green focus:border-transparent bg-transparent text-rich-black dark:text-seashell resize-none"
                    ></textarea>
                </div>
            </div>
        </div>


        <!-- Step 2: Bot Settings -->
        <div v-else-if="currentStep === 2">
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z" clip-rule="evenodd" />
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    Bot Configuration
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-300">
                    Customize how OpenPledge bot interacts with your repository
                </p>
            </div>

            <div class="space-y-8">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                        When should pledges be accepted?
                    </label>
                    <div class="space-y-3">
                        <label class="flex items-start space-x-3 cursor-pointer p-4 rounded-lg border hover:bg-gray-50 dark:hover:bg-gray-800"
                            :class="pledgeAcceptance === 'all-issues' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-700'">
                            <input
                                type="radio"
                                value="all-issues"
                                v-model="pledgeAcceptance"
                                class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                            >
                            <div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Accept pledges on all issues</span>
                                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">Anyone can pledge money to any issue in your repository</p>
                            </div>
                        </label>
                        <label class="flex items-start space-x-3 cursor-pointer p-4 rounded-lg border hover:bg-gray-50 dark:hover:bg-gray-800"
                            :class="pledgeAcceptance === 'labeled-issues' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-700'">
                            <input
                                type="radio"
                                value="labeled-issues"
                                v-model="pledgeAcceptance"
                                class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                            >
                            <div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Only on labeled issues</span>
                                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">Pledges only accepted on issues with specific labels (e.g., 'help-wanted', 'funding-welcome')</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div v-if="pledgeAcceptance === 'labeled-issues'" class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Required Labels (comma-separated)
                    </label>
                    <input
                        v-model="requiredLabels"
                        type="text"
                        placeholder="help-wanted, funding-welcome, bounty"
                        class="w-full px-3 py-2 border border-grayish dark:border-gunmetal rounded-lg focus:ring-2 focus:ring-green focus:border-transparent bg-transparent text-rich-black dark:text-seashell text-sm"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                        Bot Comment Customization
                    </label>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">
                        Customize the message our bot posts when someone pledges to an issue
                    </p>
                    <textarea
                        v-model="botCommentTemplate"
                        rows="4"
                        class="w-full px-4 py-3 border border-grayish dark:border-gunmetal rounded-lg focus:ring-2 focus:ring-green focus:border-transparent bg-transparent text-rich-black dark:text-seashell resize-none text-sm"
                        :placeholder="defaultBotComment"
                    ></textarea>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        Available variables: {pledger_name}, {amount}, {issue_title}, {repo_name}
                    </p>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-2">Preview</h4>
                    <div class="bg-transparent border border-grayish dark:border-gunmetal rounded p-3 text-sm">
                        <div class="flex items-start space-x-2">
                            <div class="w-6 h-6 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">OP</span>
                            </div>
                            <div class="flex-1">
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">openpledge-bot commented</div>
                                <div class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ previewComment }}</div>
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
                    Connect Repository!
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";

const emit = defineEmits(["completed", "back"]);

const currentStep = ref(1);
const totalSteps = 2;
const repositoryUrl = ref('');
const projectDescription = ref('');
const pledgeAcceptance = ref('all-issues');
const requiredLabels = ref('');
const botCommentTemplate = ref('');

const defaultBotComment = `🎉 Great news! {pledger_name} has pledged {amount} to this issue.

This means there's now funding available for anyone who solves this issue. Feel free to start working on it!

💡 New to OpenPledge? Check out how it works: [openpledge.io](https://openpledge.io)`;



const previewComment = computed(() => {
    const template = botCommentTemplate.value || defaultBotComment;
    return template
        .replace('{pledger_name}', 'John Doe')
        .replace('{amount}', '$50')
        .replace('{issue_title}', 'Example Issue Title')
        .replace('{repo_name}', 'example/repository');
});

const canProceed = computed(() => {
    if (currentStep.value === 1) {
        return repositoryUrl.value.length > 0 && projectDescription.value.length > 0;
    }
    if (currentStep.value === 2) {
        return pledgeAcceptance.value && (pledgeAcceptance.value === 'all-issues' || requiredLabels.value.length > 0);
    }
    return true;
});

const canComplete = computed(() => {
    return pledgeAcceptance.value && (pledgeAcceptance.value === 'all-issues' || requiredLabels.value.length > 0);
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
    if (canComplete.value) {
        const formData = {
            repositoryUrl: repositoryUrl.value,
            projectDescription: projectDescription.value,
            pledgeAcceptance: pledgeAcceptance.value,
            requiredLabels: requiredLabels.value.split(',').map(label => label.trim()).filter(label => label.length > 0),
            botCommentTemplate: botCommentTemplate.value || defaultBotComment
        };
        emit("completed", formData);
    }
};
</script>