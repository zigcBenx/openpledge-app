<template>
  <ul :class="['flex gap-5 items-center', wrapperClass]">
    <li v-for="(link, index) in links" class="flex gap-5 items-center">
      <Link 
        :href="link.url" 
        :class="['text-sm font-medium', {
          'text-mondo dark:text-seashell': index === links.length - 1,
          'text-tundora dark:text-spun-pearl': index !== links.length - 1
        }]"
      >
        {{ link.title }}
      </Link>
      <Icon v-if="index !== links.length - 1" class="dark:text-mondo text-spun-pearl" name="chevron-right" />
    </li>
  </ul>
</template>

<script setup>
  import { Link } from '@inertiajs/vue3';
  import Icon from '@/Components/Icon.vue';

  defineProps({
    wrapperClass: {
      type: String
    },
    links: {
      type: Array,
      validator: (value) =>
          value[0].title && value[0].url
    }
  });
</script>