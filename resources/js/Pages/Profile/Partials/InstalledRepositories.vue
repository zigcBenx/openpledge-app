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
                            <Link
                                :href="route('repositories.show', { githubUser: installedRepository.title.split('/')[0], repository: installedRepository.title.split('/')[1] })"
                                class="text-turquoise">
                            {{ installedRepository.title }}
                            </Link>
                        </li>
                    </ul>
                </div>
                <SkeletonLoader v-else-if="isLoading" v-for="n in 5" :key="n" class="w-full h-5 mb-5" />
                <p v-else class="text-gray-500 text-center">You have no installed repositories</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { Link } from '@inertiajs/vue3';
import SkeletonLoader from '@/Components/SkeletonLoader.vue';

const installedRepositories = ref([]);
const isLoading = ref(true);

const fetchRepositories = async () => {
    try {
        const response = await axios.get(route('profile.repositories'));
        installedRepositories.value = response.data;
    } catch (error) {
        console.error('Failed to fetch repositories:', error);
    } finally {
        isLoading.value = false
    }
};

onMounted(() => {
    fetchRepositories();
});
</script>
