<template>
    <div
      :class="{
        'col-span-1': spanValue.sm === 1,
        'col-span-2': spanValue.sm === 2,
        'col-span-3': spanValue.sm === 3,
        'col-span-4': spanValue.sm === 4,
        'col-span-5': spanValue.sm === 5,
        'col-span-6': spanValue.sm === 6,
        'col-span-7': spanValue.sm === 7,
        'col-span-8': spanValue.sm === 8,
        'col-span-9': spanValue.sm === 9,
        'col-span-10': spanValue.sm === 10,
        'col-span-11': spanValue.sm === 11,
        'col-span-12': spanValue.sm === 12,
        'md:col-span-1': spanValue.md === 1,
        'md:col-span-2': spanValue.md === 2,
        'md:col-span-3': spanValue.md === 3,
        'md:col-span-4': spanValue.md === 4,
        'md:col-span-5': spanValue.md === 5,
        'md:col-span-6': spanValue.md === 6,
        'md:col-span-7': spanValue.md === 7,
        'md:col-span-8': spanValue.md === 8,
        'md:col-span-9': spanValue.md === 9,
        'md:col-span-10': spanValue.md === 10,
        'md:col-span-11': spanValue.md === 11,
        'md:col-span-12': spanValue.md === 12,
        'lg:col-span-1': spanValue.lg === 1,
        'lg:col-span-2': spanValue.lg === 2,
        'lg:col-span-3': spanValue.lg === 3,
        'lg:col-span-4': spanValue.lg === 4,
        'lg:col-span-5': spanValue.lg === 5,
        'lg:col-span-6': spanValue.lg === 6,
        'lg:col-span-7': spanValue.lg === 7,
        'lg:col-span-8': spanValue.lg === 8,
        'lg:col-span-9': spanValue.lg === 9,
        'lg:col-span-10': spanValue.lg === 10,
        'lg:col-span-11': spanValue.lg === 11,
        'lg:col-span-12': spanValue.lg === 12,
        'xl:col-span-1': spanValue.xl === 1,
        'xl:col-span-2': spanValue.xl === 2,
        'xl:col-span-3': spanValue.xl === 3,
        'xl:col-span-4': spanValue.xl === 4,
        'xl:col-span-5': spanValue.xl === 5,
        'xl:col-span-6': spanValue.xl === 6,
        'xl:col-span-7': spanValue.xl === 7,
        'xl:col-span-8': spanValue.xl === 8,
        'xl:col-span-9': spanValue.xl === 9,
        'xl:col-span-10': spanValue.xl === 10,
        'xl:col-span-11': spanValue.xl === 11,
        'xl:col-span-12': spanValue.xl === 12,
      }"
    >
      <slot />
    </div>
  </template>
  
  <script>
  import { computed } from "vue";
  
  export default {
    props: {
      span: {
        type: [Object, Number],
        default: {
          sm: 12,
          md: null,
          lg: null,
          xl: null,
        },
      },
    },
    setup(props) {
      const spanValue = computed(() => {
        const span = {
          sm: 12,
          md: null,
          lg: null,
          xl: null,
        };
  
        if (typeof props.span === 'number') {
          return {
            ...span,
            lg: props.span,
          };
        }
        return {
          ...span,
          ...props.span,
        };
      });
  
      return {
        spanValue,
      };
    },
  };
  </script>
  