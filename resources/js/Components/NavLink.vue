<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    active: Boolean,
});

const classes = computed(() => {
    return props.active
        ? 'inline-flex uppercase items-center px-1 pb-1 pt-1 border-b-2 border-dark-green bg:border-green text-sm leading-5 text-gray-900 dark:text-gray-100'
        : 'inline-flex uppercase items-center px-1 pb-2 pt-1 border-b-2 border-transparent text-sm leading-5 text-rich-black dark:text-platinum hover:text-green dark:hover:text-green';
});
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
