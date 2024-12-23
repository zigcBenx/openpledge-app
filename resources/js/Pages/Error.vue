<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Icon from '@/Components/Icon.vue';
import Button from '@/Components/Button.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    message: {
        type: String,
        default: 'Oops! An error has occurred.'
    },
    subMessage: {
        type: String,
        default: 'Please try again later or contact support if the issue persists.'
    },
    redirectUrl: {
        type: String,
        default: null
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

const redirectHref = computed(() => {
    return props.redirectUrl || route(props.redirectRoute);
});
</script>

<template>
    <AppLayout title="Error">
        <div class="flex flex-col w-full justify-center items-center gap-1 mt-20 px-96">
            <Icon name="error" class="fill-tundora dark:fill-spun-pearl"></Icon>
            <div class="text-[1.56rem] text-oil dark:text-lavender-mist mt-7 text-center" v-html="props.message"></div>
            <div class="dark:text-spun-pearl text-tundora text-sm mt-4" v-html="props.subMessage"></div>
            <component :is="props.redirectUrl ? 'a' : Link" :href="redirectHref" class="w-[12rem]">
                <Button color="link" class="h-[2.625rem] text-seashell dark:text-oil font-sm mt-4">
                    {{ props.redirectButtonText }}
                </Button>
            </component>
        </div>
    </AppLayout>
</template>
