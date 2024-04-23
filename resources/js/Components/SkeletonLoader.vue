<template>
  <div :class="[bgClass, loaderClass, 'relative overflow-hidden']">
    <div class="shimmer absolute top-0 right-0 bottom-0 left-0" :style="shimmerStyle"></div>
    <slot />
  </div>
</template>

<script>
  import { ref, computed, defineProps, toRefs } from 'vue';
  import resolveConfig from 'tailwindcss/resolveConfig';
  import tailwindConfig from '/tailwind.config.js';
  import { useDark } from '@vueuse/core';

  const isDark = ref(false);
  useDark({
    onChanged(dark) {
      isDark.value = dark;
    },
  });
  const { theme } = resolveConfig(tailwindConfig);
  const LOADER_TYPES = { rectangle: 'rectangle', circle: 'circle' };

  const LOADER_CSS_CLASSES = {
    [LOADER_TYPES.rectangle]: 'rounded-sm',
    [LOADER_TYPES.circle]: 'rounded-full',
  };

  const isHexColor = (hexColor) => {
    const hex = hexColor.replace('#', '');
    return typeof hexColor === 'string' && hexColor.startsWith('#') && hex.length === 6 && !isNaN(Number('0x' + hex));
  };

  const hexToRgb = (hex) => {
    const rgb = hex.match(/\w\w/g).map((x) => parseInt(x, 16));
    return rgb.join(', ');
  };

  const SHIMMER_COLOR = computed(() => isDark.value ? theme.colors.gunmetal : '#ffffff');

</script>

<script setup>
  const props = defineProps({
    type: {
      type: String,
      default: LOADER_TYPES.rectangle,
      validator(value) {
        return Object.values(LOADER_TYPES).includes(value);
      },
    },
    bgClass: {
      type: String,
      default: 'bg-platinum dark:bg-charcoal-gray',
    },
    cssClass: {
      type: String,
      default: '',
    },
    shimmerColor: {
      type: String
    },
  });

  const { type, bgClass, cssClass, shimmerColor } = toRefs(props);

  const shimmerStyle = computed(() => {
    const rgb = shimmerColor.value && isHexColor(shimmerColor.value) ? hexToRgb(shimmerColor.value) : hexToRgb(SHIMMER_COLOR.value);

    return {
      backgroundImage: `linear-gradient(90deg, rgba(${rgb}, 0) 0%, rgba(${rgb}, 0.2) 20%, rgba(${rgb}, 0.5) 60%, rgba(${rgb}, 0))`,
    };
  });

  const loaderClass = computed(() => (cssClass.value ? cssClass.value : LOADER_CSS_CLASSES[type.value]));
</script>

<style lang="css" scoped>
  .shimmer {
    transform: translateX(-100%);
    animation: shimmer 1.4s infinite;
  }

  @keyframes shimmer {
    100% {
      transform: translateX(100%);
    }
  }
</style>