<template>
    <div class="flex justify-center dark:text-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="w-full p-4 border border-gray-200 rounded-lg shadow sm:p-8 dark:border-gray-700">
            <div class="flex items-center justify-between mb-8">
                <h5 class="text-xl leading-none text-gray-900 dark:text-white">{{ title }}</h5>
                <a :href="viewAllLink" class="text-turquoise">View all ></a>
            </div>
            <div class="mb-4">
                <IssuesTable v-if="issues.length > 0" :issues="issues" :pledged="true" class="hidden md:table" />
                <p v-else-if="!isLoading" class="text-center">{{ noIssuesMessage }}</p>
                <TableRowSkeleton v-if="isLoading" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';
import IssuesTable from '@/Components/Custom/IssuesTable.vue';
import TableRowSkeleton from '@/Components/Custom/TableRowSkeleton.vue';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    routeName: {
        type: String,
        required: true,
    },
    noIssuesMessage: {
        type: String,
        required: true,
    },
    viewAllLink: {
        type: String,
        default: '#',
    }
});

const isLoading = ref(true);
const issues = ref([])

const fetchIssues = async () => {
    try {
        const response = await axios.get(route(props.routeName));
        issues.value = response.data;
    } catch (error) {
        console.error('Failed to fetch issues:', error);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    fetchIssues();
});
</script>