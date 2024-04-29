<script setup>
  import Avatar from '@/Components/Avatar.vue';
</script>

<template>
  <div class="flex gap-3 pl-3">
      <Avatar url="/images/avatar.png" />
      <div class="pt-1.5">
        <p class="flex gap-1.5 mb-4 text-sm text-tundora dark:text-spun-pearl">
          <span class="font-medium text-mondo dark:text-seashell">username</span>
          commented
        </p>
        <p class="text-sm text-oil dark:text-lavender-mist">
          These could simply be syntactic sugar for .str.slice(0, n) and .str.slice(-n), but they are pretty common string operations and make the code a bit more obvious when used.
        </p>
      </div>
      <span class="pt-1.5 text-xs text-tundora dark:text-spun-pearl ml-auto whitespace-nowrap">8 Days ago</span>
  </div>
</template>