<template>
    <div class="flex justify-center dark:text-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="w-full p-4 border border-gray-200 rounded-lg shadow sm:p-8 dark:border-gray-700">
            <div class="flex items-center justify-between mb-8">
                <h5 class="text-xl leading-none text-gray-900 dark:text-white">Connected Repositories</h5>
            </div>
            <div class="mb-4">
                <div v-if="!repositories.length && !isLoading" class="flex flex-col items-center justify-center w-full h-48">
                    <div class="text-[1.56rem] text-oil dark:text-lavender-mist mt-7 text-center">No repositories found</div>
                    <div class="dark:text-spun-pearl text-tundora text-xs text-center mt-2">Connect your GitHub repositories to start receiving pledges</div>
                </div>

                <table v-else-if="repositories.length > 0" class="w-full border-separate border-spacing-x-0 border-spacing-y-4">
                    <thead>
                        <tr class="text-tundora dark:text-spun-pearl uppercase text-xs text-left">
                            <th class="pb-5 font-normal">Repository</th>
                            <th class="pb-5 font-normal">Issues</th>
                            <th class="pb-5 font-normal">Total Pledges</th>
                            <th class="pb-5 font-normal">Settings</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="repository in repositories" :key="repository.id"
                            class="text-sm bg-white dark:bg-charcoal-gray border-separate">
                            <td class="rounded-bl-md font-medium overflow-hidden border-l-[6px] border-ocean-green dark:border-green pl-3.5 py-6 rounded-tl-md">
                                <div class="flex items-center space-x-2">
                                    <Link 
                                        :href="`/repositories/${repository.title}`"
                                        class="dark:text-white dark:hover:text-green hover:text-green text-base"
                                    >
                                        {{ repository.title }}
                                    </Link>
                                    <a :href="repository.github_url" target="_blank" class="text-tundora dark:text-spun-pearl hover:text-green dark:hover:text-green">
                                        <i class="fa-brands fa-github"></i>
                                    </a>
                                </div>
                            </td>
                            <td class="py-6 dark:text-spun-pearl text-tundora">
                                {{ repository.issues_count }} {{ repository.issues_count === 1 ? 'issue' : 'issues' }}
                            </td>
                            <td class="py-6 dark:text-green text-ocean-green font-medium">
                                ${{ (repository.total_donations / 100).toFixed(2) }}
                            </td>
                            <td class="py-6">
                                <div v-if="repository.settings && repository.settings.allowed_labels && repository.settings.allowed_labels.includes('Pledgeable')"
                                     class="text-xs text-ocean-green dark:text-green">
                                    Requires "Pledgeable" label
                                </div>
                                <div v-else class="text-xs dark:text-spun-pearl text-tundora">
                                    No restrictions
                                </div>
                            </td>
                            <td class="py-6 rounded-br-md rounded-tr-md">
                                <button
                                    @click="openSettings(repository)"
                                    class="p-2 text-tundora dark:text-spun-pearl hover:text-green dark:hover:text-green transition-colors"
                                    title="Repository Settings"
                                >
                                    <i class="fa fa-cog"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-if="isLoading" class="animate-pulse">
                    <table class="w-full border-separate border-spacing-x-0 border-spacing-y-4">
                        <thead>
                            <tr class="text-tundora dark:text-spun-pearl uppercase text-xs text-left">
                                <th class="pb-5 font-normal">Repository</th>
                                <th class="pb-5 font-normal">Issues</th>
                                <th class="pb-5 font-normal">Total Pledges</th>
                                <th class="pb-5 font-normal">Settings</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="i in 3" :key="i" class="text-sm bg-gray-200 dark:bg-gray-700 border-separate">
                                <td class="rounded-bl-md font-medium overflow-hidden border-l-[6px] border-gray-300 dark:border-gray-600 pl-3.5 py-6 rounded-tl-md">
                                    <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-3/4"></div>
                                    <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded w-1/2 mt-2"></div>
                                </td>
                                <td class="py-6">
                                    <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-16"></div>
                                </td>
                                <td class="py-6">
                                    <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-20"></div>
                                </td>
                                <td class="py-6">
                                    <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded w-24"></div>
                                </td>
                                <td class="py-6 rounded-br-md rounded-tr-md">
                                    <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded"></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Settings Modal -->
        <RepositorySettingsModal
            v-if="showModal"
            :repository="selectedRepository"
            @close="closeSettings"
            @updated="handleSettingsUpdated"
        />
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';
import { Link } from '@inertiajs/vue3';
import RepositorySettingsModal from './RepositorySettingsModal.vue';

const repositories = ref([]);
const isLoading = ref(true);
const showModal = ref(false);
const selectedRepository = ref(null);

const fetchRepositories = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get(route('profile.repositories'));
        repositories.value = response.data;
    } catch (error) {
        console.error('Failed to fetch repositories:', error);
    } finally {
        isLoading.value = false;
    }
};

const openSettings = (repository) => {
    selectedRepository.value = repository;
    showModal.value = true;
};

const closeSettings = () => {
    showModal.value = false;
    selectedRepository.value = null;
};

const handleSettingsUpdated = (updatedSettings) => {
    // Update the repository in the list with new settings
    const index = repositories.value.findIndex(repo => repo.id === selectedRepository.value.id);
    if (index !== -1) {
        repositories.value[index].settings = updatedSettings;
    }
    closeSettings();
};

onMounted(() => {
    fetchRepositories();
});
</script>
