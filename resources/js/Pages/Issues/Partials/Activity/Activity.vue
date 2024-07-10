<script setup>
import CommentActivity from './CommentActivity.vue';
import PullRequestActivity from './PullRequestActivity.vue';
import IssueActivity from './IssueActivity.vue';
import { computed } from 'vue';

const props = defineProps({
  class: {
    type: String,
  },
  issue: {
    type: Object,
    required: true,
    default: () => {
      return {};
    },
  },
});

const getActivityComponent = (activity) => {
  if (activity.event === 'reopened' || activity.event === 'closed') {
    return { component: IssueActivity, prop: 'issueActivity' };
  } else if (activity.event === 'connected' || activity.event === 'disconnected') {
    return { component: PullRequestActivity, prop: 'pullRequestActivity' };
  } else if (activity.body && typeof activity.body === 'string') {
    return { component: CommentActivity, prop: 'commentActivity' };
  }

  return null;
};

const activityCount = computed(() => {
  return props.issue.issueResolver ? props.issue.issueActivity.length + 1 : props.issue.issueActivity.length;
});

</script>
<template>
  <div :class="class">
    <p class="mb-10 text-oil dark:text-lavender-mist">Activity ({{ activityCount }})</p>
    <div class="flex flex-col gap-9">
      <IssueActivity v-if="issue.issueResolver" :issue="issue" />
      <div v-for="activity in issue.issueActivity" :key="activity.id">
        <component :is="getActivityComponent(activity)?.component"
          v-bind:[getActivityComponent(activity)?.prop]="activity" v-if="getActivityComponent(activity)" />
      </div>
    </div>
  </div>
</template>