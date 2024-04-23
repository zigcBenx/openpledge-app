<template>
  <span
    class="inline-flex cursor-pointer items-center rounded text-sm font-medium"
    :class="[{
      'bg-platinum hover:bg-grayish hover:dark:bg-gunmetal dark:bg-charcoal-gray dark:text-spun-pearl': color === 'primary',
      'bg-platinum hover:dark:bg-gunmetal bg-grayish bg-green text-dark-green dark:bg-shade-green dark:text-green': color === 'secondary',
      'dark:bg-gunmetal dark:text-spun-pearl !cursor-default': color === 'present',
      '!px-1.5 !py-0.5 text-xs': size === 'sm',
      '!pointer-events-all !cursor-default': disabled
    }, ...contentClasses]"
    @click="selectHandler"
  >
    <slot />
    <Icon
      class="ml-2 fill-green dark:fill-green"
      v-if="dismissable"
      name="close"
      @click="clickHandler"
      >
      </Icon>
  </span>
</template>

<script>
import Icon from '@/Components/Icon.vue';

export default {
  components: {
    Icon
  },
  emits: ["dismiss", "select"],
  props: {
    color: {
      type: String,
      default: "primary",
      validator: (value) =>
        ["primary", "secondary", "success", "danger", "warning", "info", "present"].includes(value),
    },
    dismissable: {
      type: Boolean,
      default: false,
    },
    contentClasses: {
      type: Array,
      default: ['px-4 py-2']
    },
    disabled: {
      type: Boolean
    },
    size: {
      type: String,
      default: 'md',
      validator: (value) =>
        ["md", "sm"].includes(value),
    }
  },
  setup(props, { emit }) {
    const clickHandler = () => {
      emit("dismiss");
    };

    const selectHandler = () => {
      emit("select");
    };

    return {
      clickHandler,
      selectHandler
    };
  },
};
</script>
