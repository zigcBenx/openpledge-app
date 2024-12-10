<script setup>
  import Button from '@/Components/Button.vue';
  import Icon from '@/Components/Icon.vue';
  import { useToast } from "vue-toastification";
  import { router } from '@inertiajs/vue3';

  const props = defineProps({
    issue: Object,
    isAuthenticated: Boolean
  });

  const solveIssue = () => {
    const toast = useToast()
    axios.post(route('issues.solve'), {
      issue_id: props.issue.id
    })
      .then(response => {
        const toastOptions = response.data.message.includes('added')
            ? {
                onClick: () => router.visit(route('profile.actives-show')),
                toastClassName: 'cursor-pointer hover:opacity-90'
            } 
            : {};
        toast.success(response.data.message, toastOptions);
        props.issue.isAuthUsersActiveIssue = !props.issue.isAuthUsersActiveIssue;
      })
      .catch(error => {
        toast.error('Something went wrong!')
        console.error(error);
      });
  }

</script>
<template>
    <div class="p-6 border-b dark:border-oil border-lavender-mist flex flex-col dark:border-oil border-b">
      <p class="dark:text-lavender-mist text-oil text-lg font-medium pb-3">How this works</p>
      <div class="flex flex-col gap-2 p-4">
        <p class="dark:text-tundora text-spun-pearl">1</p>
        <div>
          <p class="dark:text-lavender-mist text-oil">Fork</p>
          <p class="dark:text-spun-pearl text-tundora text-xs">Fork the repository to your own GitHub account.</p>
        </div>
      </div>
      <div class="flex flex-col gap-2 p-4">
        <p class="dark:text-tundora text-spun-pearl">2</p>
        <div>
          <p class="dark:text-lavender-mist text-oil">Branch</p>
          <p class="dark:text-spun-pearl text-tundora text-xs">Create a new branch in your forked repository and check it out locally.</p>
        </div>
      </div>
      <div class="flex flex-col gap-2 p-4">
        <p class="dark:text-tundora text-spun-pearl">3</p>
        <div>
          <p class="dark:text-lavender-mist text-oil">Code & push</p>
          <p class="dark:text-spun-pearl text-tundora text-xs">Make your changes or additions to the code, then submit a pull request to the original repository.</p>
        </div>
      </div>
    </div>
    <div class="p-6 flex flex-col gap-6 pt-8">
      <div class="flex flex-inline">
        <div class="pl-4">
          <Icon name="info" class="dark:fill-spun-pearl fill-tundora"/>
        </div>
        <p class="dark:text-spun-pearl text-tundora text-xs float-left font-medium leading-5 ml-2">By selecting 'Solve', this issue will be added to your active issues. You will receive notifications regarding activity happening to this issue.</p>
      </div>
      <Button v-if="!issue.isAuthUsersActiveIssue" size="lg" color="primary" class="dark:text-oil" @click="solveIssue" :disabled="!isAuthenticated">Solve This Issue</Button>
      <Button v-else size="lg" color="outline" class="dark:text-platinum" @click="solveIssue">Remove Issue</Button>
    </div>
</template>