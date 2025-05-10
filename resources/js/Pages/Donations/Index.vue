<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    donations: Array,
});

</script>

<template>
    <AppLayout title="Issues">
        <template #header>
            <!-- <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <Link :href="route('home')" :active="route().current('home')">Issues</Link> / Repositories
            </h2> -->
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white flex justify-center dark:text-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
                        <div class="flow-root">
                            <ul>
                                <li v-for="donation in donations" :key="donation.id">{{ donation.net_amount }} by {{ donation.donor_id }} for {{ donation.donatable_id }}({{ donation.donatable_type }})</li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>
