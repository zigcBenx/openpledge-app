<template>
    <TopList 
        containerClass="mt-6"
        title="Top contributors" 
        subtitle="A list of top users based on their activity"
    >
        <li v-for="contributor in contributors" :key="contributor.id" class="flex justify-between overflow-hidden py-1.5">
            <UserListItem :userImageUrl="contributor.avatar_url" :userName="contributor.name" />
            <span class="text-purple-heart font-medium text-xs">{{ contributor.issueCount }} issues</span>
        </li>
    </TopList>
</template>

<script setup>
import TopList from '@/Components/Custom/TopList.vue'
import { onMounted, ref } from 'vue'
import axios from 'axios'
import UserListItem from '@/Pages/Discover/Partials/UserListItem.vue';

onMounted(() => {
    fetchTopContributors()
})

const contributors = ref([])

async function fetchTopContributors() {
    const response = await axios.get(route('top-contributors'));
    contributors.value = response.data

}
</script>