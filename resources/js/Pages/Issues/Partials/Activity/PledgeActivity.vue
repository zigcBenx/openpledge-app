<script setup>
import Avatar from '@/Components/Avatar.vue';
import dayjs from 'dayjs';
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  pledgeActivity: {
    type: Object,
    required: true,
    default: () => {
      return {}
    }
  },
});

const timeLeft = ref('');

const calculateTimeLeft = () => {
  if (!props.pledgeActivity.expire_date) return;
  
  const now = dayjs();
  const expiry = dayjs(props.pledgeActivity.expire_date);
  const diff = expiry.diff(now);
  
  const days = Math.floor(diff / (1000 * 60 * 60 * 24));
  const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
  
  if (days > 0) {
    timeLeft.value = `Expires in ${days}d ${hours}h`;
  } else if (hours > 0) {
    timeLeft.value = `Expires in ${hours}h ${minutes}m`;
  } else if (minutes > 0) {
    timeLeft.value = `Expires in ${minutes} minutes`;
  } else {
    timeLeft.value = 'Expired';
  }
};

let timer;
onMounted(() => {
  calculateTimeLeft();
  timer = setInterval(calculateTimeLeft, 60000);
});

onUnmounted(() => {
  if (timer) clearInterval(timer);
});
</script>

<template>
  <div class="flex gap-3 pl-3">
    <Avatar :url="pledgeActivity.user?.profile_photo_url || '/images/anonymous_pledger.png'" />
    <div class="pt-1.5">
      <p class="flex gap-1.5 mb-4 text-sm text-tundora dark:text-spun-pearl">
        <span class="font-medium text-mondo dark:text-seashell">{{ pledgeActivity.user?.name || 'Anonymous Pledger'}}</span>
        pledged
        <span class="font-medium text-purple-heart">${{ pledgeActivity.amount }}</span>
      </p>
      <p 
        v-if="pledgeActivity.expire_date" 
        class="text-xs flex items-center gap-2 text-red-500 dark:text-red-400"
      >
        <span 
          class="inline-flex items-center animate-pulse"
        >
          <svg
            class="w-4 h-4 mr-1" 
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
          >
            <path 
              stroke-linecap="round" 
              stroke-linejoin="round" 
              stroke-width="2" 
              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          {{ timeLeft }}
        </span>
      </p>
    </div>
    <span class="pt-1.5 text-xs text-tundora dark:text-spun-pearl ml-auto whitespace-nowrap">{{
      dayjs(pledgeActivity.created_at).fromNow() }}</span>
  </div>
</template>

<style scoped>
@keyframes urgentPulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

.animate-pulse {
  animation: urgentPulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>