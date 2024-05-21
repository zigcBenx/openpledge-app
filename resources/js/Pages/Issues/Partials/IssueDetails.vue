<script setup>
    import { ref } from 'vue';
    import Pill from '@/Components/Form/Pill.vue';
    import dayjs from '../../../libs/dayjs.js';

    defineProps({
        class: {
            type: String
        },
        issue: {
            type: Object,
            // validator: (value) =>
            //     value.id && value.description && value.user && value.changed_at && value.hasOwnProperty('state')
        }
    });

    const isDescriptionFullVisible = ref(false);
</script>
<template>
  <div :class="class">
      <div class="flex">
          <span class="dark:text-spun-pearl text-tundora text-xs capitalize min-w-[10.75rem]">DESCRIPTION</span> 
          <div>
              <p :class="['dark:text-lavender-mist text-oil text-sm', {
                'line-clamp-2': !isDescriptionFullVisible
              }]">
                  {{ issue.description }}
              </p>
              <button 
                class='font-medium text-mondo dark:text-seashell'
                @click="isDescriptionFullVisible=!isDescriptionFullVisible"
              >
                {{ isDescriptionFullVisible ? 'Read less' : 'Read more' }}
            </button>
          </div>
      </div>
      <div class="flex mt-8">
          <span class="dark:text-spun-pearl text-tundora text-xs capitalize min-w-[10.75rem]">ACTIVE USERS</span> 
          <span class='dark:text-lavender-mist text-oil text-sm'>5</span>
      </div>
      <div class="flex mt-8">
          <span class="dark:text-spun-pearl text-tundora text-xs capitalize min-w-[10.75rem]">LAST CHANGES</span> 
          <span class='dark:text-lavender-mist text-oil text-sm flex gap-1'>{{ dayjs(issue.changed_at).format('MMMM DD YYYY') }}<strong class="font-medium text-mondo dark:text-seashell">userx</strong></span>
      </div>
      <div class="flex mt-8">
          <span class="dark:text-spun-pearl text-tundora text-xs capitalize min-w-[10.75rem]">REPOSITORY</span> 
          <span class='dark:text-seashell text-mondo text-sm font-medium'>strapi/strapi</span>
      </div>
      <div class="flex mt-8">
          <span class="dark:text-spun-pearl text-tundora text-xs capitalize min-w-[10.75rem]">LANGUAGES</span> 
          <div class='flex gap-1'>
              <Pill 
                color="secondary"
                size='sm'
              >
                  Javascript
              </Pill>
              <Pill 
                  color="secondary"
                  size='sm'
              >
                  Ruby
              </Pill>
              <Pill 
                  color="secondary"
                  size='sm'
              >
                  Java
              </Pill>
          </div>
      </div>
  </div>
</template>