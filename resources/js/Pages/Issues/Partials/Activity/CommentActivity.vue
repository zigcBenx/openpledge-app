<script setup>
  import Avatar from '@/Components/Avatar.vue';
  import dayjs from 'dayjs';

  const props = defineProps({
    commentActivity: {
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
      <Avatar :url="commentActivity.actor.avatar_url" />
      <div class="pt-1.5">
        <p class="flex gap-1.5 mb-4 text-sm text-tundora dark:text-spun-pearl">
          <span class="font-medium text-mondo dark:text-seashell">{{ commentActivity.actor.login }}</span>
          commented
        </p>
        <p class="text-sm text-oil dark:text-lavender-mist">
          {{ commentActivity.body }}
        </p>
      </div>
      <span class="pt-1.5 text-xs text-tundora dark:text-spun-pearl ml-auto whitespace-nowrap">{{ dayjs(commentActivity.created_at).fromNow() }}</span>
  </div>
</template>