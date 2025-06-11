<template>
    <Modal
        :show="isQuizModalVisible"
        :closeable="false"
        overflow-visible
        @close="handleClose"
    >
        <div
            :class="[
                'p-5',
                'flex',
                'flex-col',
                'justify-between',
                currentQuizStep === 0 &&
                newUserQuizSubmission.intent &&
                newUserQuizSubmission.intent !== UserIntent.CONTRIBUTOR
                    ? 'h-120'
                    : 'h-80',
            ]"
        >
            <ProgressStepper
                :currentStep="currentQuizStep"
                :totalSteps="quizStepsCount"
            />

            <div class="mt-6 flex-grow">
                <div v-if="currentQuizStep === 0">
                    <h2
                        class="text-xl font-bold leading-none text-gray-900 dark:text-white"
                    >
                        You are here to:
                    </h2>
                    <div class="mt-10 flex flex-col space-y-2">
                        <label
                            v-for="option in userIntentQuizOptions"
                            :key="option.value"
                            class="flex items-center"
                        >
                            <Checkbox
                                v-on:change="
                                    newUserQuizSubmission.intent =
                                        $event.target.value
                                "
                                :checked="
                                    newUserQuizSubmission.intent ===
                                    option.value
                                "
                                :value="option.value"
                                :disabled="
                                    newUserQuizSubmission.intent ===
                                    option.value
                                "
                            />
                            <span
                                class="pl-2 leading-none text-gray-900 dark:text-white"
                            >
                                {{ option.label }}
                                <span
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    {{
                                        option.description
                                            ? ` - ${option.description}`
                                            : ""
                                    }}
                                </span>
                            </span>
                        </label>
                        <div
                            class="py-4 flex flex-col gap-3"
                            v-if="
                                newUserQuizSubmission.intent &&
                                newUserQuizSubmission.intent !==
                                    UserIntent.CONTRIBUTOR
                            "
                        >
                            <span
                                class="leading-none text-gray-900 dark:text-white"
                            >
                                Company name (if applicable):
                            </span>
                            <Input
                                inputClass="h-30"
                                v-model:input="
                                    newUserQuizSubmission.companyName
                                "
                            />
                            <div v-if="hasCompanyName" class="flex flex-col gap-3 mt-2">
                                <div class="flex flex-col gap-2">
                                    <span class="leading-none text-gray-900 dark:text-white">
                                        Address:
                                    </span>
                                    <Input
                                        inputClass="h-30"
                                        placeholder="e.g. Marktstr. 5"
                                        v-model:input="newUserQuizSubmission.companyAddress"
                                    />
                                </div>
                            </div>
                            <div v-if="hasCompanyName" class="flex flex-col gap-3 mt-2">
                                <div class="flex flex-col gap-2">
                                    <span class="leading-none text-gray-900 dark:text-white">
                                        City:
                                    </span>
                                    <Input
                                        inputClass="h-30"
                                        placeholder="e.g. Berlin"
                                        v-model:input="newUserQuizSubmission.companyCity"
                                    />
                                </div>
                            </div>
                            <div v-if="hasCompanyName" class="flex flex-col gap-3 mt-2">
                                <div class="flex flex-col gap-2">
                                    <span class="leading-none text-gray-900 dark:text-white">
                                        Postal code:
                                    </span>
                                    <Input
                                        inputClass="h-30"
                                        placeholder="e.g. 10115"
                                        v-model:input="newUserQuizSubmission.companyPostalCode"
                                    />
                                </div>
                            </div>
                            <div v-if="hasCompanyName" class="flex flex-col gap-3 mt-2">
                                <div class="flex flex-col gap-2">
                                    <span class="leading-none text-gray-900 dark:text-white">
                                        State:
                                    </span>
                                    <Input
                                        inputClass="h-30"
                                        placeholder="e.g. Bavaria"
                                        v-model:input="newUserQuizSubmission.companyState"
                                    />
                                </div>
                            </div>
                            <div v-if="hasCompanyName" class="flex flex-col gap-3 mt-2">
                                <div class="flex flex-col gap-2">
                                    <span class="leading-none text-gray-900 dark:text-white">
                                        Country:
                                    </span>
                                    <CountrySelect
                                        v-model="newUserQuizSubmission.companyCountry"
                                        class="!w-80"
                                    />
                                </div>
                            </div>
                            <div v-if="hasCompanyName" class="flex flex-col gap-3 mt-2">
                                <span class="leading-none text-gray-900 dark:text-white">
                                    VAT ID:
                                </span>
                                <Input
                                    inputClass="h-30"
                                    placeholder="e.g. DE123456789"
                                    v-model:input="newUserQuizSubmission.companyVatId"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="currentQuizStep === 1">
                    <h2
                        class="text-xl font-bold leading-none text-gray-900 dark:text-white"
                    >
                        What tech stacks or tools do you love working with?
                        (Pick as many as you like!)
                    </h2>
                    <MultiSelect
                        class="mt-8"
                        :options="programmingLanguages"
                        :value="newUserQuizSubmission.programmingLanguages"
                        @input="handleProgrammingLanguagesInput"
                    />
                </div>

                <div v-if="currentQuizStep === 2">
                    <h2
                        class="text-xl font-bold leading-none text-gray-900 dark:text-white"
                    >
                        Which job title best describes your role?
                    </h2>
                    <Select
                        class="mt-8"
                        :data="jobTitleQuizOptions"
                        :value="newUserQuizSubmission.jobTitle"
                        @input="newUserQuizSubmission.jobTitle = $event"
                    />
                </div>
            </div>

            <div class="flex justify-between space-x-4 mt-auto">
                <Button
                    class="px-4 h-11"
                    color="secondary"
                    @click="handlePageBack"
                    :disabled="currentQuizStep === 0"
                >
                    Back
                </Button>

                <Button
                    class="px-8 h-11"
                    color="primary"
                    @click="handlePageNext"
                    :disabled="isNextPageDisabled()"
                >
                    {{
                        currentQuizStep === quizStepsCount - 1
                            ? "Confirm"
                            : "Next"
                    }}
                </Button>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { ref, computed } from "vue";
