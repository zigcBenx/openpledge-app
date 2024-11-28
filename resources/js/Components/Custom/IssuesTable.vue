<template>
  <div v-if="!issues.length" class="flex flex-col items-center justify-center w-full h-screen">
    <div class="flex flex-col items-center gap-1 mt-20">
      <Icon name="error" class="fill-tundora dark:fill-spun-pearl"></Icon>
    </div>
    <div class="text-[1.56rem] text-oil dark:text-lavender-mist mt-7 text-center">Oops! No matches found...</div>
    <div class="dark:text-spun-pearl text-tundora text-xs text-center">We couldn’t find any matches for your current filters. Please try changing your search criteria or clear the filters to see more issues.</div>
  </div>
  <table v-else class="w-full border-separate border-spacing-x-0 border-spacing-y-4" id="issues-table">
      <thead>
          <tr class="text-tundora dark:text-spun-pearl uppercase text-xs text-left">
              <th class="pb-5 min-w-[5rem] font-normal">State</th>
              <th class="pb-5 font-normal">Name</th>
              <th class="pb-5 font-normal">Labels</th>
              <th v-if="pledged" class="pb-5 font-normal">Repository</th>
              <th class="pb-5 font-normal">Languages</th>
              <th v-if="pledged" class="pb-5 font-normal">Donations</th>
              <th></th>
          </tr>
      </thead>
      <tbody>
        <tr 
            v-for="issue in issues"
            :key="issue.id"
            :class="['text-sm bg-white dark:bg-charcoal-gray border-separate', {
              'dark:bg-rich-black bg-ghost-white': issue.isExternal
            }]"
          >
              <IssueItemPledged v-if="pledged && !issue.isExternal" :issue="issue" :isAuthenticated="isAuthenticated"/>
              <IssueItemExternal v-else :issue="issue" :repository="repository ?? issue.repository"/>
          </tr>
          <tr v-intersection-observer="onIntersectionObserver"></tr>
      </tbody>
  </table>
</template>

<script setup>
    import Icon from '@/Components/Icon.vue'
    import Avatar from '@/Components/Avatar.vue'
    import { vIntersectionObserver } from '@vueuse/components'
    import IssueItemPledged from '@/Components/Custom/IssueItemPledged.vue'
    import IssueItemExternal from '@/Components/Custom/IssueItemExternal.vue'

    defineProps({
        issues: {
            type: Array,
            required: true,
            default: [],
            validator: function (value) {
                return value.every(item => {
                  return typeof item === 'object' && item.hasOwnProperty('id')
                });
            }
        },
        pledged: {
          type: Boolean,
          required: false,
          default: () => {
            return false
          }
        },
        repository: Object,
        isAuthenticated: {
            type: Boolean,
            default: true,
        },
    })

    const emit = defineEmits(['onLazyLoading'])

    const onIntersectionObserver = ([{ isIntersecting }]) => {
      if (isIntersecting) {
        emit('onLazyLoading');
      }
    }
</script>