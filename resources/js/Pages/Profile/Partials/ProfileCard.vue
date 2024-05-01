<script setup>
import Pill from "@/Components/Form/Pill.vue";

</script>

<template>
  <div class="flex flex-col items-center dark:bg-charcoal-gray bg-seashell p-4 rounded-md">
    <div class="w-[8.25rem]">
        <img :src="$page.props.auth.user.profile_photo_url" class="rounded-full float-right" />
    </div>
    <div class="flex flex-col items-center mt-3">
        <div class="dark:text-lavender-mist text-xl text-oil brake-all">{{ $page.props.auth.user.name }}</div>
        <div class="text-tundora dark:text-spun-pearl brake-all">{{ $page.props.auth.user.email }}</div>
    </div>
    <div class="flex mt-8 justify-between items-center">
        <p class="text-tundora dark:text-spun-pearl uppercase text-xs">Joined</p>
        <p class="text-oil dark:text-lavender-mist">{{ new Date($page.props.auth.user.created_at).toDateString()}}</p>
    </div>
    <div class="flex mt-8 justify-between items-center">
        <p class="text-tundora dark:text-spun-pearl uppercase text-xs">Languages</p>
        <p class="text-oil dark:text-lavender-mist">
            <Pill color="present" :contentClasses="['px-2']" class="ml-2">PHP</Pill>
            <Pill color="present" :contentClasses="['px-2']" class="ml-2">Vue</Pill>
            <Pill color="present" :contentClasses="['px-2']" class="ml-2">Laravel</Pill>
        </p>
    </div>
    <div class="flex-col mt-8 w-full">
        <p class="text-white">Leaderboard</p>
        <p class="text-gray-600 bg-gunmetal p-3 mt-3 rounded-md"># <span class="text-white">45</span></p>
    </div>
  </div>
</template>