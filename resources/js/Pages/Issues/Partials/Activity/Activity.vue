<script setup>
  import { Link } from '@inertiajs/vue3';
  import Avatar from '@/Components/Avatar.vue';
  import Pill from '@/Components/Form/Pill.vue';
  import BranchUpdateActivity from './BranchUpdateActivity.vue';
  import CommentActivity from './CommentActivity.vue';
  import IssueUpdateActivity from './IssueUpdateActivity.vue';
  import PledgeActivity from './PledgeActivity.vue';

  defineProps({
    class: {
      type: String
    }
  });

</script>
<template>
  <div :class="class">
    <p class="mb-10 text-oil dark:text-lavender-mist">Activity (4)</p>
    <div class="flex flex-col gap-9">
      <BranchUpdateActivity />
      <CommentActivity />
      <IssueUpdateActivity />
      <PledgeActivity />
    </div>
  </div>
</template>