<template>
    <AppLayout title="Issue Details">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Issue Details
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white flex justify-center dark:text-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
                        <div class="flex">
                            <img :src="issue.user_avatar" class="rounded-full h-20 w-20 object-cover mr-2">
                            <div class="flex items-center justify-between mb-4">
                                <h5 class="text-2xl font-bold leading-none text-gray-900 dark:text-white">{{ issue.title }}</h5>
                                <a :href="issue.github_url" target="_blank"><i class="fa-brands fa-github mr-1 text-4xl"></i></a>
                            </div>
                        </div>
                        
                        <div class="text-gray-700 dark:text-white">
                            <p class="text-4xl">Pledged so far: {{ issue.donation_sum }}€</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="actions max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
                <PrimaryButton
                    class="ms-3"
                    @click="alert('Adding pledge')"
                >
                    Add pledge
                </PrimaryButton>

                <PrimaryButton
                    class="ms-3"
                    @click="alert('Adding pledge')"
                >
                    Solve
                </PrimaryButton>
            </div>

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
                <div class="bg-white flex justify-center dark:text-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
                        Donations:
                            <u>
                                <li v-for="donation in issue.donations" :key="donation.id">
                                    <b>{{ donation.amount }}€</b> by {{ donation.donor_id }} for {{ donation.donatable_id }}({{ donation.donatable_type }})
                                </li>
                            </u>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useToast } from "vue-toastification";
import PrimaryButton from '@/Components/PrimaryButton.vue';

export default {
    props: {
        issue: Object,
    },
    components: { AppLayout, PrimaryButton },

    data() {
        return {};
    },
    methods: {
    }
}
</script>