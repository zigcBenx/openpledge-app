<template>
  <button
      class="inline-block w-full h-9 rounded-full font-medium text-sm leading-tight focus:outline-none focus:ring-0 transition duration-150 ease-in-out"
      :class="{
        'dark:bg-turquoise bg-dark-green text-white dark:text-dark-black dark:hover:ring-green hover:ring-green hover:ring-2': color === 'primary' && !plain,
        'dark:hover:bg-green dark:bg-shade-green bg-mint-green text-turquoise dark:text-dark-green dark:hover:ring-green hover:ring-green hover:ring-2': color === 'primary' && plain,
        'dark:bg-shade-green bg-mint-green text-turquoise dark:text-dark-green dark:hover:ring-green hover:ring-green hover:ring-2': color === 'secondary' && !plain,
        'bg-transparent ring-mondo text-rich-black ring-[1px] dark:text-grayish dark:ring-grayish dark:hover:ring-green hover:ring-green hover:ring-2': color === 'outline' && !plain,
        'dark:bg-green bg-dark-green dark:hover:ring-green hover:ring-green hover:ring-2': color === 'link' && !plain,
        'pointer-events-none opacity-60': disabled,
        '!h-12': size === 'lg'
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
          ["primary", "secondary", "link", "outline"].includes(value),
    },
    plain: {
      type: Boolean,
      default: false,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    size: {
      type: String,
      validator: (value) =>
          ["md", "lg"].includes(value),
    }
  },
  setup(props, {emit}) {
    const clickHandler = (event) => {
      emit("click", event);
    };

    return {
      clickHandler,
    };
  },
};
</script>