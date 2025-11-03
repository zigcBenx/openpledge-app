<template>
    <TopList
        containerClass="mt-6"
        title="Trending today"
        subtitle="List of top donated open issues today"
    >
        <li v-for="item in trendingTodayItems" :key="item.id" class="flex justify-between overflow-hidden py-1.5">
            <span class="w-6/12 pl-1.5 border-l-2 rounded-sm dark:border-green border-ocean-green text-oil dark:text-lavender-mist text-xs">
                <Link :href="route('issues.show', {issue: item.id})">
                    {{ item.title }}
                </Link>
            </span>
            <span class="w-6/12 text-mondo dark:text-lavender-mist font-medium text-xs">
                <Link :href="route('repositories.show',{ githubUser: item.repository.title.split('/')[0], repository: item.repository.title.split('/')[1] })">
                    {{ item.repository.title }}
                </Link>
            </span>
            <span class="w-2/12 text-purple-heart font-medium text-xs" title="Sum of pledges">{{ item.today_donations_sum }} $</span>
        </li>
    </TopList>
</template>

<script setup>
import TopList from '@/Components/Custom/TopList.vue'
import { onMounted, ref } from 'vue'
import axios from 'axios'
import { Link } from '@inertiajs/vue3';

onMounted(() => {
    fetchTrendingIssues()
})

const trendingTodayItems = ref([])

async function fetchTrendingIssues() {
    const response = await axios.get(route('trending-today-issues'));
    trendingTodayItems.value = response.data;
}
</script>
