<template>
    <span
      class="inline-flex cursor-pointer items-center px-4 py-2 rounded text-sm font-medium"
      :class="{
        'bg-gray-tones hover:bg-primary-grayish hover:dark:bg-grayish-blue text-primary-800 dark:bg-charcoal-gray dark:text-lavender-gray': color === 'primary',
        'bg-gray-tones hover:dark:bg-grayish-blue bg-primary-grayish bg-green text-dark-green dark:bg-shade-green dark:text-green': color === 'secondary'
      }"
      @click="selectHandler"
    >
      <slot />
      <button
        v-if="dismissable"
        type="button"
        class="inline-flex items-center ms-2 text-sm bg-transparent rounded-sm"
        :class="{
            'text-primary-400 hover:text-primary-900 dark:hover:text-primary-300': color === 'primary',
            'text-secondary-400 hover:text-secondary-900 dark:hover:text-secondary-300': color === 'secondary',
            'text-green-400 hover:text-green-900 dark:hover:text-green-300': color === 'success',
            'text-red-400 hover:text-red-900 dark:hover:text-red-300': color === 'danger',
            'text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300': color === 'warning',
            'text-blue-400 hover:text-blue-900 dark:hover:text-blue-300': color === 'info',
        }"
        @click="clickHandler"
        aria-label="Remove"
        >
        <i class="fa-solid fa-xmark"></i>
        <span class="sr-only">Remove</span>
        </button>
    </span>
  </template>
  
  <script>
  export default {
    emits: ["dismiss", "select"],
    props: {
      color: {
        type: String,
        default: "primary",
        validator: (value) =>
          ["primary", "secondary", "success", "danger", "warning", "info"].includes(value),
      },
      dismissable: {
        type: Boolean,
        default: false,
      },
    },
    setup(props, { emit }) {
      const clickHandler = (event) => {
        emit("dismiss", event);
      };

      const selectHandler = (event) => {
        emit("select");
      };
  
      return {
        clickHandler,
        selectHandler
      };
    },
  };
  </script>
  