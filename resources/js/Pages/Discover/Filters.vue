<template>
    <DialogModal max-width="xl" :show="displayFilterModal" @close="displayHandler(false)" :closeable="true" overflow-visible>
        <template #title>
            <span>Filters</span>
        </template>
        <template #content>
            <div class="flex flex-col gap-6">
                <div>
                    <div class="mb-2.5">Label</div>
                    <div class="flex flex-wrap gap-2">
                        <div 
                            v-for="selectedValue in labels"
                        >
                            <Pill 
                                :key="selectedValue.value"
                                :color="filters.find(item => item.key === keys.labels && item.value === selectedValue.value) ? 'secondary' : 'primary'"
                                @select="() => handleSelectOption(selectedValue.value, keys.labels)"
                                @dismiss="() => handleRemoveOption(selectedValue.value, keys.labels)"
                            >
                                {{ selectedValue.label }}
                            </Pill>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="mb-2.5">Language</div>
                    <div class="flex flex-wrap gap-2">
                        <div 
                            v-for="selectedValue in languages"
                            class="float-left flex"
                        >
                            <Pill
                                :key="selectedValue.name"
                                :color="filters.find(item => item.key === keys.languages && item.value === selectedValue.name) ? 'secondary' : 'primary'"
                                @select="() => handleSelectOption(selectedValue.name, keys.languages)"
                                @dismiss="() => handleRemoveOption(selectedValue.name, keys.languages)"
                            >
                                {{ selectedValue.name }}
                            </Pill>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="mb-5">Pledge Range</div>
                    <RangeSlider
                        @input="handleRangeChange"
                        :value="filters.find(item => item.key === keys.range)?.value ? Object.entries(filters.find(item => item.key === keys.range).value).map(entry => entry[1]) : [0,10000]"
                    />
                </div>
                <div>
                    <div class="pb-2.5">Time Created</div>
                    <VueDatePicker v-model="date" placeholder="Select time" month-picker :clearable="true"/>
                </div>
                <div class="flex items-center">
                    <Checkbox v-model:checked="showPledgedOnly" id="showPledgedOnly" @update:checked="handleShowPledgedOnlyChange" />
                    <label for="showPledgedOnly" class="ml-2">Show only pledged issues</label>
                </div>
            </div>
        </template>
        <template #footer>
            <Row class="pt-9">
                <Col :span="{sm:6}">
                    <Button @click="clearFilters" color="outline">
                        Clear
                    </Button>
                </Col>
                <Col :span="{sm:6}">
                    <Button color="primary" @click="submit">
                        Apply Filters
                    </Button>
                </Col>
            </Row>
        </template>
    </DialogModal>
