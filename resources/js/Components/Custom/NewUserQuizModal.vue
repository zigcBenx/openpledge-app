<template>
    <Modal :show="isQuizModalVisible" :closeable="false" overflow-visible @close="handleClose">
        <div :class="[
            'p-5',
            'flex',
            'flex-col',
            'justify-between',
            currentQuizStep === 0 && newUserQuizSubmission.intent && newUserQuizSubmission.intent !== UserIntent.CONTRIBUTOR ? 'h-40dvh' : 'h-31dvh'
        ]">
            <ProgressStepper :currentStep="currentQuizStep" :totalSteps="quizStepsCount" />

            <div class="mt-6 flex-grow">
                <div v-if="currentQuizStep === 0">
                    <h2 class="text-xl font-bold leading-none text-gray-900 dark:text-white">You are here to:</h2>
                    <div class="mt-10 flex flex-col space-y-2">
                        <label v-for="option in userIntentQuizOptions" :key="option.value" class="flex items-center">
                            <Checkbox v-on:change="newUserQuizSubmission.intent = $event.target.value"
                                :checked="newUserQuizSubmission.intent === option.value" :value="option.value"
                                :disabled="newUserQuizSubmission.intent === option.value" />
                            <span class="pl-2 leading-none text-gray-900 dark:text-white">
                                {{ option.label }}
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ option.description ? ` - ${option.description}` : '' }}
                                </span>
                            </span>
                        </label>
                        <div class="py-4" v-if="newUserQuizSubmission.intent && newUserQuizSubmission.intent !== UserIntent.CONTRIBUTOR">
                            <span class="leading-none text-gray-900 dark:text-white">
                                Company name (if applicable):
                            </span>
                            <Input inputClass="h-30" v-model:input="newUserQuizSubmission.companyName" />
                        </div>
                    </div>
                </div>

                <div v-if="currentQuizStep === 1">
                    <h2 class="text-xl font-bold leading-none text-gray-900 dark:text-white">What tech stacks or tools
                        do you love working with? (Pick as many as you like!)</h2>
                    <MultiSelect class="mt-8" :options="programmingLanguages"
                        :value="newUserQuizSubmission.programmingLanguages"
                        @input="newUserQuizSubmission.programmingLanguages = $event" />
                </div>

                <div v-if="currentQuizStep === 2">
                    <h2 class="text-xl font-bold leading-none text-gray-900 dark:text-white">Which job title best
                        describes your role?</h2>
                    <Select class="mt-8" :data="jobTitleQuizOptions" :value="newUserQuizSubmission.jobTitle"
                        @input="newUserQuizSubmission.jobTitle = $event" />
                </div>
            </div>

            <div class="flex justify-between space-x-4 mt-auto">
                <Button class="px-4 h-11" color="secondary" @click="handlePageBack" :disabled="currentQuizStep === 0">
                    Back
                </Button>

                <Button class="px-8 h-11" color="primary" @click="handlePageNext" :disabled="isNextPageDisabled()">
                    {{ currentQuizStep === quizStepsCount - 1 ? 'Confirm' : 'Next' }}
                </Button>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { ref } from 'vue';
import Checkbox from '@/Components/Checkbox.vue';
import Modal from '@/Components/Modal.vue';
import Button from '@/Components/Button.vue';
import { useToast } from "vue-toastification";
import ProgressStepper from '@/Components/Form/ProgressStepper.vue';
import Select from '@/Components/Select.vue';
import MultiSelect from '@/Components/MultiSelect.vue';
import Input from '@/Components/Input.vue';

const props = defineProps({
    isQuizModalVisible: Boolean,
    programmingLanguages: Array
});

const emit = defineEmits(['update:isQuizModalVisible']);

const currentQuizStep = ref(0);
const quizStepsCount = 3;
const newUserQuizSubmission = ref({
    intent: undefined,
    programmingLanguages: [],
    jobTitle: undefined,
    companyName: undefined
});

const UserIntent = Object.freeze({
    CONTRIBUTOR: 'userIsContributor',
    PLEDGER: 'userIsPledger',
    BOTH: 'userIsBoth',
});

const userIntentQuizOptions = [
    { value: UserIntent.CONTRIBUTOR, label: "Contribute and earn", description: "solves issues and gets paid" },
    { value: UserIntent.PLEDGER, label: "Pledge money to open source", description: "donates and allocates funds" },
    { value: UserIntent.BOTH, label: "Both" }
];

const jobTitleQuizOptions = [
    "Full Stack Developer", "Frontend Developer", "Backend Developer", "DevOps Engineer",
    "Data Scientist", "Software Engineer", "UI/UX Designer", "Product Manager", "Other"
];

const handlePageBack = () => {
    if (currentQuizStep.value > 0) {
        currentQuizStep.value = newUserQuizSubmission.value.intent === UserIntent.PLEDGER ? 0 : currentQuizStep.value - 1;
    }
};

const handlePageNext = () => {
    if (currentQuizStep.value < quizStepsCount - 1) {
        currentQuizStep.value = newUserQuizSubmission.value.intent === UserIntent.PLEDGER ? quizStepsCount - 1 : currentQuizStep.value + 1;
    } else {
        handleNewUserQuizSubmission();
    }
};

const isNextPageDisabled = () => {
    if (currentQuizStep.value === 0 && !newUserQuizSubmission.value.intent) {
        return true;
    }
    if (currentQuizStep.value === 1 && (newUserQuizSubmission.value.programmingLanguages.length === 0)) {
        return true;
    }
    return false;
};

const handleClose = () => {
    emit('update:isQuizModalVisible', false);
    document.body.style.overflow = null;
};

const handleNewUserQuizSubmission = () => {
    const toast = useToast();
    axios.post(route('user.new-user-quiz'), { newUserQuizSubmission: newUserQuizSubmission.value })
        .then(response => {
            toast.success(response.data.message);
            handleClose();
        })
        .catch(error => {
            toast.error('Something went wrong!');
            console.error(error);
        });
};
</script>

<style scoped>
.h-40dvh {
    height: 40dvh;
}

.h-31dvh {
    height: 31dvh;
}
</style>
