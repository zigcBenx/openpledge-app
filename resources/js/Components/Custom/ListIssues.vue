<script setup>
    import { Link } from '@inertiajs/vue3';

    defineProps({
        issues: Object,
        title: String,
        pledged: {
            type: Boolean,
            default: false,
        },
    });

</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-4 mt-2">
            <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white">{{ title }}</h5>
        </div>
        <div class="text-gray-700 dark:text-white">
            <ul>
                <li v-for="issue in issues" :key="issue.id" class="mt-2">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <img class="w-8 h-8 rounded-full" :src="issue.user_avatar ?? issue.user.avatar_url" alt="Neil image">
                        </div>
                        <div class="flex-1 min-w-0 ms-4">
                            <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                <a :href="issue.html_url">
                                    {{ issue.title }}
                                </a>
                            </p>
                            <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                {{ issue.user?.login }}
                            </p>
                        </div>
                        <div v-if="pledged" class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                            {{ issue.donations_sum_amount || 0 }} €
                        </div>
                        <div class="ml-3 flex">
                            <slot name="actions" :issue="issue"></slot>
                            <Link
                                v-if="issue.repository_id"
                                :href="`/issues/${issue.id}`"
                                class="flex items-center justify-center w-full py-2 px-4 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                View Details
                            </Link>

                            <a :href="issue.html_url" target="_blank" class="ml-2 flex items-center justify-center w-full py-2 px-4 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            <i class="fa-solid fa-code-pull-request"></i>
                                Contribute
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>