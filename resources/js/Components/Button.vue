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
      <div class="flex items-center justify-center">
        <template v-if="loading">
            <svg class="animate-spin" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle opacity="0.2" cx="12" cy="12" r="6" stroke="#FCFCFD" stroke-width="2"/>
              <path d="M18 12C18 8.68629 15.3137 6 12 6" stroke="#FCFCFD" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </template>
        <slot />
      </div>
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
      loading: {
        type: Boolean,
        default: false,
      },
    },
    plain: {
      type: Boolean,
      default: false,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
<<<<<<< HEAD
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
=======
  };
  </script>

<style scoped>
    /* Styles for the loader */
    .loader {
      border: 2px solid #f3f3f3;
      border-radius: 50%;
      border-top: 2px solid #3498db;
      width: 16px;
      height: 16px;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
</style>
>>>>>>> origin
