<template>
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="$emit('close')"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                Repository Settings
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                {{ repository.title }}
                            </p>

                            <form @submit.prevent="submitForm" class="space-y-6">
                                <!-- Require Pledgeable Label -->
                                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                    <div class="flex items-start">
                                        <input
                                            id="require_pledgeable_label"
                                            v-model="form.require_pledgeable_label"
                                            type="checkbox"
                                            class="h-4 w-4 text-turquoise focus:ring-turquoise border-gray-300 rounded mt-1"
                                        >
                                        <div class="ml-3">
                                            <label for="require_pledgeable_label" class="text-sm font-medium text-gray-900 dark:text-white">
                                                Require "Pledgeable" Label
                                            </label>
                                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                                                Only issues with the "Pledgeable" label can receive pledges.
                                                <span class="font-medium text-green-600 dark:text-green-400">We'll automatically create this label in your GitHub repository if it doesn't exist.</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Donation Expiry -->
                                <div>
                                    <div class="flex items-center">
                                        <input
                                            id="enable_donation_expiry"
                                            v-model="form.enable_donation_expiry"
                                            type="checkbox"
                                            class="h-4 w-4 text-turquoise focus:ring-turquoise border-gray-300 rounded"
                                        >
                                        <label for="enable_donation_expiry" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Enable Pledge Expiry
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Automatically expire pledges after a specified period
                                    </p>
                                </div>

                                <!-- Default Expiry Days -->
                                <div v-if="form.enable_donation_expiry">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Default Expiry Days
                                    </label>
                                    <input
                                        v-model="form.default_expiry_days"
                                        type="number"
                                        min="1"
                                        max="365"
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-turquoise focus:border-turquoise dark:bg-gray-700 dark:text-white sm:text-sm"
                                        placeholder="30"
                                    >
                                </div>

                                <!-- Min Donation Amount -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Minimum Donation Amount ($)
                                    </label>
                                    <input
                                        v-model="form.min_donation_amount"
                                        type="number"
                                        step="0.01"
                                        min="0.01"
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-turquoise focus:border-turquoise dark:bg-gray-700 dark:text-white sm:text-sm"
                                        placeholder="5.00"
                                    >
                                </div>

                                <!-- Max Donation Amount -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Maximum Donation Amount ($)
                                    </label>
                                    <input
                                        v-model="form.max_donation_amount"
                                        type="number"
                                        step="0.01"
                                        min="0.01"
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-turquoise focus:border-turquoise dark:bg-gray-700 dark:text-white sm:text-sm"
                                        placeholder="1000.00"
                                    >
                                </div>

                                <!-- Error Message -->
                                <div v-if="errorMessage" class="text-red-600 dark:text-red-400 text-sm">
                                    {{ errorMessage }}
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button
                        @click="submitForm"
                        :disabled="loading"
                        type="button"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-turquoise text-base font-medium text-white hover:bg-turquoise-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-turquoise sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                    >
                        <span v-if="loading">Saving...</span>
                        <span v-else>Save Settings</span>
                    </button>
                    <button
                        @click="$emit('close')"
                        :disabled="loading"
                        type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    repository: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['close', 'updated']);

const availableLabels = [
    'test', 'feature', 'bug', 'enhancement',
    'documentation', 'question', 'invalid', 'duplicate', 'security'
];

const loading = ref(false);
const errorMessage = ref('');

const form = reactive({
    require_pledgeable_label: false,
    allowed_labels: [],
    enable_donation_expiry: false,
    default_expiry_days: null,
    min_donation_amount: null,
    max_donation_amount: null,
});

const initializeForm = () => {
    if (props.repository.settings) {
        form.allowed_labels = props.repository.settings.allowed_labels || [];
        // Check if 'Pledgeable' is in allowed_labels to set the checkbox
        form.require_pledgeable_label = form.allowed_labels.includes('Pledgeable');
        form.enable_donation_expiry = props.repository.settings.enable_donation_expiry || false;
        form.default_expiry_days = props.repository.settings.default_expiry_days;
        form.min_donation_amount = props.repository.settings.min_donation_amount ?
            (props.repository.settings.min_donation_amount / 100) : null;
        form.max_donation_amount = props.repository.settings.max_donation_amount ?
            (props.repository.settings.max_donation_amount / 100) : null;
    }
};

const submitForm = async () => {
    loading.value = true;
    errorMessage.value = '';

    try {
        const response = await axios.put(
            route('repositories.settings.update', { repositoryId: props.repository.id }),
            {
                ...form,
                // Convert dollars to cents for backend
                min_donation_amount: form.min_donation_amount ? form.min_donation_amount * 100 : null,
                max_donation_amount: form.max_donation_amount ? form.max_donation_amount * 100 : null,
            }
        );

        emit('updated', response.data.settings);
    } catch (error) {
        errorMessage.value = error.response?.data?.message || 'An error occurred while saving settings.';
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    initializeForm();
});
</script>
