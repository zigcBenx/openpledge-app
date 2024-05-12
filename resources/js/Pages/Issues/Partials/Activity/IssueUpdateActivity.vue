<script setup>
  import Avatar from '@/Components/Avatar.vue';
  import Pill from '@/Components/Form/Pill.vue';
</script>

<template>
  <div class="flex gap-3 pl-3 items-center">
      <Avatar url="/images/avatar.png" />
      <p class="flex gap-1.5 text-sm text-tundora dark:text-spun-pearl">
        <span class="font-medium text-mondo dark:text-seashell">username</span>
        added
        <Pill 
          color="secondary"
          size='sm'
        >
            feature
        </Pill>
        label
      </p>
      <span class="text-xs text-tundora dark:text-spun-pearl ml-auto whitespace-nowrap">8 Days ago</span>
  </div>
</template>