<template>
    <AppLayout title="Issue Details">
        <div v-if="issue">
            <!-- Desktop Layout (unchanged from original) -->
            <div class="hidden lg:flex gap-10">
                <div class="w-[100%]">
                    <Breadcrumbs :links="breadcrumbsData" wrapperClass="mb-[5.25rem]" />
                    <IssueTopDetails :issue="issue" @onFavoriteClick="handleFavoriteClick" />
                    <IssueDetails :issue="issue" class="mt-[3.375rem]" />
                    <Activity :issue="issue" class="mt-14 pb-10" id="issue-activity-container" />
                </div>
                <div class="w-[70%] pt-[6.43rem]">
                    <IssueDetailsSidebar
                        :issue="issue"
                        :stripePublicKey="stripePublicKey"
                        :isAuthenticated="isAuthenticated"
                        id="issue-sidebar-container"
                    />
                </div>
            </div>

            <!-- Mobile Layout -->
            <div class="lg:hidden">
                <Breadcrumbs :links="breadcrumbsData" wrapperClass="mb-4" />

                <!-- Issue Header -->
                <IssueTopDetails :issue="issue" @onFavoriteClick="handleFavoriteClick" />

                <!-- Mobile: Pledge Section First -->
                <div class="mt-4">
                    <IssueDetailsSidebar
                        :issue="issue"
                        :stripePublicKey="stripePublicKey"
                        :isAuthenticated="isAuthenticated"
                    />
                </div>

                <!-- Mobile: Toggle Details Button -->
                <button
                    @click="showDetails = !showDetails"
                    class="w-full mt-4 p-3 bg-white dark:bg-charcoal-gray rounded-lg border-2 border-gray-200 dark:border-gray-700 hover:border-green dark:hover:border-green transition-colors flex items-center justify-between"
                >
                    <span class="text-sm font-medium dark:text-white">
                        {{ showDetails ? 'Hide' : 'Show' }} Issue Details & Activity
                    </span>
                    <svg
                        class="w-5 h-5 dark:text-white transition-transform"
                        :class="{ 'rotate-180': showDetails }"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- Toggleable Details -->
                <div v-show="showDetails" class="mt-4 space-y-6">
                    <IssueDetails :issue="issue" />
                    <Activity :issue="issue" id="issue-activity-container" class="pb-10" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps, onMounted, ref } from 'vue';
import IssueTopDetails from './Partials/IssueTopDetails.vue';
import IssueDetails from './Partials/IssueDetails.vue';
import Activity from './Partials/Activity/Activity.vue';
import IssueDetailsSidebar from './Partials/IssueDetailsSidebar/IssueDetailsSidebar.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import { useToast } from "vue-toastification";
import { getIssueTour } from '@/utils/onboardingWalkthrough.js';
import { router } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
const props = defineProps({
    issue: {
        type: Object,
        required: true,
        default: () => {
            return {}
        }
    },
    stripePublicKey: String
})

const breadcrumbsData = [{
    title: 'Discover',
    url: '/discover/issues'
}, {
    title: props.issue?.title,
    url: `/issues/${props.issue.id}`
}];

const page = usePage();
const isAuthenticated = page.props.auth.user !== null;
const showDetails = ref(false);

function handleFavoriteClick() {
    const toast = useToast()

    if (!isAuthenticated) {
        toast.error('Please log in to add this issue to favorites');
        return;
    }

    axios.post(route('favorites.store'), {
        favorable_id: props.issue.id,
        favorable_type: 'Issue',
    })
        .then(response => {
            const toastOptions = response.data.message.includes('added')
                ? {
                    onClick: () => router.visit(route('profile.favorites-show')),
                    toastClassName: 'cursor-pointer hover:opacity-90'
                }
                : {};
            toast.success(response.data.message, toastOptions);
            props.issue.favorite = !props.issue.favorite
        })
        .catch(error => {
            toast.error('Something went wrong!')
            console.error(error);
        });
}

onMounted(() => {
    if (localStorage.getItem('isTutorialInProgress') === 'true') {
        const issueTour = getIssueTour();
        issueTour.start();
    }
});
</script>
