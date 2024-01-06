<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    repositories: Array,
});

</script>

<template>
    <AppLayout title="Repositories">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <Link :href="route('home')" :active="route().current('home')">Issues</Link> / Repositories
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white flex justify-center dark:text-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white">Repositories</h5>
                    </div>
                        <div class="flow-root">
                            <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                                <li v-for="repository in repositories" :key="repository.id" class="py-3 sm:py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <img class="w-8 h-8 rounded-full" :src="repository.user_avatar" alt="Neil image">
                                        </div>
                                        <div class="flex-1 min-w-0 ms-4">
                                            <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                                <a :href="repository.github_url">
                                                    {{ repository.title }}
                                                </a>
                                            </p>
                                            <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                                tet
                                            </p>
                                        </div>
                                        <div class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                            {{ repository.donations_sum || 0 }} €
                                        </div>
                                        <div class="ml-3 flex">
                                            <Link
                                             :href="`/repositories/${repository.id}`"
                                             class="flex items-center justify-center w-full py-2 px-4 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                             View Details
                                            </Link>
                                            
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <div class="flex flex-col w-full items-center">
                                <p>Can't find your favourite repository?</p>
                                <Link class="underline" :href="route('repositories-request-get')">
                                    Request repository
                                </Link>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>