</template>
<script setup>
  import Pill from '@/Components/Form/Pill.vue';
  import { ref, onMounted, watch } from 'vue';
  import RangeSlider from '@/Components/Form/RangeSlider.vue';
  import Row from '@/Components/Grid/Row.vue';
  import Col from '@/Components/Grid/Col.vue';
  import DialogModal from '@/Components/DialogModal.vue';
  import Button from '@/Components/Button.vue';
  import VueDatePicker from '@vuepic/vue-datepicker';
  import Checkbox from '@/Components/Checkbox.vue';

  const props = defineProps({
    displayFilterModal: {
      type: Boolean,
      default: false
    },
    labels: {
      type: Object,
      default: () => ({})
    },
    languages: {
      type: Object,
      default: () => ({})
    },
    queryFilters: {
      type: Object,
      default: () => ({})
    },
    keys: {
      type: Object,
      default: () => ({})
    },
    removedFilters: {
      type: Number,
      default: 0
    }
  });
  const emit = defineEmits(['submit', 'display']);

  const filters = ref([]);
  const date = ref();
  const showPledgedOnly = ref();

  const handleSelectOption = (value, key) => {
    const index = filters.value.findIndex(item => item.key === key && item.value === value);
    if (index !== -1) {
      filters.value.splice(index, 1);
    } else {
      filters.value.push({ key, value });
    }
  };

  const handleRemoveOption = (value, key) => {
    if (key === props.keys.labels) {
        const indexToRemove = props.labels.findIndex(labelObj => labelObj.value === value);
        if (indexToRemove !== -1) {
            filters.value.labels.splice(indexToRemove, 1);
        }
    } else if (key === props.keys.languages) {
        const indexLangToRemove = props.languages.findIndex(lang => lang.name === value);
        if (indexLangToRemove !== -1) {
            filters.value.languages.splice(indexLangToRemove, 1);
        }
    }
  };

  const handleRangeChange = (value) => {
    let range = filters.value.find(item => item.key === props.keys.range);
    if (range) {
      range.value = value;
    } else {
      filters.value.push({ key: props.keys.range, value: value });
    }
  };

  const handleShowPledgedOnlyChange = (shouldShowOnlyPledged) => {
    const showOnlyPledgedFilterIndex = filters.value.findIndex(
      (filter) => filter.key === props.keys.showPledgedOnly
    );

    if (shouldShowOnlyPledged === true) {
      if (showOnlyPledgedFilterIndex === -1) {
        filters.value.push({ key: props.keys.showPledgedOnly, value: true });
      }
    } else {
      // we remove the filter from the params if the user unchecks the checkbox
      if (showOnlyPledgedFilterIndex !== -1) {
        filters.value.splice(showOnlyPledgedFilterIndex, 1);
      }
    }
  };

  const displayHandler = (value) => {
    emit("display", value);
  };

  const submit = () => {
    emit("submit", filters.value);
    localStorage.setItem(props.keys.storageDiscoverKey, JSON.stringify(filters.value));
    displayHandler(false);
  };

  const clearFilters = () => {
    filters.value = [];
    emit("submit", filters.value);
    emit("display", false);
    localStorage.setItem(props.keys.storageDiscoverKey, JSON.stringify(filters.value));
  };

  const updateFiltersFromQuery = () => {
    filters.value = [...props.queryFilters];
    localStorage.setItem(props.keys.storageDiscoverKey, JSON.stringify(filters.value));
    date.value = null;
  };

  onMounted(() => {
    if (localStorage.getItem(props.keys.storageDiscoverKey)) {
      filters.value = JSON.parse(localStorage.getItem(props.keys.storageDiscoverKey));
    } else {
      if (Object.keys(props.queryFilters).length) {
        filters.value = props.queryFilters;
      }
    }
    date.value = filters.value.find(item => item.key === props.keys.date)?.value;
    showPledgedOnly.value = filters.value.find(item => item.key === props.keys.showPledgedOnly)?.value;
  });

  watch(() => date, () => {
    if (date.value) {
      let filterDate = filters.value.find(item => item.key === props.keys.date);
      if(filterDate) {
        filterDate.value = date.value;
      } else {
        filters.value.push({ key: props.keys.date, value: { month: date.value.month, year: date.value.year } });
      }
    }
  }, { deep: true });

  watch(() => props.removedFilters, updateFiltersFromQuery, { deep: true });
</script>

<style>
.dp__overlay.dp--overlay-relative, .dp__menu.dp__menu_index.dp__theme_light, .dp__overlay.dp--overlay-absolute {
    @apply dark:!bg-charcoal-gray bg-ghost-white;
}
.dp__overlay_cell_active.dp__overlay_cell_pad {
    @apply dark:!text-seashell text-dark-green;
}
.dp__overlay_cell.dp__overlay_cell_pad, .dp__btn.dp--year-select {
    @apply dark:!text-spun-pearl !text-mondo;
}
.dp__btn.dp--year-select, .dp__overlay_cell.dp__overlay_cell_pad, .dp__inner_nav {
    @apply dark:hover:bg-shade-green;
}
.dp__overlay_cell_active.dp__overlay_cell_pad, .dp__action_select {
    @apply dark:!bg-shade-green !bg-pale-aqua;
}
.dp__selection_preview, .dp__action_button {
    @apply dark:text-green text-dark-green;
}
.dp__action_button, .dp__menu.dp__menu_index.dp__theme_light {
    @apply border-none;
}
.dp__action_button.dp__action_select, .dp__btn.dp__button.dp__overlay_action {
    @apply dark:hover:bg-shade-green;
}
.dp__outer_menu_wrap.dp--menu-wrapper {
    @apply dark:bg-charcoal-gray bg-ghost-white px-3 py-2 -mt-2;
}
.dp__arrow_bottom {
    @apply hidden;
}
.dp__pointer.dp__input_readonly.dp__input.dp__input_icon_pad,
.dp__pointer.dp__input_readonly.dp__input.dp__input_icon_pad.dp__input_focus.dp__input_reg {
    @apply dark:bg-transparent bg-transparent dark:text-spun-pearl text-dark-green dark:border-mondo focus:outline-none border-spun-pearl border-[1px] h-12;
    @apply placeholder-tundora dark:placeholder-spun-pearl;
}
.dp__action_button.dp__action_select{
    @apply text-dark-green;
}
</style>