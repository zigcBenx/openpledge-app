<template>
  <table class="w-full border-separate border-spacing-x-0 border-spacing-y-4">
      <thead>
          <tr class="text-tundora dark:text-spun-pearl uppercase text-xs text-left">
              <th class="pb-5 min-w-[5rem] font-normal">State</th>
              <th class="pb-5 font-normal">Name</th>
              <th class="pb-5 font-normal">Labels</th>
              <th class="pb-5 font-normal">Repository</th>
              <th class="pb-5 font-normal">Languages</th>
              <th class="pb-5 font-normal">Donations</th>
              <th></th>
          </tr>
      </thead>
      <tbody>
        <TableRowSkeleton v-if="!issues.length" v-for="index in 10" :key="index" />
        <tr 
            v-for="issue in issues"
            :key="issue.id"
            :class="['text-sm bg-white dark:bg-charcoal-gray border-separate', {
              'dark:bg-rich-black bg-ghost-white': issue.state === 'closed'
            }]"
          >
              <td :class="['rounded-bl-md font-medium overflow-hidden border-l-[6px] border-ocean-green dark:border-green pl-3.5 py-6 rounded-tl-md', {
                '!border-tundora': issue.state === 'closed'
              }]">
                <div 
                  :class="['text-ocean-green dark:text-green', {
                    '!text-spun-pearl': issue.state === 'closed'
                  }]"
                >
                  {{ issue.state }}
                </div>
              </td>
              <td class="py-6">
                <Link 
                  :class="['dark:text-white dark:hover:text-green hover:text-green text-base pr-4', {
                    '!text-spun-pearl': issue.state === 'closed'
                  }]"
                  href="/issues/1"
                  >
                  {{ issue.title }}
                </Link>
                <div class="flex gap-1 mt-3">
                  <Avatar :url="issue.user_avatar" size="sm" />
                  <span class="dark:text-spun-pearl text-tundora text-xs font-medium">{{ issue.github_username }}</span>
                  <span class="dark:text-spun-pearl text-tundora text-xs font-light">{{ dayjs(issue.created_at).fromNow() }}</span>
                </div>
              </td>
              <td class="py-6 pr-4 align-middle">
                <div class="flex flex-wrap gap-1">
                  <Pill
                    v-if="issue.labels && issue.labels.length > 0"
                    v-for="label in issue.labels.split(',')"
                    :key="label"
                    color="present"
                    size="sm"
                    :disabled="issue.state === 'closed'"
                  >
                    {{ label }}
                  </Pill>
                </div>
              </td>
              <td class="py-6"><span class="dark:text-white font-medium text-xs pr-4">strapi/strapi</span></td>
              <td class="py-6 pr-4 align-middle">
                  <div class="flex flex-wrap gap-1">
                    <Pill 
                      v-if="issue.repository"
                      v-for="lang in issue.repository.programming_languages" 
                      :key="lang"
                      color="present" 
                      size="sm" 
                      :disabled="issue.state === 'closed'"
                    >
                      {{ lang.name }}
                    </Pill>
                  </div>
              </td>
              <td class="py-6">
                <span 
                  :class="['text-purple-heart font-medium', {
                    '!dark:text-spun-pearl text-tundora': issue.state === 'closed'
                  }]"
                >
                  {{ issue.donations_sum_amount ?? 0 }} €
                </span>
              </td>
              <td class="rounded-br-md rounded-tr-md pr-6">
                  <Icon 
                    name="star"
                    width="1.375rem"
                    :class="getIconStrokeColor(issue.favorite, issue.state === 'closed')"
                    :disabled="issue.state === 'closed'"
                    @click="addFavorites(issue)"
                  />
              </td>
          </tr>
          <tr v-intersection-observer="onIntersectionObserver"></tr>
      </tbody>
  </table>
</template>

<script setup>
    import TableRowSkeleton from '@/Components/Custom/TableRowSkeleton.vue'
    import { Link } from '@inertiajs/vue3';
    import Pill from '@/Components/Form/Pill.vue';
    import Icon from '@/Components/Icon.vue';
    import Avatar from '@/Components/Avatar.vue';
    import { useDark } from '@vueuse/core';
    import { vIntersectionObserver } from '@vueuse/components'
    import dayjs from '@/libs/dayjs';

    defineProps({
        issues: {
            type: Array,
            required: true,
            default: [],
            validator: function (value) {
                return value.every(item => {
                  return typeof item === 'object' && item.hasOwnProperty('id');
                });
            }
        },
    });

    const isDark = useDark();
    const emit = defineEmits(['onLazyLoading']);
 
    const getIconStrokeColor = (isFavorite, isDisabled) => {
      if (isDark.value) {
        if (isDisabled) return 'dark:stroke-mondo dark:fill-mondo';
        return isFavorite ? 'dark:stroke-green dark:fill-green' : 'dark:stroke-spun-pearl dark:hover:stroke-green';
      }
      if (isDisabled) {
        return 'stroke-spun-pearl dark:fill-spun-pearl';
      }
      return isFavorite ? 'stroke-ocean-green fill-ocean-green' : 'stroke-tundora hover:stroke-ocean-green';
    }

    const onIntersectionObserver = ([{ isIntersecting }]) => {
      if (isIntersecting) {
        emit('onLazyLoading');
      }
    }

    const addFavorites = (issue) => {
      issue.favorite = !issue.favorite;
    }
</script>