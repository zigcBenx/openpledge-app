<script setup>
import { useDark, useToggle } from '@vueuse/core';
import Icon from '@/Components/Icon.vue';

const isDark = useDark();
const toggleDark = useToggle(isDark);

</script>
<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-oil">
        <Icon name="moon" 
            class="dark:stroke-spun-pearl hover:fill-green stroke-tundora"
            @click="toggleDark()" 
        />
        <div>
            <slot name="logo" />
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-rich-black shadow-md overflow-hidden sm:rounded-lg">
            <slot />
        </div>
    </div>
</template>
