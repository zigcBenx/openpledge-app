<template>
    <button
      class="inline-block w-full h-9 rounded-full font-medium text-sm leading-tight focus:outline-none focus:ring-0 transition duration-150 ease-in-out"
      :class="{
        'dark:bg-shade-green bg-mint-green text-turquoise dark:text-dark-green dark:hover:border-green hover:border-green hover:border-2': color === 'primary' && !plain,
        'bg-transparent border-mondo text-rich-black border-[1px] dark:text-grayish dark:border-grayish dark:hover:border-green hover:border-green hover:border-2': color === 'secondary' && !plain,
        'dark:bg-green bg-dark-green dark:hover:border-green hover:border-green hover:border-2': color === 'link' && !plain,
        'pointer-events-none opacity-60': disabled,
      }"
      :type="type"
      @click="clickHandler"
    >
      <slot />
    </button>
  </template>
  
  <script>
  
  export default {
    emits: ["click"],
    props: {
      type: {
        type: String,
        default: "button",
        validator: (value) => ["button", "submit", "reset"].includes(value),
      },
      color: {
        type: String,
        default: "primary",
        validator: (value) =>
          ["primary", "secondary", "link"].includes(value),
      },
      plain: {
        type: Boolean,
        default: false,
      },
      disabled: {
        type: Boolean,
        default: false,
      },
    },
    setup(props, { emit }) {
      const clickHandler = (event) => {
        emit("click", event);
      };
  
      return {
        clickHandler,
      };
    },
  };
  </script>