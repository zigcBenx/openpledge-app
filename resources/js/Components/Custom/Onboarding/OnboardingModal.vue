<template>
    <teleport to="body">
        <transition leave-active-class="duration-200">
            <div v-show="isOnboardingVisible" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
                <!-- Backdrop -->
                <transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div v-show="isOnboardingVisible" class="fixed inset-0 bg-black opacity-50 dark:opacity-80" />
                </transition>

                <!-- Modal -->
                <transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                >
                    <div
                        v-show="isOnboardingVisible"
                        class="relative w-full max-w-6xl h-[80vh] bg-white dark:bg-charcoal-gray rounded-2xl shadow-2xl transform transition-all"
                        @click.stop
                    >
                        <div class="h-full p-6 sm:p-8 lg:p-12">
                            <!-- Goal Selection Step -->
                            <GoalSelectionStep
                                v-if="currentStep === 'goal-selection'"
                                @goal-selected="handleGoalSelection"
                            />

                            <!-- Contributor Flow -->
                            <ContributorFlow
                                v-else-if="currentStep === 'contributor-flow'"
                                @completed="handleFlowCompleted"
                                @back="goBack"
                            />

                            <!-- Maintainer Flow -->
                            <MaintainerFlow
                                v-else-if="currentStep === 'maintainer-flow'"
                                @completed="handleFlowCompleted"
                                @back="goBack"
                            />

                            <!-- Pledger Flow -->
                            <PledgerFlow
                                v-else-if="currentStep === 'pledger-flow'"
                                @completed="handleFlowCompleted"
                                @back="goBack"
                            />
                        </div>
                    </div>
                </transition>
            </div>
        </transition>
    </teleport>
</template>

<script setup>
import { ref, onUnmounted, watch } from "vue";
import GoalSelectionStep from "./GoalSelectionStep.vue";
import ContributorFlow from "./ContributorFlow.vue";
import MaintainerFlow from "./MaintainerFlow.vue";
import PledgerFlow from "./PledgerFlow.vue";
import { useToast } from "vue-toastification";

const props = defineProps({
    isOnboardingVisible: Boolean,
});

const emit = defineEmits(["update:isOnboardingVisible"]);

const toast = useToast();

// Check if resuming from GitHub auth or GitHub App installation
const storedGoal = localStorage.getItem('onboarding_goal');
const storedStep = localStorage.getItem('onboarding_step');
const maintainerInstalling = localStorage.getItem('maintainer_onboarding_installing');

const currentStep = ref('goal-selection');
const selectedGoal = ref(null);

// Resume onboarding if returning from GitHub auth
if (storedGoal) {
    selectedGoal.value = storedGoal;
    if (storedGoal === 'userIsContributor') {
        currentStep.value = 'contributor-flow';
    } else if (storedGoal === 'userIsMaintainer') {
        currentStep.value = 'maintainer-flow';
    } else if (storedGoal === 'userIsPledger') {
        currentStep.value = 'pledger-flow';
    }
    // Clear onboarding_goal from localStorage (but keep onboarding_step for the flow components to handle)
    localStorage.removeItem('onboarding_goal');
    // Note: onboarding_step is removed by the individual flow components after they use it
}

// Resume maintainer flow if returning from GitHub App installation
if (maintainerInstalling === 'true') {
    selectedGoal.value = 'userIsMaintainer';
    currentStep.value = 'maintainer-flow';
    // Don't remove the flag yet - let MaintainerFlow handle it
}

// Body scroll management
watch(() => props.isOnboardingVisible, (newValue) => {
    if (newValue) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = null;
    }
});

onUnmounted(() => {
    document.body.style.overflow = null;
});

const handleGoalSelection = async (goal) => {
    selectedGoal.value = goal;

    // Navigate to appropriate flow based on goal
    if (goal === 'userIsContributor') {
        currentStep.value = 'contributor-flow';
    } else if (goal === 'userIsMaintainer') {
        currentStep.value = 'maintainer-flow';
    } else if (goal === 'userIsPledger') {
        currentStep.value = 'pledger-flow';
    }
};

const handleFlowCompleted = async (formData) => {
    try {
        const response = await axios.post(route("user.new-user-quiz"), {
            newUserQuizSubmission: {
                intent: selectedGoal.value,
                ...formData
            },
        });
        toast.success(response.data.message || "Welcome to OpenPledge!");
        emit("update:isOnboardingVisible", false);
    } catch (error) {
        toast.error("Something went wrong!");
        console.error(error);
    }
};

const goBack = () => {
    currentStep.value = 'goal-selection';
};
</script>
