<template>
    <Link v-if="$page.props.auth?.user"
        :href="route('wallet.transactions')"
        class="cursor-pointer inline-flex uppercase items-center px-1 pb-2 pt-1 border-b-2 border-transparent text-sm leading-5 text-rich-black dark:text-green hover:scale-105 transition-transform">
            <div class="flex items-center gap-2">
                <div class="bg-gradient-to-r from-gray-900 to-gray-800 border border-teal-500/30 rounded-lg px-3 py-1.5 shadow-lg shadow-teal-500/5 hover:border-teal-500/50">
                    <div class="flex items-center gap-2">
                    <div class="w-5 h-5 rounded-full bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center">
                        <i class="fas fa-wallet" />
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center">
                            <span class="text-xs text-gray-400 uppercase tracking-wide">WALLET</span>
                            <div :class="walletAmount> 0 ? 'bg-teal-400' : 'bg-yellow-400'" class="ml-1.5 w-2 h-2 rounded-full animate-pulse"></div>
                        </div>
                        <span :class="walletAmount> 0 ? 'text-teal-400' : 'text-gray-400'" class="font-medium tracking-wide">{{ walletAmount }}$</span>
                    </div>
                    </div>
                </div>
            </div>
    </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const walletAmount = computed(() => usePage().props.user?.wallet_amount_available?.toFixed(2))
</script>
