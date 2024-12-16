<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Icon from '@/Components/Icon.vue';
import Button from '@/Components/Button.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    message: {
        type: String,
        default: 'Oops! An error has occured.'
    },
    redirectRoute: {
        type: String,
        default: 'discover.issues'
    },
    redirectButtonText: {
        type: String,
        default: 'Back to Discover'
    }
});
</script>

<template>
    <AppLayout title="Error">
        <div class="flex flex-col w-full justify-center items-center gap-1 mt-20">
            <div><Icon name="error" class="fill-tundora dark:fill-spun-pearl"></Icon></div>
            <div class="text-[1.56rem] text-oil dark:text-lavender-mist mt-7">{{ props.message }}</div>
            <div class="dark:text-spun-pearl text-tundora text-xs">Please try again later or contact support if the issue persists.</div>
            <Link :href="route(props.redirectRoute)" class="w-[12rem]"><Button color="link" class="h-[2.625rem] text-seashell dark:text-oil font-sm mt-14">{{ props.redirectButtonText }}</Button></Link>
        </div>
    </AppLayout>
</template>
