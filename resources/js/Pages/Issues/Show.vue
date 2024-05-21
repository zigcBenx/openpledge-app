<template>
    <AppLayout title="Issue Details">
        <div class="flex gap-10">
            <div>
                <Breadcrumbs :links="breadcrumbsData" wrapperClass="mb-[5.25rem]" />
                <IssueTopDetails :issue="issueState" @onFavoriteClick="handleFavoriteClick" />
                <IssueDetails :issue="issueState" class="mt-[3.375rem]" />
                <Activity class="mt-14 pb-10" />
            </div>
            <div class="pt-[6.43rem]">
                <IssueDetailsSidebar :issue="issue" :stripePublicKey="stripePublicKey" />
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import IssueTopDetails from './Partials/IssueTopDetails.vue';
import IssueDetails from './Partials/IssueDetails.vue';
import Activity from './Partials/Activity/Activity.vue';
import IssueDetailsSidebar from './Partials/IssueDetailsSidebar/IssueDetailsSidebar.vue';
import MoneyInput from '@/Components/MoneyInput.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import StripePayment from '@/Components/Custom/StripePayment.vue';
import { issues } from '../../assets/mockedData.js'

const issueState = ref(issues[0]);
const breadcrumbsData = [{
    title: 'Discover',
    url: '/issues'
}, {
    title: issueState.value.title,
    url: `/issues/${issueState.value.id}`
}];

export default {
    props: {
        issue: Object,
        stripePublicKey: String
    },
    components: { 
        AppLayout, 
        PrimaryButton, 
        DialogModal, 
        MoneyInput, 
        SecondaryButton, 
        StripePayment,
        IssueTopDetails,
        IssueDetails,
        Activity,
        Breadcrumbs,
        IssueDetailsSidebar
     },
    data() {
        return {
            issueState,
            breadcrumbsData
        };
    },
    methods: {
        handleFavoriteClick() {
            issueState.value.favorite = !issueState.value.favorite;
        }
    }
}
</script>