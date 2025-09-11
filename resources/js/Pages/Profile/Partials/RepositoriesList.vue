<template>
    <div class="flex justify-center dark:text-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="w-full p-4 border border-gray-200 rounded-lg shadow sm:p-8 dark:border-gray-700">
            <div class="flex items-center justify-between mb-8">
                <h5 class="text-xl leading-none text-gray-900 dark:text-white">Connected Repositories</h5>
            </div>
            <div class="mb-4">
                <div v-if="repositories.length > 0" class="space-y-4">
                    <div v-for="repository in repositories" :key="repository.id"
                         class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2">
                                <h6 class="font-semibold text-gray-900 dark:text-white">{{ repository.title }}</h6>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ repository.issues_count }} {{ repository.issues_count === 1 ? 'issue' : 'issues' }}
                                </span>
                            </div>
                            <p class="text-sm text-green-600 dark:text-green-400 mt-1">
                                ${{ (repository.total_donations / 100).toFixed(2) }} total pledges
                            </p>
                        </div>
                        <button
                            @click="openSettings(repository)"
                            class="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors"
                            title="Repository Settings"
                        >
                            <i class="fa fa-cog"></i>
                        </button>
                    </div>
                </div>
                <p v-else-if="!isLoading" class="text-center text-gray-500 dark:text-gray-400">
                    You have no connected repositories
                </p>
                <div v-if="isLoading" class="animate-pulse space-y-4">
                    <div v-for="i in 3" :key="i" class="flex items-center justify-between p-4 bg-gray-200 dark:bg-gray-700 rounded-lg">
                        <div class="flex-1">
                            <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-3/4"></div>
                            <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded w-1/2 mt-2"></div>
                        </div>
                        <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded"></div>
                    </div>
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
