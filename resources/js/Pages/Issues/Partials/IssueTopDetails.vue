<script setup>
  import { watch } from 'vue';
  import Icon from '@/Components/Icon.vue';
  import Pill from '@/Components/Form/Pill.vue';
  import Avatar from '@/Components/Avatar.vue';
  import dayjs from '../../../libs/dayjs.js';

  const props = defineProps({
    issue: {
      type: Object,
      validator: (value) =>
          value.id && value.title && value.user && value.hasOwnProperty('state')
    }
  });

  const emit = defineEmits(['onFavoriteClick']);
</script>
<template>
  <div 
    :class="['flex gap-6 p-6 pr-10 border-l-[6px] border-ocean-green dark:border-green bg-seashell dark:bg-charcoal-gray rounded-md text-oil bg:text-lavender-mist', {
        '!border-tundora': issue && issue.state === 'closed'
      }]"
    >
      <Icon name="github" class='rounded-md' />
      <div>
          <h1 class='dark:text-lavender-mist text-[1.75rem]'>{{ issue.title }}</h1>
          <div class='flex items-center gap-3'>
              <span class='font-medium bg:text-green text-ocean-green'>Open</span>
              <div class='flex gap-1.5 items-center'>
                  <Avatar url='/images/avatar.png' size='sm' />
                  <span class='dark:text-spun-pearl font-medium text-tundora'>Username</span>
                  <span class='dark:text-spun-pearl text-tundora'>opened this issue {{ dayjs(issue.created_at).fromNow() }}</span>
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
            :class="['dark:stroke-spun-pearl stroke-tundora', {
              'dark:fill-spun-pearl fill-tundora': issue.favorite
            }]" 
            size="lg"
          />
      </button>
  </div>
</template>