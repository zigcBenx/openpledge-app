<template>
  <div class="bg-periwinkle dark:bg-valhalla p-6 rounded-md">
    <h2 class="text-gunmetal dark:text-lavender-mist text-xl mb-4">Connect your GitHub repository to OpenPledge</h2>
    <p class="text-tundora dark:text-spun-pearl text-xs mb-6">Add a repository from GitHub by pasting the link to that repository.</p>
    <Input 
      v-model:input="repositoryLink"
      inputClass="!w-full !bg-transparent border-spun-pearl placeholder-tundora focus:border-tundora dark:border-mondo dark:placeholder-spun-pearl" 
      placeholder="Paste URL" 
      icon="open-in-new" 
      iconPosition="right"
      @keydown.enter="openRepository"
    />
  </div>
</template>
<script setup>
  import Input from '@/Components/Input.vue'
  import { ref } from 'vue'
  import { router } from '@inertiajs/vue3'

  const repositoryLink = ref()
  function openRepository() {
    if (repositoryLink.value) {
      let link = repositoryLink.value.split('/')
      const user = link[link.length - 2]
      const repo = link[link.length - 1]
      router.visit(route('repositories.show',{ githubUser: user, repository: repo }))
    }
  }
</script>