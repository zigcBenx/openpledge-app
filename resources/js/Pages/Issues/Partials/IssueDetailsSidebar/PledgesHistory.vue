<script setup>
  import Icon from '@/Components/Icon.vue';
</script>
<template>
  <div class="min-w-[31.9rem]">
    <div class="bg-periwinkle dark:bg-valhalla p-6 rounded-md flex flex-col gap-6">
      <h2 class="flex dark:text-platinum text-gunmetal text-xl">
        Pledges
        <span class="font-medium ml-auto text-purple-heart">$205</span>
      </h2>
      <div class="py-[1.87rem] border-y dark:border-stylish-red border-thistle">
          <p class="dark:text-spun-pearl text-tundora flex text-xs mb-5">VALID UNTIL <span class="ml-auto text-purple-heart">January 7 2025</span></p>
          <p class="dark:text-spun-pearl text-tundora flex text-xs">AFTER THAT <span class="ml-auto text-purple-heart">$195</span></p>
      </div>
      <button class="text-mondo dark:text-seashell text-sm font-medium flex gap-3 items-center">
        Pledge history <Icon name="chevron-down" class="text-mondo dark:text-seashell" />
      </button>
    </div>
  </div>
</template>