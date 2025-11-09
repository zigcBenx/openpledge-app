<template>
    <div class="w-full">
      <div class="flex flex-col mb-3">
        <header
          class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 pb-3 md:pb-4"
        >
          <div class="flex-1 min-w-0">
            <h1 class="dark:text-lavender-mist text-[2.5rem]">
              {{ title }}
            </h1>
            <p v-if="description" class="ext-tundora dark:text-spun-pearl text-sm">
              {{ description }}
            </p>
          </div>

          <div class="flex items-center gap-4 justify-start shrink-0 w-full md:w-auto">
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
  