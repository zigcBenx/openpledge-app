<template>
    <div class="custom-select" :tabindex="0" ref="dropdownContainer">
        <div class="selected" :class="{ open: dropdownOpen }" @click="toggleDropdown">
            <template v-if="selectedOptions.length === 0">
                Select at least one
            </template>
            <template v-else>
                {{ selectedOptions.map(option => option.name).join(', ') }}
            </template>
        </div>

        <div class="items" :class="{ selectHide: !dropdownOpen }">
            <div v-for="option in options" :key="option.id"
                class="flex items-center p-2 pl-3 cursor-pointer select-none" @click="toggleOption(option)">
                <Checkbox :checked="isSelected(option.id)" :value="String(option.id)" />
                <span class="ml-2">
                    {{ option.name }}
                </span>
            </div>
        </div>
    </div>
</template>

<script>
import Checkbox from '@/Components/Checkbox.vue';

export default {
    components: {
        Checkbox
    },
    props: {
        options: {
            type: Array,
            required: true,
            default: () => [],
        },
        value: {
            type: Array,
            required: false,
            default: () => [],
        },
    },
    data() {
        return {
            dropdownOpen: false,
            selectedValues: this.value,
        };
    },
    computed: {
        selectedOptions() {
            return this.options.filter(option => this.selectedValues.includes(option.id));
        },
    },
    methods: {
        toggleDropdown() {
            this.dropdownOpen = !this.dropdownOpen;
        },
        toggleOption(option) {
            const index = this.selectedValues.indexOf(option.id);
            if (index > -1) {
                this.selectedValues.splice(index, 1);
            } else {
                this.selectedValues.push(option.id);
            }
            this.$emit('input', [...this.selectedValues]);
        },
        isSelected(optionId) {
            return this.selectedValues.includes(optionId);
        },
        handleClickOutside(event) {
            if (this.$refs.dropdownContainer && !this.$refs.dropdownContainer.contains(event.target)) {
                this.dropdownOpen = false;
            }
        },
    },
    watch: {
        value(newValue) {
            this.selectedValues = newValue;
        },
    },
    mounted() {
        document.addEventListener('click', this.handleClickOutside);
    },
    beforeDestroy() {
        document.removeEventListener('click', this.handleClickOutside);
    },
};
</script>

<style scoped>
.custom-select {
    @apply relative w-full h-[48px] ring-0 text-left leading-[2.93rem];
}

.custom-select .selected {
    @apply !bg-transparent rounded-md pl-3.5 select-none placeholder-spun-pearl text-oil dark:text-lavender-mist focus:ring-0 border border-slate-gray focus:border-green cursor-pointer;
}

.custom-select .selected.open {
    @apply border-green;
}

.custom-select .selected:after {
    @apply absolute top-[22px] right-[16px] w-0 h-0 border-transparent;
    content: "";
}

.custom-select .items {
    @apply absolute text-xs bg-seashell dark:bg-charcoal-gray rounded-md border border-green overflow-hidden left-0 right-0 max-h-[14rem] overflow-scroll z-10 top-[53px];
}

.custom-select .items div {
    @apply text-oil dark:text-lavender-mist p-2 pl-3 cursor-pointer select-none;
}

.custom-select .items div:hover {
    @apply bg-lavender-mist dark:bg-gunmetal;
}

.selectHide {
    @apply hidden;
}
</style>