import Checkbox from "@/Components/Checkbox.vue";
import Modal from "@/Components/Modal.vue";
import Button from "@/Components/Button.vue";
import { useToast } from "vue-toastification";
import ProgressStepper from "@/Components/Form/ProgressStepper.vue";
import Select from "@/Components/Select.vue";
import MultiSelect from "@/Components/MultiSelect.vue";
import Input from "@/Components/Input.vue";
import CountrySelect from "@/Components/Custom/CountrySelect.vue";


const props = defineProps({
    isQuizModalVisible: Boolean,
    programmingLanguages: Array,
});

const emit = defineEmits(["update:isQuizModalVisible"]);

const currentQuizStep = ref(0);
const quizStepsCount = 3;
const newUserQuizSubmission = ref({
    intent: undefined,
    programmingLanguages: [],
    jobTitle: undefined,
    companyName: undefined,
    companyAddress: undefined,
    companyCity: undefined,
    companyPostalCode: undefined,
    companyState: undefined,
    companyCountry: undefined,
    companyVatId: undefined,
});

const UserIntent = Object.freeze({
    CONTRIBUTOR: "userIsContributor",
    PLEDGER: "userIsPledger",
    BOTH: "userIsBoth",
});

const userIntentQuizOptions = [
    {
        value: UserIntent.CONTRIBUTOR,
        label: "Contribute and earn",
        description: "solves issues and gets paid",
    },
    {
        value: UserIntent.PLEDGER,
        label: "Pledge money to open source",
        description: "donates and allocates funds",
    },
    { value: UserIntent.BOTH, label: "Both" },
];

const jobTitleQuizOptions = [
    "Full Stack Developer",
    "Frontend Developer",
    "Backend Developer",
    "DevOps Engineer",
    "Data Scientist",
    "Software Engineer",
    "UI/UX Designer",
    "Product Manager",
    "Other",
];

const handlePageBack = () => {
    if (currentQuizStep.value > 0) {
        currentQuizStep.value =
            newUserQuizSubmission.value.intent === UserIntent.PLEDGER
                ? 0
                : currentQuizStep.value - 1;
    }
};

const handlePageNext = () => {
    if (currentQuizStep.value < quizStepsCount - 1) {
        currentQuizStep.value =
            newUserQuizSubmission.value.intent === UserIntent.PLEDGER
                ? quizStepsCount - 1
                : currentQuizStep.value + 1;
    } else {
        handleNewUserQuizSubmission();
    }
};

const isNextPageDisabled = () => {
    const currentStep = currentQuizStep.value;

    if (currentStep === 0) {
        if (!hasIntent.value) {
            return true;
        }

        if (hasCompanyName.value && isCompanyFormIncomplete.value) {
            return true;
        }
    }
    if (currentStep === 1) {
        if (!hasSelectedProgrammingLanguages.value) {
            return true;
        }
    }
    return false;
};

const handleClose = () => {
    emit("update:isQuizModalVisible", false);
    document.body.style.overflow = null;
};

const handleNewUserQuizSubmission = () => {
    const toast = useToast();
    axios
        .post(route("user.new-user-quiz"), {
            newUserQuizSubmission: newUserQuizSubmission.value,
        })
        .then((response) => {
            toast.success(response.data.message);
            handleClose();
        })
        .catch((error) => {
            toast.error("Something went wrong!");
            console.error(error);
        });
};

const handleProgrammingLanguagesInput = (e) => {
    if (e instanceof Event) {
        return;
    }
    newUserQuizSubmission.value.programmingLanguages = e;
};

const hasCompanyName = computed(() => {
    const name = newUserQuizSubmission.value.companyName;
    return !!name && name.toString().trim() !== '';
});

const isNonEmpty = (field) =>
    computed(() => !!newUserQuizSubmission.value[field] && newUserQuizSubmission.value[field].toString().trim() !== '');

const requiredCompanyFields = [
    'companyName',
    'companyAddress',
    'companyPostalCode',
    'companyCity',
    'companyState',
    'companyCountry',
    'companyVatId',
];

const companyFieldValidators = Object.fromEntries(
    requiredCompanyFields.map((field) => [field, isNonEmpty(field)])
);

const isCompanyFormIncomplete = computed(() =>
    requiredCompanyFields.some((field) => !companyFieldValidators[field].value)
);

const hasSelectedProgrammingLanguages = computed(() => {
  return newUserQuizSubmission.value.programmingLanguages.length > 0;
});

const hasIntent = computed(() => {
    return !!newUserQuizSubmission.value.intent;
});
</script>

<style scoped>
.h-104 {
    height: 25rem;
}
</style>
