<script setup>
import CommentActivity from './CommentActivity.vue';
import PullRequestActivity from './PullRequestActivity.vue';
import IssueActivity from './IssueActivity.vue';
import { computed } from 'vue';
import PledgeActivity from './PledgeActivity.vue';

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
  if (activity.event === 'reopened' || activity.event === 'closed' || activity.event === 'resolved') {
    return { component: IssueActivity, prop: 'issueActivity' };
  } else if (activity.event === 'connected' || activity.event === 'disconnected') {
    return { component: PullRequestActivity, prop: 'pullRequestActivity' };
  } else if (activity.body && typeof activity.body === 'string' && activity.user.type !== 'Bot') {
    return { component: CommentActivity, prop: 'commentActivity' };
  } else if (activity.net_amount) {
    return { component: PledgeActivity, prop: 'pledgeActivity' };
  }
  return null;
};

const activityCount = computed(() => {
  if (!props.issue.issueActivity) {
    return 0;
  }
  return props.issue.issueActivity.reduce((count, activity) => {
    return count + (getActivityComponent(activity) !== null ? 1 : 0);
  }, 0);
});

</script>
<template>
  <div :class="class">
    <p class="mb-10 text-oil dark:text-lavender-mist">Activity ({{ activityCount }})</p>
    <div class="flex flex-col gap-9">
      <template v-for="activity in issue.issueActivity" :key="activity.id">
        <div v-if="getActivityComponent(activity)">
          <component :is="getActivityComponent(activity).component"
            v-bind:[getActivityComponent(activity).prop]="activity" />
        </div>
      </template>
    </div>
  </div>
</template>
