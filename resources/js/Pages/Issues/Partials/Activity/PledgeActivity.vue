<script setup>
import Avatar from '@/Components/Avatar.vue';
import dayjs from 'dayjs';

const props = defineProps({
  pledgeActivity: {
    type: Object,
    required: true,
    default: () => {
      return {}
    }
  },
})

const formatExpireDate = (date) => {
  return dayjs(date, 'YYYY-MM-DD').format('MMM D YYYY');
};
</script>

<template>
  <div class="flex gap-3 pl-3">
    <Avatar :url="pledgeActivity.user.profile_photo_url" />
    <div class="pt-1.5">
      <p class="flex gap-1.5 mb-4 text-sm text-tundora dark:text-spun-pearl">
        <span class="font-medium text-mondo dark:text-seashell">{{ pledgeActivity.user.name }}</span>
        pledged
        <span class="font-medium text-purple-heart">${{ pledgeActivity.amount }}</span>
      </p>
      <p v-if="pledgeActivity.expire_date" class="text-xs text-tundora dark:text-spun-pearl">
        Valid until {{ formatExpireDate(pledgeActivity.expire_date) }}
      </p>
    </div>
    <span class="pt-1.5 text-xs text-tundora dark:text-spun-pearl ml-auto whitespace-nowrap">{{
      dayjs(pledgeActivity.created_at).fromNow() }}</span>
  </div>
</template>