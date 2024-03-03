<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    campaigns: Array,
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


                            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th scope="col" class="px-6 py-3">
                                                Name
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                <div class="flex items-center">
                                                    Enabled
                                                </div>
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                <div class="flex items-center">
                                                    Is recurring fore new users
                                                </div>
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                <div class="flex items-center">
                                                    Start time
                                                </div>
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                <span class="flex items-center">Actions</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="campaign in campaigns" :key="campaign.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ campaign.name }}
                                            </th>
                                            <td class="px-6 py-4">
                                                {{ campaign.is_enabled }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ campaign.is_recurring_for_new_users }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ campaign.start_time }}
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <a :href="'/campaigns/' + campaign.id + '/edit'" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
