<template>
    <AppLayout title="Wallet Transactions">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Wallet Transactions
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden">
                    <div class="p-6">
                        <!-- Wallet Summary -->
                        <div class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Current Balance -->
                            <div class="bg-gradient-to-r from-gray-900 to-gray-800 border border-teal-500/30 rounded-lg p-6 shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-xl font-semibold text-gray-200 mb-2">Current Balance</h3>
                                        <p class="text-3xl font-bold text-teal-400">${{ $page.props.user?.wallet_amount }}</p>
                                    </div>
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center">
                                        <i class="fas fa-wallet text-2xl text-white" />
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <button class="bg-green/10 text-green px-4 py-2 rounded-lg">Payout to Stripe</button>
                                </div>
                            </div>

                            <!-- Total Earned -->
                            <div class="bg-gradient-to-r from-gray-900 to-gray-800 border border-teal-500/30 rounded-lg p-6 shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-xl font-semibold text-gray-200 mb-2">Total Earned</h3>
                                        <p class="text-3xl font-bold text-teal-400">${{ $page.props.user?.wallet_amount }}</p>
                                    </div>
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center">
                                        <i class="fas fa-arrow-trend-up text-2xl text-white" />
                                    </div>
                                </div>
                            </div>

                            <!-- Total Paid Out -->
                            <div class="bg-gradient-to-r from-gray-900 to-gray-800 border border-teal-500/30 rounded-lg p-6 shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-xl font-semibold text-gray-200 mb-2">Total Paid Out</h3>
                                        <p class="text-3xl font-bold text-teal-400">${{ $page.props.user?.wallet_amount }}</p>
                                    </div>
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-purple-600 flex items-center justify-center">
                                        <i class="fas fa-money-bill-transfer text-2xl text-white" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Transactions List -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-charcoal-gray text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-charcoal-gray text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-charcoal-gray text-left text-xs leading-4 font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gunmetal divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="transaction in transactions" :key="transaction.id" class="hover:bg-gray-50 dark:hover:bg-charcoal-gray transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                            {{ transaction.created_at }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                                            :class="transaction.amount >= 0 ? 'text-green dark:text-green' : 'text-red-600 dark:text-red-400'">
                                            ${{ transaction.amount }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span v-if="transaction.is_withdrawn"
                                                  class="px-2 py-1 text-xs rounded-full bg-green/10 text-green">
                                                Paid Out
                                            </span>
                                            <span v-else
                                                  class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                                                In wallet
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    transactions: {
        type: Array,
        required: true
    }
});
</script>
