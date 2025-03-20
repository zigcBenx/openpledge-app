<template>
    <AppLayout title="Donation Details">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Donation Details
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white flex justify-center dark:text-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white">{{ donation.net_amount }}</h5>
                        </div>
                        <div class="text-gray-700 dark:text-white">
                            <ul>
                                <li>ID: {{ donation.id }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
                <div class="bg-white flex justify-center dark:text-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
                       sdfsdf
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';


export default {
    props: {
        donation: Object,
    },
    components: { AppLayout },

    data() {
        return {};
    },
    methods: {
    }
}
</script>
