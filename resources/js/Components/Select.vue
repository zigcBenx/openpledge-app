<template>
  <div class="custom-select" :tabindex="tabindex" @blur="open = false">
    <div class="selected" :class="{ open: open }" @click="open = !open">
      {{ selected }}
    </div>
    <div class="items" :class="{ selectHide: !open }">
      <div
        v-for="option of data"
        :key="option"
        @click="
          selected = option;
          open = false;
          $emit('input', option);
        "
      >
        {{ option }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    data: {
      type: Array,
      required: true
    },
    default: {
      type: String,
      required: false,
      default: null,
    },
    tabindex: {
      type: Number,
      required: false,
      default: 0,
    },
  },
  data() {
    return {
      selected: this.default
        ? this.default
        : this.data.length > 0
        ? this.data[0]
        : null,
      open: false
    };
  },
  mounted() {
    this.$emit("input", this.selected);
  },
};
</script>

<style scoped>
  .custom-select {
    @apply relative w-full h-[48px] ring-0 text-left leading-[2.93rem];
  }

  .custom-select .selected {
    @apply !bg-transparent rounded-md pl-3.5 select-none placeholder-spun-pearl dark:text-lavender-mist text-oil focus:ring-0 border border-slate-gray focus:border-green rounded-md cursor-pointer;
  }

  .custom-select .selected.open {
    @apply border-green;
  }

  .custom-select .selected:after {
    @apply absolute top-[22px] right-[16px] w-0 h-0 dark:border-r-transparent dark:border-b-transparent dark:border-l-transparent dark:!border-t-lavender-mist !border-t-oil border-r-transparent border-b-transparent border-l-transparent;
    content: "";
    border: 5px solid transparent;
  }

  .custom-select .items {
    @apply absolute text-xs dark:bg-charcoal-gray bg-seashell rounded-md border border-green bg-seashell overflow-hidden left-0 right-0 max-h-[14rem] overflow-scroll z-10 top-[53px];
  }

  .custom-select .items div {
    @apply dark:text-lavender-mist text-oil p-2 pl-3 cursor-pointer select-none;
  }

  .custom-select .items div {
    @apply hover:bg-lavender-mist hover:dark:bg-gunmetal;
  }

  .selectHide {
    @apply hidden;
  }
</style>
