<template>
    <div>
        <div class="flex justify-center dark:text-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="w-full p-4 border border-gray-200 rounded-lg shadow sm:p-8 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white">Installed repositories</h5>
                    <a href="#" class="text-turquoise">View all ></a>
                </div>
                <div v-if="installedRepositories.length > 0">
                    <ul>
                        <li v-for="(installedRepository, index) in installedRepositories" :key="index">
                            <a :href="getRepositoryUrlByTitle(installedRepository.title)" target="_blank" class="text-turquoise">{{ installedRepository.title }}</a>
                        </li>
                    </ul>
                </div>
                <p v-else class="text-gray-500 text-center">You have no installed repositories</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const appUrl = import.meta.env.VITE_APP_URL
const installedRepositories = ref([]);

const fetchRepositories = async () => {
    try {
        const response = await axios.get(`/user/repositories`);
        installedRepositories.value = response.data;
    } catch (error) {
        console.error('Failed to fetch repositories:', error);
    }
};

const getRepositoryUrlByTitle = (title) => {
    return appUrl + '/repositories/' + title
}

onMounted(() => {
    fetchRepositories();
});
</script>
