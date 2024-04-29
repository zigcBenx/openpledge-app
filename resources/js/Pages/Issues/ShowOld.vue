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
                            <p class="text-4xl">Pledged so far: {{ donationSum }}€</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="actions max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
                <PrimaryButton
                    class="ms-3"
                    @click="displayDonationModal = true"
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
                        ISSUE ACTIVITY:
                            <ol class="relative border-s border-gray-200 dark:border-gray-700">
                                <li v-for="donation in donations" :key="donation.id" class="mb-10 ms-4">
                                    <div class="absolute w-3 h-3 bg-gray-200 rounded-full mt-1.5 -start-1.5 border border-white dark:border-gray-900 dark:bg-gray-700"></div>
                                    <time class="mb-1 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">{{ formatDate(donation.created_at) }}</time>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Donation</h3>
                                    <p class="text-base font-normal text-gray-500 dark:text-gray-400">
                                        <b>{{ donation.amount }}€</b> by {{ donation.donor_id }} for {{ donation.donatable_id }}({{ donation.donatable_type }})
                                    </p>
                                </li>
                            </ol>


                    </div>
                </div>
            </div>
        </div>
        <!-- Donation Modal -->
        <DialogModal :show="displayDonationModal" @close="displayDonationModal = false" :closeable="false">
            <template #title>
                Pledge a donation
            </template>

            <template #content>
                <div>
                    <money-input
                        id="donation"
                        v-model="form.donation"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Donation amount"
                        :disabled="donationPledged"
                        required
                    />
                    <stripe-payment v-if="donationPledged" :donation="form.donation" @success="insertDonation" @cancel="cancelDonation" />
                </div>
            </template>

            <template #footer v-if="!donationPledged">
                <SecondaryButton @click="displayDonationModal = false">
                    Cancel
                </SecondaryButton>

                <PrimaryButton
                    class="ms-3"
                    @click="pledgeDonation()"
                >
                    Pledge 💸
                </PrimaryButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useToast } from "vue-toastification";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import MoneyInput from '@/Components/MoneyInput.vue';
import { format } from 'date-fns';
import StripePayment from '@/Components/Custom/StripePayment.vue';

export default {
    props: {
        issue: Object,
    },
    components: { AppLayout, PrimaryButton, DialogModal, MoneyInput, SecondaryButton, StripePayment },

    data() {
        return {
            displayDonationModal: false,
            donations: [],
            form: {
                donation: 0
            },
            donationPledged: false
        };
    },
    mounted() {
        this.fetchDonationsForIssue()
    },
    computed: {
        donationSum() {
            const sum = this.donations.reduce((acc, donation) => acc + Number(donation.amount), 0);
            return Number(sum).toFixed(2);        }
    },
    methods: {
        formatDate(date) {
            return format(new Date(date), 'MMMM yyyy');
        },
        fetchDonationsForIssue() {
            const toast = useToast()
            axios.get('/issues/' + this.issue.id + '/donations')
            .then((response) => {
                this.donations = response.data
            }).catch((err) => {
                toast.error('Something went wrong!')
            });
        },
        pledgeDonation() {
            this.donationPledged = true
        },
        insertDonation() {
            const toast = useToast()
            axios.post('/donations', {
                amount: this.form.donation,
                donor_id: this.$page.props.auth.user.id,
                donatable_id: this.issue.id,
                donatable_type: 'App\\Models\\Issue',
                transaction_id: null, // todo: stripe id?
            })
            .then((response) => {
                toast.success('Donation pledged!')
                this.displayDonationModal = false
                this.fetchDonationsForIssue()
            }).catch((err) => {
                toast.error('Something went wrong!')
            });
        },
        cancelDonation() {
            this.displayDonationModal = false
            this.donationPledged = false
        }
    }
}
</script>