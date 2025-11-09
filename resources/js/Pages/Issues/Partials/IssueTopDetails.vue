<template>
  <div
    :class="['flex gap-3 md:gap-6 p-3 md:p-6 md:pr-10 border-l-4 md:border-l-[6px] border-ocean-green dark:border-green bg-seashell dark:bg-charcoal-gray rounded-md text-oil bg:text-lavender-mist', {
        '!border-tundora': issue && issue.state === 'closed'
      }]"
    >
      <a :href="issue.github_url" target="_blank" class="flex-shrink-0 flex items-center justify-center">
        <i class="fa-brands fa-github text-3xl md:text-5xl dark:text-white hover:text-green dark:hover:text-green transition-colors"></i>
      </a>
      <div class="flex-1 min-w-0">
          <h1 class='dark:text-lavender-mist text-lg md:text-[1.75rem] font-semibold line-clamp-2 md:line-clamp-none'>{{ issue.title }}</h1>
          <div class='flex flex-wrap items-center gap-2 md:gap-3 mt-2'>
              <span class='text-xs md:text-sm font-medium bg:text-green text-ocean-green uppercase'>{{issue.state}}</span>
              <div class='hidden md:flex gap-1.5 items-center'>
                  <Avatar :url='issue.user_avatar' size='sm' />
                  <span class='dark:text-spun-pearl font-medium text-tundora text-sm'>{{issue.github_username}}</span>
                  <span class='dark:text-spun-pearl text-tundora whitespace-nowrap font-light text-sm'>opened {{ dayjs(issue.github_created_at).fromNow() }}</span>
              </div>
              <div class='hidden md:flex gap-1'>
                  <Pill
                      color="secondary"
                      size='sm'
                  >
                      Bug
                  </Pill>
                  <Pill
                      color="secondary"
                      size='sm'
                  >
                      Feature
                  </Pill>
              </div>
          </div>
      </div>
      <button
        class='flex-shrink-0'
        @click="emit('onFavoriteClick')"
      >
          <Icon
            name="star"
            :class="['stroke-tundora dark:hover:stroke-green w-5 h-5 md:w-6 md:h-6', {
              'dark:fill-green dark:stroke-green fill-tundora': issue.favorite
            }]"
          />
      </button>
  </div>
</template>
<script setup>
  import Icon from '@/Components/Icon.vue';
  import Pill from '@/Components/Form/Pill.vue';
  import Avatar from '@/Components/Avatar.vue';
  import dayjs from '../../../libs/dayjs.js';

  const props = defineProps({
    issue: {
      type: Object,
      required: true
    }
  });

  const emit = defineEmits(['onFavoriteClick']);
</script>