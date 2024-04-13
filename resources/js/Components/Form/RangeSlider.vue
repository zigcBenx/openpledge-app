<template>

  <div class="flex gap-2">
    <div class="text-sm text-secondary dark:text-primary -mt-2">{{'$'+Math.round(startValue) }}</div>
    <div class="w-full relative h-1">
      <div class="w-full overflow-hidden">
        <div
          class="absolute h-full bg-turquoise dark:bg-secondary"
          :style="{ left: handleLeft + '%', width: rangeWidth + '%' }"
        ></div>
        <div
          class="absolute h-full bg-grayish dark:bg-grayish-blue"
          :style="{ width: handleLeft + '%' }"
        ></div>
        <div
          class="absolute h-full bg-grayish dark:bg-grayish-blue"
          :style="{ left: handleRight + '%', width: (100 - handleRight) + '%' }"
        ></div>
        <div
          class="absolute h-4 w-4 bg-white border-2 border-gray-400 rounded-full cursor-pointer -mt-[5px]"
          :style="{ left: handleLeft + '%' }"
          @mousedown="startDrag('start')"
        ></div>
        <div
          class="absolute h-4 w-4 bg-white border-2 border-gray-400 rounded-full cursor-pointer -mt-[5px]"
          :style="{ left: handleRight + '%' }"
          @mousedown="startDrag('end')"
        >
        </div>
      </div>
    </div>
    <div class="text-sm text-secondary dark:text-primary -mt-2">{{'$'+Math.round(endValue) }}</div>

  </div>
</template>

<script>
export default {
  name: 'RangeSlider',
  props: {
    min: {
      type: Number,
      default: 0
    },
    max: {
      type: Number,
      default: 100
    },
    start: {
      type: Number,
      default: 20
    },
    end: {
      type: Number,
      default: 35
    }
  },
  data() {
    return {
      startValue: Math.round(this.start),
      endValue: Math.round(this.end),
      isDragging: false,
      activeHandle: ''
    };
  },
  computed: {
    handleLeft() {
      return ((this.startValue - this.min) / (this.max - this.min)) * 100;
    },
    handleRight() {
      return ((this.endValue - this.min) / (this.max - this.min)) * 100;
    },
    rangeWidth() {
      return this.handleRight - this.handleLeft;
    }
  },
  methods: {
    startDrag(handle) {
      this.isDragging = true;
      this.activeHandle = handle;
      document.addEventListener('mousemove', this.handleDrag);
      document.addEventListener('mouseup', this.stopDrag);
    },
    handleDrag(event) {
      if (this.isDragging) {
        const rect = event.target.parentElement.getBoundingClientRect();
        const newPosition = ((event.clientX - rect.left) / rect.width) * (this.max - this.min);
        if (this.activeHandle === 'start') {
          this.startValue = Math.min(Math.max(this.min, Math.round(newPosition)), this.endValue);
        } else if (this.activeHandle === 'end') {
          this.endValue = Math.max(Math.min(this.max, Math.round(newPosition)), this.startValue);
        }
      }
    },
    stopDrag() {
      this.isDragging = false;
      document.removeEventListener('mousemove', this.handleDrag);
      document.removeEventListener('mouseup', this.stopDrag);
      this.$emit('input', { start: this.startValue, end: this.endValue });
    }
  },
  watch: {
    start(newVal) {
      this.startValue = Math.round(newVal);
    },
    end(newVal) {
      this.endValue = Math.round(newVal);
    }
  }
};
</script>