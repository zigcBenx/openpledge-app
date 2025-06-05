<template>
    <TopList 
        :class="{
            'bg-mint-green text-turquoise dark:bg-dark-green': true && isPledgingAnonymously
        }"
        class="dark:text-white"
        containerClass="mt-6 p-4 rounded-lg shadow-lg transition duration-150 ease-in-out"
        title="Anonymous pledges"
        subtitle="Total amount of anonymous pledges"
    >
        <div class="flex items-center">
            <span class="font-bold text-lg mr-2">💰</span>
            <span 
                class="font-medium text-lg" 
                :class="{
                    'text-purple-heart': !isPledgingAnonymously
                }"
            >
                {{ anonymousDonationsAmount }} €
            </span>
        </div>
        <p class="mt-2 text-sm italic text-tundora dark:text-spun-pearl">"Keep this pledge private—your identity won’t be shown."</p>
        <div class="w-full flex mt-4 justify-between items-center">
            <span class="dark:text-white">Pledge Anonymously</span>
            <Switch v-model="isPledgingAnonymously" :disabled="!isAuthenticated" @click="togglePledgingAnonymously" />
        </div>
    </TopList>
</template>

<script setup>
import TopList from '@/Components/Custom/TopList.vue'
import { onMounted, ref, watch } from 'vue'
import axios from 'axios'
import Switch from '@/Components/Switch.vue'
import { useToast } from 'vue-toastification'
import { usePage, router } from '@inertiajs/vue3'

const toast = useToast()
const page = usePage()

const authenticatedUser = () => {
  return page.props.auth?.user
}

const isAuthenticated = ref(authenticatedUser() !== null)
const isPledgingAnonymously = ref(Boolean(isAuthenticated.value ? authenticatedUser().is_pledging_anonymously : true))
const anonymousDonationsAmount = ref(null)

onMounted(() => {
    fetchAnonymousDonations()
})

async function fetchAnonymousDonations() {
    const response = await axios.get(route('anonymous-donations'))
    anonymousDonationsAmount.value = response.data
}

const togglePledgingAnonymously = () => {
    if (!isAuthenticated.value) {
        router.visit(route('login'))
    }
}

watch(isPledgingAnonymously, async (newValue) => {
    if (!isAuthenticated.value) {
        return
    }
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