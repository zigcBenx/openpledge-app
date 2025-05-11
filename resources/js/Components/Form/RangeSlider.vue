<style src="@vueform/slider/themes/default.css"></style>
<template>
  <div class="flex flex-inline">
      <div class="w-12 text-center -mt-2 dark:text-spun-pearl text-tundora">{{ rangeValues[0] }}€</div>
      <div class="w-9/12 mx-4">
        <Slider
          v-model="rangeValues"
          :max="10000"
          :min="0"
        />
      </div>
      <div class="w-16 text-center -mt-2 dark:text-spun-pearl text-tundora">{{ rangeValues[1] }}€</div>
  </div>
</template>
<script setup>
  import Slider from '@vueform/slider'
  import { ref, watch } from 'vue';
  const emit = defineEmits(['input'])
  const props = defineProps({
    max: {
      type: Number,
      default: 10000
    },
    min: {
      type: Number,
      default: 1
    },
    value: {
      type: Array,
      default: [0,10000]
    }
  });
  const rangeValues = ref(props.value);

  watch(rangeValues, (data) => {
    emit('input', { start: data[0], end: data[1] });
  });
</script>
<style>
.slider-connect {
  @apply dark:bg-ocean-green bg-ocean-green;
}
.slider-target {
  @apply h-1;
}
.slider-touch-area {
  @apply dark:bg-green rounded-full;
}
.slider-tooltip.slider-tooltip-top {
  @apply hidden;
}
.slider-connects {
  @apply dark:bg-gunmetal;
}
</style>
