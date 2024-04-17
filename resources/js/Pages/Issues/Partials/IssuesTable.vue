<script setup>
    import { Link } from '@inertiajs/vue3';
    import Pill from '@/Components/Form/Pill.vue';
    import Icon from '@/Components/Icon.vue';
    import Avatar from '@/Components/Avatar.vue';
    import resolveConfig from 'tailwindcss/resolveConfig';
    import tailwindConfig from '/tailwind.config.js';
    import { useDark } from '@vueuse/core';
    import dayjs from '../../../libs/dayjs.js';

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

    const { theme } = resolveConfig(tailwindConfig);
    const isDark = useDark();

    const getIconStrokeColor = (isFavorite, isDisabled) => {
      if (isDark.value) {
        if (isDisabled) return theme.colors.mondo;
        return isFavorite ? theme.colors.green : theme.colors['spun-pearl'];
      }
      if (isDisabled) {
        return theme.colors['spun-pearl'];
      }
      return isFavorite ? theme.colors['ocean-green'] : theme.colors.tundora;
    }

    const getIconFillColor = (isFavorite, isDisabled) => {
      if (isDark.value) {
        if (isDisabled) return theme.colors.mondo;
        return isFavorite ? theme.colors.green : 'none';
      }
      if (isDisabled) {
        return theme.colors['spun-pearl'];
      }
      return isFavorite ? theme.colors['ocean-green'] : 'none';
    }
</script>

<template>
  <table class="border-separate border-spacing-x-0 border-spacing-y-4">
      <thead>
          <tr class="text-tundora dark:text-spun-pearl uppercase text-xs text-left">
              <th class="pb-5 min-w-[80px] font-normal">State</th>
              <th class="pb-5 font-normal">Name</th>
              <th class="pb-5 font-normal">Labels</th>
              <th class="pb-5 font-normal">Repository</th>
              <th class="pb-5 font-normal">Languages</th>
              <th class="pb-5 font-normal">Donations</th>
              <th></th>
          </tr>
      </thead>

      <tbody>
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
                  <Link 
                  :class="['text-ocean-green dark:text-green', {
                    '!text-spun-pearl': issue.state === 'closed'
                  }]"
                  href="/issue/123" 
                  target="_blank"
                >
                {{ issue.state }}
              </Link>
              </td>
              <td class="py-6">
                <div 
                  :class="['dark:text-white text-base pr-4', {
                    '!text-spun-pearl': issue.state === 'closed'
                  }]"
                  >
                  {{ issue.title }}
                </div>
                <div class="flex gap-1 mt-3">
                  <Avatar :url="issue.user.user_avatar" size="sm" />
                  <span class="dark:text-spun-pearl text-tundora text-xs font-medium">{{ issue.user.username }}</span>
                  <span class="dark:text-spun-pearl text-tundora text-xs font-light">{{ dayjs(issue.created_at).fromNow() }}</span>
                </div>
            </td>
              <td class="py-6 pr-4 align-middle">
                <div class="flex flex-wrap gap-1">
                  <Pill 
                    v-for="label in issue.labels"
                    :key="label"
                    color="secondary"
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
                      v-for="lang in issue.languages" 
                      :key="lang"
                      color="secondary" 
                      size="sm" 
                      :disabled="issue.state === 'closed'"
                    >
                      {{ lang }}
                    </Pill>
                  </div>
              </td>
              <td class="py-6">
                <span 
                  :class="['text-purple-heart font-medium', {
                    '!dark:text-spun-pearl text-tundora': issue.state === 'closed'
                  }]"
                >
                  {{ issue.donations }}
                </span>
              </td>
              <td class="rounded-br-md rounded-tr-md pr-6">
                  <Icon 
                    name="star" 
                    :stroke="getIconStrokeColor(issue.favorite, issue.state === 'closed')"
                    :fill="getIconFillColor(issue.favorite, issue.state === 'closed')"
                    :disabled="issue.state === 'closed'" 
                  />
              </td>
          </tr>
      </tbody>
  </table>
</template>