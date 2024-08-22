<template>
    <AppLayout title="Issue Details">
        <div class="flex gap-10" v-if="issue">
            <div>
                <Breadcrumbs :links="breadcrumbsData" wrapperClass="mb-[5.25rem]" />
                <IssueTopDetails :issue="issue" @onFavoriteClick="handleFavoriteClick" />
                <IssueDetails :issue="issue" class="mt-[3.375rem]" />
                <Activity :issue="issue" class="mt-14 pb-10" />
            </div>
            <div class="pt-[6.43rem]">
                <IssueDetailsSidebar :issue="issue" :stripePublicKey="stripePublicKey" />
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps } from 'vue';
import IssueTopDetails from './Partials/IssueTopDetails.vue';
import IssueDetails from './Partials/IssueDetails.vue';
import Activity from './Partials/Activity/Activity.vue';
import IssueDetailsSidebar from './Partials/IssueDetailsSidebar/IssueDetailsSidebar.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import { useToast } from "vue-toastification";

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

function handleFavoriteClick() {
    const toast = useToast()
    axios.post(route('favorites.store'), {
        favorable_id: props.issue.id,
        favorable_type: 'Issue',
    })
    .then(response => {
        toast.success(response.data.message)
        props.issue.favorite = !props.issue.favorite
    })
    .catch(error => {
        toast.error('Something went wrong!')
        console.error(error);
    });
}
</script>