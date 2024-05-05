<template>
    <AppLayout title="Repository Details">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Repository Details
            </h2>
        </template>

        <div class="flex gap-x-8">
            <div class="w-8/12 flex-grow-0 flex-shrink-0">
                <div class="w-full">
                    <div class="flex items-center justify-between dark:bg-charcoal-gray bg-seashell p-5 rounded-md">
                        <div class="flex items-center gap-x-5">
                            <div>
                                <img :src="repository.user_avatar" class="rounded-md h-24" />
                            </div>
                            <p class="dark:text-lavender-mist text-oil text-2xl">{{ repository.title }}</p>
                        </div>
                    
                        <div>
                            <Button v-if="repository.direct_from_github" @click="connect" :loading="loadingConnect" color="primary" class="px-8 h-11">
                                Connect
                            </Button>
                            <Icon 
                                v-else
                                name="star" 
                                class="dark:stroke-mondo dark:fill-mondo"
                            />
                        </div>
                    </div>
                    <div>
                        <div class="w-full flex mt-8 items-center">
                            <p class="w-2/12 text-tundora dark:text-spun-pearl uppercase text-xs">About</p>
                            <p class="w-10/12 text-oil dark:text-lavender-mist">Some lorem ipsum text for filling up space to fill the description part of repository. Make sure to fix this part.</p>
                        </div>

                        <div class="w-full flex mt-8 items-center">
                            <p class="w-2/12 text-tundora dark:text-spun-pearl uppercase text-xs">Languages</p>
                            <div class="w-10/12">
                                <div class="flex gap-2 text-oil dark:text-lavender-mist">
                                    <Pill color="present" :contentClasses="['px-2']">PHP</Pill>
                                    <Pill color="present" :contentClasses="['px-2']">Vue</Pill>
                                    <Pill color="present" :contentClasses="['px-2']">Laravel</Pill>
                                    <Pill color="present" :contentClasses="['px-2']">Nuxt</Pill>
                                    <Pill color="present" :contentClasses="['px-2']">Bash</Pill>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-28">
                        <div class="flex justify-between">
                            <p class="text-2xl dark:text-lavender-mist text-oil">Issues</p>
                            <div class="flex gap-2">
                                <Pill 
                                    color="secondary"
                                >
                                    Pledged 10
                                </Pill>
                                <Pill 
                                    color="primary"
                                >
                                    Open 576
                                </Pill>
                            </div>
                        </div>
                        <div>
                            <IssuesTable :issues="issues" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex-grow flex-shrink-0 w-4/12">
                <GithubIssueConnect />
                <TopList 
                    containerClass="mt-6"
                    title="Trending today" 
                    subtitle="List of top donated open issues today"
                >
                    <li v-for="item in trendingToday" :key="item.id" class="flex justify-between overflow-hidden py-1.5">
                        <span class="pl-1.5 border-l-2 rounded-sm dark:border-green border-ocean-green text-oil dark:text-lavender-mist text-xs">{{ item.title }}</span>
                        <span class="text-mondo dark:text-lavender-mist font-medium text-xs">{{ item.repository }}</span>
                        <span class="text-purple-heart font-medium text-xs">{{ item.donations }}</span>
                    </li>
                </TopList>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import ListIssues from '@/Components/Custom/ListIssues.vue';
import { useToast } from "vue-toastification";
import { router } from '@inertiajs/vue3';
import Icon from '@/Components/Icon.vue';
import GithubIssueConnect from '@/Components/Custom/GithubIssueConnect.vue';
import { ref, onMounted } from 'vue';
import Pill from '@/Components/Form/Pill.vue';
import TopList from '@/Components/Custom/TopList.vue';
import Button from '@/Components/Button.vue';
import {
    trendingToday,
} from '../../assets/mockedData.js'

const props = defineProps
({
    repository: Object,
});

const openIssues = ref([])
const loadingConnect = ref(false)

const connect = () => {
    loadingConnect.value = true
    axios.post('/repositories/connect', {
            repository: props.repository.title,
        })
        .then((response) => {
            const toast = useToast()
            toast.success(`Repository ${props.repository.title} is now available on OpenPledge!`)
            const username = props.repository.title.split('/')[0]
            const repo = props.repository.title.split('/')[1]
            router.visit(route('repositories.show', { githubUser: username, repository: repo }))
        }).catch((err) => {
            const toast = useToast()
            toast.error(err.response.data.message)
        }).finally(() => {
            loadingConnect.value = false
        })
}
</script>