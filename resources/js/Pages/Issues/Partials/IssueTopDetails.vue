<template>
  <div 
    :class="['flex gap-6 p-6 pr-10 border-l-[6px] border-ocean-green dark:border-green bg-seashell dark:bg-charcoal-gray rounded-md text-oil bg:text-lavender-mist', {
        '!border-tundora': issue && issue.state === 'closed'
      }]"
    >
      <a :href="issue.github_url" target="_blank"><Icon name="github" class='rounded-md' /></a>
      <div>
          <h1 class='dark:text-lavender-mist text-[1.75rem]'>{{ issue.title }}</h1>
          <div class='flex items-center gap-3'>
              <span class='font-medium bg:text-green text-ocean-green'>{{issue.state}}</span>
              <div class='flex gap-1.5 items-center'>
                  <Avatar :url='issue.user_avatar' size='sm' />
                  <span class='dark:text-spun-pearl font-medium text-tundora'>{{issue.github_username}}</span>
                  <span class='dark:text-spun-pearl text-tundora whitespace-nowrap font-light'>opened this issue {{ dayjs(issue.github_created_at).fromNow() }}</span>
              </div>
              <div class='flex gap-1'>
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
        class='ml-auto' 
        @click="emit('onFavoriteClick')"
      >
          <Icon 
            name="star" 
            :class="['stroke-tundora dark:hover:stroke-green', {
              'dark:fill-green dark:stroke-green fill-tundora': issue.favorite
            }]" 
            size="lg"
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