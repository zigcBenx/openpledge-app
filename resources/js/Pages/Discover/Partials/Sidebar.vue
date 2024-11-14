<template>
  <GithubRepositoryConnect id="repository-connect-container" />
  <TrendingToday />
  <TopContributors />
  <TopDonors />
</template>
<script setup>
  import GithubRepositoryConnect from '@/Components/Custom/GithubRepositoryConnect.vue';
  import TrendingToday from '@/Components/Custom/TrendingToday.vue';
  import TopContributors from '@/Components/Custom/TopContributors.vue';
  import TopDonors from '@/Components/Custom/TopDonors.vue';
</script>