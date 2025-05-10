<template>
    <TopList 
        containerClass="mt-6"
        title="Top pledgers" 
        subtitle="A list of top pledgers based on their pledges"
    >
        <li v-for="donor in donors" :key="donor.user?.id" class="flex justify-between overflow-hidden py-1.5">
            <UserListItem :userImageUrl="donor.user?.profile_photo_url" :userName="donor.user?.name" />
            <span class="text-purple-heart font-medium text-xs">{{ donor.total_donated }} €</span>
        </li>
    </TopList>
</template>

<script setup>
import TopList from '@/Components/Custom/TopList.vue'
import { onMounted, ref } from 'vue'
import axios from 'axios'
import UserListItem from '@/Pages/Discover/Partials/UserListItem.vue';

onMounted(() => {
    fetchTopDonors()
})

const donors = ref([])

async function fetchTopDonors() {
    const response = await axios.get(route('top-donors'));
    donors.value = response.data
}
</script>