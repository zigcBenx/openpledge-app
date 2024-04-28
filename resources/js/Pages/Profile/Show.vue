<script setup>
import Pill from '@/Components/Form/Pill.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({});
</script>

<template>
    <AppLayout title="Profile">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Profile
            </h2>
        </template>

        <div class="flex">
            <div class="flex flex-col items-center dark:bg-charcoal-gray bg-seashell p-4 rounded-md">
                <div class="w-[8.25rem]">
                    <img src="/images/avatar.png" class="rounded-full float-right" />
                </div>
                <div class="flex flex-col items-center mt-3">
                    <div class="dark:text-lavender-mist text-xl text-oil brake-all">{{ $page.props.auth.user.name }}</div>
                    <div class="text-tundora dark:text-spun-pearl brake-all">{{ $page.props.auth.user.email }}</div>
                </div>
                <div class="flex mt-8">
                    <p class="text-tundora dark:text-spun-pearl">Joined</p>
                    <p class="text-oil dark:text-lavender-mist ml-3">{{ new Date($page.props.auth.user.created_at).toDateString()}}</p>
                </div>
                <div class="flex mt-8">
                    <p class="text-tundora dark:text-spun-pearl">Languages</p>
                    <p class="text-oil dark:text-lavender-mist ml-3">
                        <Pill color="present" class="ml-2">PHP</Pill>
                        <Pill color="present" class="ml-2">Vue</Pill>
                        <Pill color="present" class="ml-2">Laravel</Pill>
                    </p>
                </div>
            </div>
            <div>Other</div>
        </div>
       
    </AppLayout>
</template>
