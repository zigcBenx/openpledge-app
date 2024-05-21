<script setup>
  import Icon from '@/Components/Icon.vue';
  import dayjs from '@/libs/dayjs.js';
  import { ref } from 'vue';

  defineProps({
    data: {
      type: Object,
      required: true
    }
  });

  const showHistory = ref(false);

  const DATE_FORMAT = 'MMMM D YYYY';

</script>
<template>
  <div class="min-w-[31.9rem]">
    <div class="bg-periwinkle dark:bg-valhalla p-6 rounded-md flex flex-col gap-6">
      <h2 class="flex dark:text-platinum text-gunmetal text-xl">
        Pledges
        <span class="font-medium ml-auto text-purple-heart">{{ data.amount }}</span>
      </h2>
      <div v-if="data.validUntil || data.amountAfter" class="py-[1.87rem] border-y dark:border-stylish-red border-thistle flex flex-col gap-5">
          <p v-if="data.validUntil" class="dark:text-spun-pearl text-tundora flex text-xs">VALID UNTIL <span class="ml-auto text-purple-heart">{{ dayjs(data.validUntil).format(DATE_FORMAT) }}</span></p>
          <p v-if="data.amountAfter" class="dark:text-spun-pearl text-tundora flex text-xs">AFTER THAT <span class="ml-auto text-purple-heart">{{ data.amountAfter }}</span></p>
      </div>
      <div v-if="data.history?.length">
        <button 
          class="text-mondo dark:text-seashell text-sm font-medium flex gap-3 items-center"
          @click="showHistory = !showHistory"
        >
          Pledge history 
          <Icon 
            name="chevron-down" 
            :class="['text-mondo dark:text-seashell transition-all', {
              'rotate-180': showHistory
            }]"
          />
        </button>
        <ul v-if="showHistory" class="flex flex-col gap-3 mt-6">
          <li v-for="item in data.history" :key="item.id" class="rounded-md border dark:border-stylish-red border-thistle p-4">
            <p class="font-medium dark:text-platinum mb-2">{{ item.amount }}</p>
            <p class="flex text-sm dark:text-lavender-mist mb-1.5">Donated by <span class="font-medium ml-1">{{ item.donatedBy }}</span> <span class="ml-auto">on {{ dayjs(item.donatedAt).format(DATE_FORMAT) }}</span></p>
            <p class="flex text-xs font-light dark:text-spun-pearl">Valid until <span class="ml-auto">{{ dayjs(item.validUntil).format(DATE_FORMAT) }}</span></p>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>