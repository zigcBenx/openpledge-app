<template>
  <div class="bg-periwinkle dark:bg-valhalla p-6 rounded-md">
    <h2 class="text-gunmetal dark:text-lavender-mist text-xl mb-4">Connect a GitHub issue to OpenPledge</h2>
    <p class="text-tundora dark:text-spun-pearl text-xs mb-6">Add an issue from GitHub by pasting the link to that issue.</p>
    <Input 
      inputClass="!w-full !bg-transparent border-spun-pearl placeholder-tundora focus:border-tundora dark:border-mondo dark:placeholder-spun-pearl" 
      placeholder="Paste URL" 
      icon="open_in_new" 
      iconPosition="right"
    />
  </div>
</template>
<script setup>
  import Input from '@/Components/Input.vue';
</script>