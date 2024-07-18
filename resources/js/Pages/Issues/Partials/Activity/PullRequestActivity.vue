<script setup>
  import Avatar from '@/Components/Avatar.vue';
  import dayjs from 'dayjs';

  const props = defineProps({
    pullRequestActivity: {
        type: Object,
        required: true,
        default: () => {
            return {}
        }
    },
  })
</script>

<template>
  <div class="flex gap-3 pl-3">
      <Avatar :url="pullRequestActivity.actor.avatar_url" />
      <div class="pt-1.5">
        <p class="flex gap-1.5 mb-4 text-sm text-tundora dark:text-spun-pearl">
          <span class="font-medium text-mondo dark:text-seashell">{{ pullRequestActivity.actor.login }}</span>
          {{ pullRequestActivity.event }} a pull request
        </p>
      </div>
      <span class="pt-1.5 text-xs text-tundora dark:text-spun-pearl ml-auto whitespace-nowrap">{{ dayjs(pullRequestActivity.created_at).fromNow() }}</span>
  </div>
</template>