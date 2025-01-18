<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        default: '',
    },
    as: String,
    contentClasses: {
        type: Array,
        default: () => ['py-4'],
    }
});
</script>

<template>
    <div class="hover:ring-2 hover:ring-green rounded-md">
        <button v-if="as == 'button'" type="submit" class="h-12 block w-full px-3 py-2 transition duration-150 ease-in-out">
            <slot />
        </button>

        <a v-else-if="as == 'a'" :href="href" class="flex px-5 transition duration-150 ease-in-out dark:focus:bg-green" :class="contentClasses">
            <slot />
        </a>

        <Link v-else :href="href" class="flex px-5 transition duration-150 ease-in-out dark:focus:bg-green" :class="contentClasses">
            <slot />
        </Link>
    </div>
</template>
