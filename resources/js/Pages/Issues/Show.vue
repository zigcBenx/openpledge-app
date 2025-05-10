<template>
    <AppLayout title="Issue Details">
        <div class="flex gap-10" v-if="issue">
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
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps, onMounted } from 'vue';
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
