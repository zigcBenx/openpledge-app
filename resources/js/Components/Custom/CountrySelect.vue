<template>
    <div class="custom-select" :tabindex="0" ref="selectRef">
        <div class="selected" :class="{ open: open, 'dark': isDark }" @click="handleOpen">
            <span v-if="modelValue" class="flag-option">
                <span :class="`fi fi-${modelValue.code.toLowerCase()}`"></span>
                {{ modelValue.label }}
            </span>
            <span v-else>Select a country</span>
        </div>
        <div class="items" :class="{ selectHide: !open, 'dark': isDark }">
            <div class="sticky-search">
                <input 
                    ref="searchInput"
                    type="text" 
                    v-model="search" 
                    class="search-input"
                    :class="{ 'dark': isDark }"
                    @click.stop
                    placeholder="Search countries..."
                >
            </div>
            <div v-if="filteredCountries.length === 0" class="no-results">
                No results found
            </div>
            <div
                v-else
                v-for="country in filteredCountries"
                :key="country.code"
                @click="
                    updateValue(country);
                    open = false;
                "
                class="flag-option"
                :class="{ 'selected-item': country === modelValue }"
            >
                <span :class="`fi fi-${country.code.toLowerCase()}`"></span>
                {{ country.label }}
            </div>
        </div>
    </div>
</template>

<script setup>
import 'flag-icons/css/flag-icons.min.css'
import { countries } from '@/constants/countries'
import { ref, computed, nextTick, onMounted, onBeforeUnmount } from 'vue';
import { useDark } from '@vueuse/core';

const props = defineProps({
    modelValue: {
        type: Object,
        default: null
    },
});

const emit = defineEmits(['update:modelValue']);
const isDark = useDark()
const open = ref(false);
const search = ref('');
const searchInput = ref(null);
const selectRef = ref(null);

const updateValue = (country) => {
    emit('update:modelValue', country);
};

const filteredCountries = computed(() => {
    if (!search.value) return countries;
    return countries.filter(country => 
        country.label.toLowerCase().includes(search.value.toLowerCase())
    );
});

const handleOpen = () => {
    open.value = !open.value;
    if (open.value) {
        // Use nextTick to ensure the input is in the DOM
        nextTick(() => {
            searchInput.value?.focus();
        });
    }
};

const handleClickOutside = (event) => {
    if (selectRef.value && !selectRef.value.contains(event.target)) {
        open.value = false;
        search.value = '';
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});
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
    @apply dark:text-lavender-mist text-oil p-2 pl-3 cursor-pointer select-none hover:bg-lavender-mist hover:dark:bg-gunmetal;
}

.selected-item {
    @apply bg-lavender-mist dark:bg-gunmetal;
}

.selectHide {
    @apply hidden;
}

.flag-option {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.fi {
    width: 1.5em;
    height: 1em;
}

.sticky-search {
    @apply sticky top-0 z-10;
}

.search-input {
    @apply w-full px-3 py-2 text-sm border-b dark:bg-charcoal-gray bg-seashell dark:text-lavender-mist text-oil border-green focus:outline-none;
    transition: border-color 0.2s ease-in-out;
}

.search-input:focus {
    outline: 2px solid transparent;
    outline-offset: 2px;
    --tw-ring-inset: var(--tw-empty,/*!*/ /*!*/);
    --tw-ring-offset-width: 0px;
    --tw-ring-offset-color: #fff;
    --tw-ring-color: rgb(63 228 189 / var(--tw-border-opacity));
    --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
    --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color);
    box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    border-color: rgb(63 228 189 / var(--tw-border-opacity));
    transition: all 0.2s ease-in-out;
}

.no-results {
    @apply p-4 text-center text-sm dark:text-lavender-mist text-oil pointer-events-none;
}
</style>