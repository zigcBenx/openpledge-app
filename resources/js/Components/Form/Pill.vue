<template>
  <span
    class="inline-flex cursor-pointer items-center rounded text-sm font-medium"
    :class="[{
      'bg-platinum hover:bg-grayish hover:dark:bg-gunmetal text-primary-800 dark:bg-charcoal-gray dark:text-spun-pearl': color === 'primary',
      'bg-platinum hover:dark:bg-gunmetal bg-grayish bg-green text-dark-green dark:bg-shade-green dark:text-green': color === 'secondary',
      '!px-1.5 !py-1 text-xs': size === 'sm',
      '!pointer-events-all !cursor-default': disabled
    }, ...contentClasses]"
    @click="selectHandler"
  >
    <slot />
    <Icon
      class="pl-2"
      v-if="dismissable"
      :fill="colors.green"
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
        ["primary", "secondary", "success", "danger", "warning", "info"].includes(value),
    },
    dismissable: {
      type: Boolean,
      default: false,
    },
    contentClasses: {
      type: Array,
      default: ['px-4 py-2']
    },
    colors: {
      type: Object,
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
      disabled: props.disabled,
      clickHandler,
      selectHandler
    };
  },
};
</script>
