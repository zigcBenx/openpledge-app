<template>
  <GithubIssueConnect />
  <TrendingToday />
  <TopContributors />
  <TopList 
        containerClass="mt-6"
        title="Top donators" 
        subtitle="A list of top users based on their activity"
    >
      <li v-for="item in topDonators" :key="item.id" class="flex justify-between items-center py-1.5">
        <UserListItem :user="item.user" :languages="item.languages" />
        <span class="text-purple-heart font-medium text-xs">{{ item.donations }}</span>
      </li>
  </TopList>
</template>
<script setup>
  import GithubIssueConnect from '@/Components/Custom/GithubIssueConnect.vue';
  import TopList from '@/Components/Custom/TopList.vue';
  import UserListItem from './UserListItem.vue';
  import TrendingToday from '@/Components/Custom/TrendingToday.vue';
  import TopContributors from '@/Components/Custom/TopContributors.vue';

  defineProps({
    topDonators: {
      type: Array,
      validator: function (value) {
          return value.every(item => {
          return typeof item === 'object' && item.hasOwnProperty('id') && item.hasOwnProperty('user') && item.hasOwnProperty('donations');
        });
      }
    }
  })
</script>