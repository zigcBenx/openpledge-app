<script setup>
import { ref, watch } from 'vue'
import Pill from "@/Components/Form/Pill.vue";
import Icon from "@/Components/Icon.vue";
import Switch from "@/Components/Switch.vue";
import { useToast } from 'vue-toastification';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage()
const isPledgingAnonymously = ref(Boolean(page.props.auth.user.is_pledging_anonymously))
const toast = useToast()

watch(isPledgingAnonymously, async (newValue) => {
  try {
    axios.post(route('profile.settings.anonymous-pledging'), {
        is_pledging_anonymously: newValue
    }, {
        preserveScroll: true,
        preserveState: true
    }).then(response => {
        toast.success(response.data.message)
    })
  } catch (error) {
    toast.error('Something went wrong!')
    console.error(error)
  }
})
</script>

<template>
  <div class="flex flex-col items-center dark:bg-charcoal-gray bg-seashell p-4 rounded-md">
    <div class="w-full flex flex-row-reverse">
      <Link :href="route('profile.settings')">
        <Icon name="pencil" class="stroke-icon-idle-gray fill-icon-idle-gray hover:fill-green"/>
      </Link>
    </div>
    <div class="w-[8.25rem] flex justify-center">
        <img :src="$page.props.auth.user.profile_photo_url" class="rounded-full" />
    </div>
    <div class="flex flex-col items-center mt-3">
        <div class="dark:text-lavender-mist text-xl text-oil brake-all">{{ $page.props.auth.user.name }}</div>
        <div class="text-tundora dark:text-spun-pearl brake-all">{{ $page.props.auth.user.email }}</div>
    </div>
    <div class="w-full flex mt-8 justify-between items-center">
        <p class="text-tundora dark:text-spun-pearl uppercase text-xs">Joined</p>
        <p class="text-oil dark:text-lavender-mist">{{ new Date($page.props.auth.user.created_at).toDateString()}}</p>
    </div>
    <div class="w-full flex mt-8 justify-between items-center">
        <p class="text-tundora dark:text-spun-pearl uppercase text-xs">Languages</p>
        <p class="text-oil dark:text-lavender-mist">
            <Pill color="present" :contentClasses="['px-2']" class="ml-2">PHP</Pill>
            <Pill color="present" :contentClasses="['px-2']" class="ml-2">Vue</Pill>
            <Pill color="present" :contentClasses="['px-2']" class="ml-2">Laravel</Pill>
            <Pill color="present" :contentClasses="['px-2']" class="ml-2">Nuxt</Pill>
            <Pill color="present" :contentClasses="['px-2']" class="ml-2">Bash</Pill>
        </p>
    </div>
    <div class="flex-col mt-8 w-full">
        <p class="text-white">Leaderboard</p>
        <p class="text-gray-600 bg-gunmetal p-3 mt-3 rounded-md"># <span class="text-white">45</span></p>
    </div>
    <div class="w-full flex mt-8 justify-between items-center">
      <p class="text-white">Pledge Anonymously</p>
      <Switch v-model="isPledgingAnonymously" />
    </div>
  </div>
</template>