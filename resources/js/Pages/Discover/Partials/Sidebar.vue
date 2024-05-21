<template>
  <GithubIssueConnect />
  <TopList 
      containerClass="mt-6"
      title="Trending today" 
      subtitle="List of top donated open issues today"
  >
    <li v-for="item in trendingToday" :key="item.id" class="flex justify-between overflow-hidden py-1.5">
      <span class="pl-1.5 border-l-2 rounded-sm dark:border-green border-ocean-green text-oil dark:text-lavender-mist text-xs">{{ item.title }}</span>
      <span class="text-mondo dark:text-lavender-mist font-medium text-xs">{{ item.repository }}</span>
      <span class="text-purple-heart font-medium text-xs">{{ item.donations }}</span>
    </li>
  </TopList>
  <TopList 
        containerClass="mt-6"
        title="Top contributors" 
        subtitle="A list of top users based on their activity"
    >
      <li v-for="item in topContributors" :key="item.id" class="flex justify-between items-center py-1.5">
        <UserListItem :user="item.user" :languages="item.languages" />
        <span class="text-purple-heart font-medium text-xs">{{ item.issues }} issues</span>
      </li>
  </TopList>
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

  defineProps({
    trendingToday: {
      type: Array,
      validator: function (value) {
          return value.every(item => {
          return typeof item === 'object' && item.hasOwnProperty('id') && item.hasOwnProperty('title') && item.hasOwnProperty('repository') && item.hasOwnProperty('donations');
        });
      }
    },
    topContributors: {
      type: Array,
      validator: function (value) {
          return value.every(item => {
          return typeof item === 'object' && item.hasOwnProperty('id') && item.hasOwnProperty('user') && item.hasOwnProperty('languages') && item.hasOwnProperty('issues');
        });
      }
    },
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