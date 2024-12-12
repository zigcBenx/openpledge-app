<script setup>
    import { ref, computed } from 'vue';
    import Pill from '@/Components/Form/Pill.vue';
    import dayjs from '../../../libs/dayjs.js';
    import { Link } from '@inertiajs/vue3';
    import { marked } from 'marked';

    const props = defineProps({
        class: {
            type: String
        },
        issue: {
            type: Object
        }
    });

    const isDescriptionFullVisible = ref(false);

    marked.setOptions({
        gfm: true,
        breaks: true,
        headerIds: false,
    });

    const parsedDescription = computed(() => {
        if (!props.issue.description) return null;
        return marked(props.issue.description);
    });
</script>
<template>
  <div :class="class">
      <div class="flex">
          <span class="dark:text-spun-pearl text-tundora text-xs capitalize min-w-[10.75rem]">DESCRIPTION</span> 
          <div v-if="parsedDescription">
              <div :class="['markdown-body dark:text-lavender-mist text-oil text-sm overflow-hidden', {
                  'max-h-24': !isDescriptionFullVisible
              }]">
                  <div v-html="parsedDescription"></div>
              </div>
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
          <span class='dark:text-lavender-mist text-oil text-sm flex gap-1'>{{ dayjs(issue.changed_at).format('MMMM DD YYYY') }}</span>
      </div>
      <div class="flex mt-8">
          <span class="dark:text-spun-pearl text-tundora text-xs capitalize min-w-[10.75rem]">REPOSITORY</span> 
          <span class='dark:text-seashell text-mondo text-sm font-medium'>
            <Link :href="route('repositories.show',{ githubUser: issue.repository.title.split('/')[0], repository: issue.repository.title.split('/')[1] })">
                {{ issue.repository.title }}
            </Link>
        </span>
      </div>
      <div class="flex mt-8">
          <span class="dark:text-spun-pearl text-tundora text-xs capitalize min-w-[10.75rem]">LANGUAGES</span> 
          <div class='flex gap-1' title="Beta alert: Not working yet ;)" v-if="issue.repository?.programming_languages?.length > 0">
              <Pill 
                  v-for="language in issue.repository.programming_languages"
                  :key="language.id"
                  color="secondary"
                  size='sm'
              >
                  {{ language.name }}
              </Pill>
          </div>
      </div>
  </div>
</template>