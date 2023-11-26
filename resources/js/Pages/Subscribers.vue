<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    subscribers: Array,
    counters: Array,
});

</script>

<template>
    <AppLayout title="Subscribers">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Subscribers
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white text-gray-800 dark:bg-gray-800 dark:text-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="flex justify-center">
                    <div v-for="counter in counters" :key="counter.name" class="block text-center m-4 max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                        <h2 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ counter.count }}</h2>
                        <small>{{ counter.name }}</small>
                    </div>
                </div>

                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <th scope="col" class="px-6 py-3">Email</th>
                            <th scope="col" class="px-6 py-3">Source</th>
                            <th scope="col" class="px-6 py-3">Subscribed at</th>
                        </thead>
                        <tbody>
                            <tr v-for="subscriber in subscribers" :key="subscriber.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td scope="col" class="px-6 py-3">{{ subscriber.email }}</td>
                                <td scope="col" class="px-6 py-3">{{ subscriber.source }}</td>
                                <td scope="col" class="px-6 py-3">{{ subscriber.created_at }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                    
                </div>
            </div>
        </div>
    </AppLayout>
</template>
