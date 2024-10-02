<template>
    <div>
        <div class="flex justify-center dark:text-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="w-full p-4 border border-gray-200 rounded-lg shadow sm:p-8 dark:border-gray-700">
                <div class="flex items-center justify-between mb-8">
                    <h5 class="text-xl leading-none text-gray-900 dark:text-white">Favorites</h5>
                    <a :href="route('profile.favorites-show')" class="text-turquoise">View all ></a>
                </div>
                <div class="mb-4">
                    <p class="text-lg">Issues</p>
                    <IssuesTable v-if="issues.length > 0" :issues="issues" :pledged="true" class="hidden md:table" />
                    <p v-else-if="!isLoading" class="text-center">You have no favourite issues</p>
                    <TableRowSkeleton v-if="isLoading" />
                </div>
                <div>
                    <p class="text-lg">Repositories</p>
                    <RepositoriesTable v-if="repositories.length > 0" :repositories="repositories" class="hidden md:table" />
                    <p v-else-if="!isLoading" class="text-center">You have no favourite repositories</p>
                    <TableRowSkeleton v-if="isLoading" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';
import IssuesTable from '@/Components/Custom/IssuesTable.vue';
import RepositoriesTable from '@/Components/Custom/RepositoriesTable.vue';
import TableRowSkeleton from '@/Components/Custom/TableRowSkeleton.vue';

const isLoading = ref(true);
const issues = ref([])
const repositories = ref([])

const fetchFavorites = async () => {
    try {
        const response = await axios.get(route('profile.favorites'));
        ({ issues: issues.value, repositories: repositories.value } = response.data);
    } catch (error) {
        console.error('Failed to fetch favorites:', error);
    } finally {
        isLoading.value = false
    }
};

onMounted(() => {
    fetchFavorites();
});
</script>
