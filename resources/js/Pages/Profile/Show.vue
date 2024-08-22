<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Earnings from './Partials/Earnings.vue';
import Graphs from './Partials/Graphs.vue';
import IssueSummary from './Partials/IssueSummary.vue';
import ProfileCard from './Partials/ProfileCard.vue';
import InstalledRepositories from './Partials/InstalledRepositories.vue';
import Favorites from './Partials/Favorites.vue';

defineProps({});
</script>

<template>
    <AppLayout title="Profile">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Profile
            </h2>
        </template>

        <div class="mx-auto flex gap-8 w-full max-w-screen-2xl">
            <div class="xl:w-3/12">
                <ProfileCard />
            </div>
            <div class="w-full">
                <div class="flex w-full gap-4">
                    <div class="w-4/12">
                        <Earnings />
                    </div>
                    <div class="w-8/12">
                        <Graphs />
                    </div>
                </div>
                <div class="mt-4">
                    <InstalledRepositories />
                </div>
                <div class="mt-4">
                    <Favorites />
                </div>
                <div class="mt-4">
                    <IssueSummary />
                </div>
            </div>
        </div>
       
    </AppLayout>
</template>
