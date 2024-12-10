<template>
    <TopList 
        containerClass="mt-6"
        title="Top donors" 
        subtitle="A list of top donors based on their donations"
    >
        <li v-for="donor in donors" :key="donor.user?.id" class="flex justify-between overflow-hidden py-1.5">
            <UserListItem :userImageUrl="donor.user?.profile_photo_url || 'https://avatars.githubusercontent.com/u/189997707?u=7a98f8258503c5cdb33ebeefc4554ee78b8c0adf&v=4'" :userName="donor.user?.name || 'Anonymous Pledger'" />
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