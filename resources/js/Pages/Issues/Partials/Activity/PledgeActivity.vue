<script setup>
  import Avatar from '@/Components/Avatar.vue';
</script>

<template>
  <div class="flex gap-3 pl-3">
      <Avatar url="/images/avatar.png" />
      <div class="pt-1.5">
        <p class="flex gap-1.5 mb-4 text-sm text-tundora dark:text-spun-pearl">
          <span class="font-medium text-mondo dark:text-seashell">username</span>
          pledged
          <span class="font-medium text-purple-heart">$24</span>
        </p>
        <p class="text-xs text-tundora dark:text-spun-pearl">
          Valid until Dec 22 2024
        </p>
      </div>
      <span class="pt-1.5 text-xs text-tundora dark:text-spun-pearl ml-auto whitespace-nowrap">8 Days ago</span>
  </div>
</template>