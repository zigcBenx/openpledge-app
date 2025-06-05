<template>
  <div class="bg-periwinkle dark:bg-valhalla p-6 rounded-md">
    <div class="flex gap-2 mb-4">
      <h2 class="text-gunmetal dark:text-lavender-mist text-xl"><i class="fa-brands fa-github dark:text-white"/> Connect your GitHub repository to OpenPledge</h2>
    </div>
    <p class="text-tundora dark:text-spun-pearl text-xs mb-6">Add a repository from GitHub by pasting the link to that repository.</p>
    <div v-if="!isRepositoryInputVisible" class="flex justify-center">
      <Button 
        @click="isRepositoryInputVisible = true"
        color="primary"
      >
        Connect your repository
      </Button>
    </div>
    <div v-else class="flex gap-2 items-center">
      <Input 
        v-model:input="repositoryLink"
        inputClass="!w-full !bg-transparent border-spun-pearl placeholder-tundora focus:border-tundora dark:border-mondo dark:placeholder-spun-pearl" 
        placeholder="https://github.com/laravel/laravel" 
        @keydown.enter="openRepository"
        class="flex-1"
      />
      <Button 
        @click="openRepository"
        color="primary"
        class="!w-20 !h-12 !rounded-md flex-shrink-0"
        :disabled="!isRepositoryLinkValid"
      >
        Connect
      </Button>
    </div>
  </div>
</template>
<script setup>
  import Input from '@/Components/Input.vue'
  import Button from '@/Components/Button.vue'
  import { ref, computed } from 'vue'
  import { router } from '@inertiajs/vue3'

  const repositoryLink = ref()
  const isRepositoryInputVisible = ref(false)

  const repositoryLinkRegex = /^(?:[\w-]+\/[\w.-]+|(?:https?:\/\/)?(?:www\.)?github\.com\/[\w-]+\/[\w.-]+\/?)$/i;
  const isRepositoryLinkValid = computed(() => repositoryLinkRegex.test((repositoryLink.value || '').trim()))

  function openRepository() {
    if (repositoryLink.value && isRepositoryLinkValid.value) {
      let link = repositoryLink.value.trim().replace(/\/$/, '').split('/')
      const user = link[link.length - 2]
      const repo = link[link.length - 1]
      router.visit(route('repositories.show',{ githubUser: user, repository: repo }))
    }
  }
</script>