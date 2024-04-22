<template>
    <div
      class="grid grid-cols-12"
      :class="{
        'gap-1': gapValue.sm === 1,
        'gap-2': gapValue.sm === 2,
        'gap-3': gapValue.sm === 3,
        'gap-4': gapValue.sm === 4,
        'gap-5': gapValue.sm === 5,
        'gap-6': gapValue.sm === 6,
        'md:gap-1': gapValue.md === 1,
        'md:gap-2': gapValue.md === 2,
        'md:gap-3': gapValue.md === 3,
        'md:gap-4': gapValue.md === 4,
        'md:gap-5': gapValue.md === 5,
        'md:gap-6': gapValue.md === 6,
        'lg:gap-1': gapValue.lg === 1,
        'lg:gap-2': gapValue.lg === 2,
        'lg:gap-3': gapValue.lg === 3,
        'lg:gap-4': gapValue.lg === 4,
        'lg:gap-5': gapValue.lg === 5,
        'lg:gap-6': gapValue.lg === 6,
        'xl:gap-1': gapValue.xl === 1,
        'xl:gap-2': gapValue.xl === 2,
        'xl:gap-3': gapValue.xl === 3,
        'xl:gap-4': gapValue.xl === 4,
        'xl:gap-5': gapValue.xl === 5,
        'xl:gap-6': gapValue.xl === 6,
      }"
    >
      <slot />
    </div>
  </template>
  
  <script>
  import { computed } from "vue";
  
  export default {
    props: {
      gap: {
        type: Object,
        default: {
          sm: 1,
          md: 2,
          lg: 4,
          xl: 4,
        },
      },
    },
    setup(props) {
      const gapValue = computed(() => {
        const gap = {
          sm: 1,
          md: 2,
          lg: 4,
          xl: 4,
        };
  
        if (typeof props.gap === 'number') {
          return {
            ...gap,
            lg: props.gap,
          };
        }
        return {
          ...gap,
          ...props.gap,
        };
      });
  
      return {
        gapValue,
      };
    },
  };
  </script>
  