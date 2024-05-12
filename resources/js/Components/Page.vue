<template>
    <div class="space-y-6 w-full">
      <div class="flex flex-col">
        <header
          class="space-y-2 items-center justify-between flex md:space-y-0 md:space-x-4 pb-8 md:py-6"
        >
          <div>
            <h1 class="dark:text-lavender-mist text-[2.5rem]">
              {{ title }}
            </h1>
            <p v-if="description" class="ext-tundora dark:text-spun-pearl text-sm">
              {{ description }}
            </p>
          </div>
  
          <div class="flex items-center gap-4 justify-start shrink-0">
            <slot name="actions" />
          </div>
        </header>
        <slot name="filters" />
      </div>
      <div class="flex flex-col">
        <slot />
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      title: {
        type: String,
        required: false,
      },
      description: {
        type: String,
        required: false,
      },
    },
  };
  </script>
  