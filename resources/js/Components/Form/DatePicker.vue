<template>
    <div class="relative datepicker" ref="containerRef">
        <Input
            :model-value="formattedDate"
            readonly
            @click="toggleOpen"
            :placeholder="placeholder"
            icon="calendar"
            inputClass="w-full cursor-pointer !bg-transparent"
        />

        <!-- Calendar Dropdown -->
        <div 
            v-show="isOpen" 
            class="absolute mt-1 w-full bg-white dark:bg-charcoal-gray rounded-md shadow-lg p-4 z-50"
            ref="dropdownRef"
            @mousedown.prevent
            @click.stop
        >
            <!-- Month/Year Navigation -->
            <div class="flex justify-between items-center mb-4">
                <button 
                    @click="previousMonth"
                    class="p-1 hover:bg-lavender-mist dark:hover:bg-oil rounded-full transition-colors"
                >
                    <svg class="w-5 h-5 dark:text-spun-pearl text-tundora" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <div class="flex gap-2">
                    <button 
                        @click="view = 'months'"
                        class="dark:text-lavender-mist text-oil hover:text-green dark:hover:text-green transition-colors"
                    >
                        {{ currentMonthName }}
                    </button>
                    <button 
                        @click="view = 'years'"
                        class="dark:text-lavender-mist text-oil hover:text-green dark:hover:text-green transition-colors"
                    >
                        {{ currentYear }}
                    </button>
                </div>
                <button 
                    @click="nextMonth"
                    class="p-1 hover:bg-lavender-mist dark:hover:bg-oil rounded-full transition-colors"
                >
                    <svg class="w-5 h-5 dark:text-spun-pearl text-tundora" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <!-- Days View -->
            <div v-if="view === 'days'">
                <!-- Weekday Headers -->
                <div class="grid grid-cols-7 mb-2">
                    <span v-for="day in weekDays" :key="day"
                        class="text-center text-xs dark:text-spun-pearl text-tundora font-medium">
                        {{ day }}
                    </span>
                </div>

                <!-- Calendar Days -->
                <div class="grid grid-cols-7 gap-1">
                    <button v-for="{ date, isCurrentMonth, isDisabled } in calendarDays" :key="date.valueOf()"
                        @click="selectDate(date)" :disabled="isDisabled" :class="[
                            'p-2 text-sm rounded-md transition-colors',
                            isCurrentMonth ? 'dark:text-lavender-mist text-oil' : 'text-spun-pearl',
                            isDisabled ? 'opacity-50 cursor-not-allowed' : 'hover:bg-green hover:text-white cursor-pointer',
                            isSelected(date) ? 'bg-green text-white' : '',
                            date.isSame(dayjs(), 'day') && !isSelected(date) ? 'border border-green' : ''
                        ]">
                        {{ date.date() }}
                    </button>
                </div>
            </div>

            <!-- Months View -->
            <div 
                v-else-if="view === 'months'" 
                class="grid grid-cols-3 gap-2"
            >
                <button
                    v-for="(month, index) in months"
                    :key="month"
                    @mousedown.prevent.stop
                    @click.prevent.stop="selectMonth(index)"
                    :class="[
                        'p-2 text-sm rounded-md transition-colors',
                        'hover:bg-green hover:text-white cursor-pointer',
                        currentMonth.month() === index ? 'bg-green text-white' : 'dark:text-lavender-mist text-oil'
                    ]"
                >
                    {{ month }}
                </button>
            </div>

            <!-- Years View -->
            <div 
                v-else-if="view === 'years'" 
                class="grid grid-cols-3 gap-2"
            >
                <button
                    v-for="year in yearRange"
                    :key="year"
                    @mousedown.prevent.stop
                    @click.prevent.stop="selectYear(year)"
                    type="button"
                    :class="[
                        'p-2 text-sm rounded-md transition-colors',
                        'hover:bg-green hover:text-white cursor-pointer',
                        currentMonth.year() === year ? 'bg-green text-white' : 'dark:text-lavender-mist text-oil'
                    ]"
                >
                    {{ year }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import dayjs from 'dayjs';
import Input from '@/Components/Input.vue';

const props = defineProps({
    modelValue: {
        type: [Date, String],
        default: null
    },
    minDate: {
        type: [Date, String],
        default: () => dayjs().add(3, 'weeks').toDate()
    },
    placeholder: {
        type: String,
        default: 'Select date'
    },
    isOpen: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:modelValue', 'update:is-open']);

const containerRef = ref(null);
const inputRef = ref(null);
const dropdownRef = ref(null);

const toggleOpen = () => {
    if (!props.isOpen || view.value === 'days') {
        if (!props.isOpen) {
            view.value = 'days';
        }
        emit('update:is-open', !props.isOpen);
    }
};

const handleClickOutside = (event) => {
    if (containerRef.value && !containerRef.value.contains(event.target)) {
        emit('update:is-open', false);
        view.value = 'days';
    }
};

const handleEscape = (e) => {
    if (e.key === 'Escape' && props.isOpen) {
        emit('update:is-open', false);
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    document.addEventListener('keydown', handleEscape);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    document.removeEventListener('keydown', handleEscape);
});

const currentMonth = ref(dayjs().startOf('month'));
const selectedDate = ref(props.modelValue ? dayjs(props.modelValue) : null);

const weekDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

const formattedDate = computed(() => {
    return selectedDate.value ? selectedDate.value.format('MMMM D, YYYY') : '';
});

const calendarDays = computed(() => {
    const start = currentMonth.value.clone().startOf('month').startOf('week');
    const end = currentMonth.value.clone().endOf('month').endOf('week');
    const days = [];
    const minDate = dayjs(props.minDate).startOf('day');

    let current = start.clone();
    while (current.isBefore(end)) {
        const clonedDate = current.clone();
        days.push({
            date: clonedDate,
            isCurrentMonth: clonedDate.month() === currentMonth.value.month(),
            isDisabled: clonedDate.isBefore(minDate)
        });
        current = current.add(1, 'day');
    }
    return days;
});

const isSelected = (date) => {
    return selectedDate.value && date.isSame(selectedDate.value, 'day');
};

const selectDate = (date) => {
    const dateToSelect = date.clone();
    const minDate = dayjs(props.minDate).startOf('day');

    if (dateToSelect.isBefore(minDate)) {
        return;
    }

    selectedDate.value = dateToSelect;
    emit('update:modelValue', dateToSelect.toDate());
    emit('update:is-open', false);
};

const previousMonth = () => {
    if (view.value === 'days') {
        currentMonth.value = currentMonth.value.subtract(1, 'month');
    } else if (view.value === 'months') {
        currentMonth.value = currentMonth.value.subtract(1, 'year');
    } else if (view.value === 'years') {
        currentMonth.value = currentMonth.value.subtract(10, 'year');
    }
};

const nextMonth = () => {
    if (view.value === 'days') {
        currentMonth.value = currentMonth.value.add(1, 'month');
    } else if (view.value === 'months') {
        currentMonth.value = currentMonth.value.add(1, 'year');
    } else if (view.value === 'years') {
        currentMonth.value = currentMonth.value.add(10, 'year');
    }
};

const view = ref('days');
const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

const currentMonthName = computed(() => currentMonth.value.format('MMMM'));
const currentYear = computed(() => currentMonth.value.year());

const yearRange = computed(() => {
    const currentYear = currentMonth.value.year();
    const years = [];
    for (let i = currentYear - 5; i <= currentYear + 5; i++) {
        years.push(i);
    }
    return years;
});

const selectMonth = (monthIndex) => {
    currentMonth.value = currentMonth.value.month(monthIndex);
    view.value = 'days';
};

const selectYear = (year) => {
    currentMonth.value = currentMonth.value.year(year);
    view.value = 'days';
};

watch(() => props.modelValue, (newValue) => {
    if (newValue) {
        const date = dayjs(newValue);
        selectedDate.value = date;
        currentMonth.value = date.startOf('month');
    } else {
        selectedDate.value = null;
        currentMonth.value = props.minDate ? 
            dayjs(props.minDate).startOf('month') : 
            dayjs().startOf('month');
    }
}, { immediate: true });

onMounted(() => {
    if (props.minDate) {
        currentMonth.value = dayjs(props.minDate).startOf('month');
    }
});
</script>

<style scoped>
button {
    transition: all 0.2s ease-in-out;
}
</style>