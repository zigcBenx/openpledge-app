<template>
    <DialogModal max-width="xl" :show="true" @close="$emit('close')" :closeable="true">
        <template #title>
            <span>Repository Settings</span>
        </template>
        <template #content>
            <div class="mb-4 dark:text-spun-pearl text-tundora text-sm">
                {{ repository.title }}
            </div>

            <form @submit.prevent="submitForm" class="flex flex-col gap-6">
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
                                            <label for="require_pledgeable_label" class="text-sm font-medium dark:text-lavender-mist text-oil">
                                                Require "Pledgeable" Label
                                            </label>
                                            <p class="text-xs dark:text-spun-pearl text-tundora mt-1">
                                                Only issues with the "Pledgeable" label can receive pledges.
                                                <span class="font-medium text-ocean-green dark:text-green">We'll automatically create this label in your GitHub repository if it doesn't exist.</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>

<!--                &lt;!&ndash; Donation Expiry &ndash;&gt;-->
<!--                <div>-->
<!--                    <div class="flex items-center">-->
<!--                        <input-->
<!--                            id="enable_donation_expiry"-->
<!--                            v-model="form.enable_donation_expiry"-->
<!--                            type="checkbox"-->
<!--                            class="h-4 w-4 text-ocean-green dark:text-green focus:ring-ocean-green dark:focus:ring-green border-gray-300 rounded"-->
<!--                        >-->
<!--                        <label for="enable_donation_expiry" class="ml-2 text-sm font-medium dark:text-lavender-mist text-oil">-->
<!--                            Enable Pledge Expiry-->
<!--                        </label>-->
<!--                    </div>-->
<!--                    <p class="text-xs dark:text-spun-pearl text-tundora mt-1">-->
<!--                        Automatically expire pledges after a specified period-->
<!--                    </p>-->
<!--                </div>-->

<!--                &lt;!&ndash; Default Expiry Days &ndash;&gt;-->
<!--                <div v-if="form.enable_donation_expiry">-->
<!--                    <label class="block text-sm font-medium dark:text-lavender-mist text-oil mb-1">-->
<!--                        Default Expiry Days-->
<!--                    </label>-->
<!--                    <input-->
<!--                        v-model="form.default_expiry_days"-->
<!--                        type="number"-->
<!--                        min="1"-->
<!--                        max="365"-->
<!--                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-ocean-green dark:focus:ring-green focus:border-ocean-green dark:focus:border-green dark:bg-charcoal-gray dark:text-lavender-mist sm:text-sm"-->
<!--                        placeholder="30"-->
<!--                    >-->
<!--                </div>-->

<!--                &lt;!&ndash; Min Donation Amount &ndash;&gt;-->
<!--                <div>-->
<!--                    <label class="block text-sm font-medium dark:text-lavender-mist text-oil mb-1">-->
<!--                        Minimum Donation Amount ($)-->
<!--                    </label>-->
<!--                    <input-->
<!--                        v-model="form.min_donation_amount"-->
<!--                        type="number"-->
<!--                        step="0.01"-->
<!--                        min="0.01"-->
<!--                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-ocean-green dark:focus:ring-green focus:border-ocean-green dark:focus:border-green dark:bg-charcoal-gray dark:text-lavender-mist sm:text-sm"-->
<!--                        placeholder="5.00"-->
<!--                    >-->
<!--                </div>-->

<!--                &lt;!&ndash; Max Donation Amount &ndash;&gt;-->
<!--                <div>-->
<!--                    <label class="block text-sm font-medium dark:text-lavender-mist text-oil mb-1">-->
<!--                        Maximum Donation Amount ($)-->
<!--                    </label>-->
<!--                    <input-->
<!--                        v-model="form.max_donation_amount"-->
<!--                        type="number"-->
<!--                        step="0.01"-->
<!--                        min="0.01"-->
<!--                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-ocean-green dark:focus:ring-green focus:border-ocean-green dark:focus:border-green dark:bg-charcoal-gray dark:text-lavender-mist sm:text-sm"-->
<!--                        placeholder="1000.00"-->
<!--                    >-->
<!--                </div>-->

                <!-- Error Message -->
                <div v-if="errorMessage" class="text-red-600 dark:text-red-400 text-sm">
                    {{ errorMessage }}
                </div>
            </form>
        </template>
        <template #footer>
            <Row class="pt-9">
                <Col :span="{sm:6}">
                    <Button @click="$emit('close')" color="outline" :disabled="loading">
                        Cancel
                    </Button>
                </Col>
                <Col :span="{sm:6}">
                    <Button color="primary" @click="submitForm" :disabled="loading">
                        <span v-if="loading">Saving...</span>
                        <span v-else>Save Settings</span>
                    </Button>
                </Col>
            </Row>
        </template>
    </DialogModal>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import DialogModal from '@/Components/DialogModal.vue';
import Button from '@/Components/Button.vue';
import Row from '@/Components/Grid/Row.vue';
import Col from '@/Components/Grid/Col.vue';

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
