<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Icon from '@/Components/Icon.vue';
import Button from '@/Components/Button.vue';

</script>

<template>
    <AppLayout title="Error">
        <div class="flex flex-col w-full justify-center items-center gap-1 mt-20">
            <div><Icon name="error" class="fill-tundora dark:fill-spun-pearl"></Icon></div>
            <div class="text-[1.56rem] text-oil dark:text-lavender-mist mt-7">Oops! An error has occured.</div>
            <div class="dark:text-spun-pearl text-tundora text-xs">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
            <a href="/issues" class="w-[12rem]"><Button color="link" class="h-[2.625rem] text-seashell dark:text-oil font-sm mt-14">Back to Discover</Button></a>
        </div>
    </AppLayout>
</template>
