<script setup>
  import { Link } from '@inertiajs/vue3';
  import Avatar from '@/Components/Avatar.vue';
  import Pill from '@/Components/Form/Pill.vue';
</script>

<template>
  <div class="flex gap-3 pl-3 items-center">
      <Avatar url="/images/avatar.png" />
      <p class="flex gap-1.5 text-sm text-tundora dark:text-spun-pearl whitespace-nowrap">
        <span class="font-medium text-mondo dark:text-seashell">username</span>
        <span class="underline dark:text-green text-dark-green">force-pushed</span>
        the
        <Pill 
          color="secondary"
          size='sm'
        >
            str-head-tail
        </Pill>
        branch from
        <Link href="/" class="underline dark:text-green text-dark-green">0e6703d</Link>
        branch from
        <Link href="/" class="underline dark:text-green text-dark-green">496f4de</Link>
      </p>
      <span class="text-xs text-tundora dark:text-spun-pearl ml-auto whitespace-nowrap">8 Days ago</span>
  </div>
</template>