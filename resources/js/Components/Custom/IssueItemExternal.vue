<template>
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
        <a
          :href="issue.github_url"
          target="_blank"
          :class="['dark:text-white dark:hover:text-green hover:text-green text-base pr-4', {
            '!text-spun-pearl': issue.state === 'closed'
          }]"
          >
            {{ issue.title }}
        </a>
      
      <div class="flex gap-1 mt-3">
        <Avatar :url="issue.user_avatar" size="sm" />
        <span class="dark:text-spun-pearl text-tundora text-xs font-medium">{{ issue.github_username }}</span>
        <span class="dark:text-spun-pearl text-tundora text-xs font-light">{{ dayjs(issue.created_at).fromNow() }}</span>
      </div>
    </td>

    <td class="py-6 pr-4 align-middle">
      <div class="flex flex-wrap gap-1">
        <!-- <Pill
          v-if="issue.labels && issue.labels.length > 0"
          v-for="label in issue.labels.split(',')"
          :key="label"
          color="present"
            v-if="issue.programming_languages"
            v-for="issue_lang in issue.programming_languages" 
            :key="issue_lang"
            color="present" 
            size="sm" 
            :disabled="issue.state === 'closed'"
          >
            {{ issue_lang.name }}
          </Pill>
          <Pill 
            v-else-if="issue.repository"
            v-for="lang in issue.repository.programming_languages" 
            :key="lang"
            color="present" 
            size="sm" 
            :disabled="issue.state === 'closed'"
          >
            {{ lang.name }}
          </Pill>
          size="sm"
          :disabled="issue.state === 'closed'"
        >
          {{ label }}
        </Pill> -->
      </div>
    </td>
              
    <td class="py-6 pr-4 align-middle">
        <div class="flex flex-wrap gap-1">
          <!-- <Pill 
            v-if="issue.programming_languages"
            v-for="issue_lang in issue.programming_languages" 
            :key="issue_lang"
            color="present" 
            size="sm" 
            :disabled="issue.state === 'closed'"
          >
            {{ issue_lang.name }}
          </Pill>
          <Pill 
            v-else-if="issue.repository"
            v-for="lang in issue.repository.programming_languages" 
            :key="lang"
            color="present" 
            size="sm" 
            :disabled="issue.state === 'closed'"
          >
            {{ lang.name }}
          </Pill> -->
        </div>
    </td>
    <td class="rounded-br-md rounded-tr-md pr-6">
      <div class="flex justify-end">
        <Icon 
          name="star"
          width="1.375rem"
          :class="getIconStrokeColor(issue.favorite, issue.state === 'closed')"
          :disabled="issue.state === 'closed'"
          @click="addFavorites(issue)"
        />
        <Pill 
          color="secondary"
          class="ml-4"
          @click="pledgeExternalIssue(issue)"
        >
          Pledge
        </Pill>
      </div>
    </td>
</template>

<script setup>
    import dayjs from '@/libs/dayjs'
    import { useDark } from '@vueuse/core'
    import Icon from '@/Components/Icon.vue'
    import Avatar from '@/Components/Avatar.vue'
    import { Link } from '@inertiajs/vue3'
    import Pill from '@/Components/Form/Pill.vue'
    import { router } from '@inertiajs/vue3'

    const props = defineProps({
        issue: {
            type: Object,
            required: true,
        },
        repository: Object,
    })

    const isDark = useDark()


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

    const addFavorites = (issue) => {
        issue.favorite = !issue.favorite;
    }
    
    const pledgeExternalIssue = (issue) => {
      router.post(route('issues.pledge-external-issue'), {
        title: issue.title,
        github_url: issue.github_url,
        github_id: issue.github_id ?? issue.id,
        repository_id: props.repository.id,
        user_avatar: issue.user_avatar,
        github_username: props.repository.title.split('/')[0]
      })
    }
</script>