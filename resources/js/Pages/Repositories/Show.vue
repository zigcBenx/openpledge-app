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
                            <Button v-if="repository.direct_from_github"
                                @click="isRepositoryOwner ? showConnectRepositoryModal = true : showContactOwnerModal = true"
                                :loading="loadingConnect" color="primary" class="px-8 h-11">
                                Connect
                            </Button>

                            <Icon v-else name="star" id="favorite-icon" :class="['stroke-tundora dark:hover:stroke-green', {
                                'dark:fill-green dark:stroke-green fill-tundora': repository.favorite
                            }]" size="lg" @click="addFavorites(repository)" />

                            <!-- Contact Repository Owner Modal -->
                            <DialogModal :show="showContactOwnerModal" @close="showContactOwnerModal = false">
                                <template #title>
                                    Request Repository Connection
                                </template>

                                <template #content>
                                    <p>Sorry, this repository is not connected yet. If you would like to see it on
                                        OpenPledge, please request the repository owner to connect it.</p>
                                    <p class="mt-4">If you are the repository owner, please log in using your GitHub
                                        account associated with this repository.</p>
                                </template>
                            </DialogModal>

                            <!-- Repository Connect Modal -->
                            <DialogModal :show="showConnectRepositoryModal" @close="showConnectRepositoryModal = false">
                                <template #title>
                                    Repository {{ isGithubAppConnected ? 'Reconnection' : 'Connection' }}
                                </template>

                                <template #content>
                                    <div v-if="isGithubAppConnected">
                                        You haven't connected this repository to OpenPledge yet. Click the 'Connect'
                                        button below and follow these steps to connect:
                                    </div>
                                    <div v-else>
                                        To connect this repository, click the 'Connect' button below and follow these
                                        steps:
                                    </div>
                                    <div class="mt-4">
                                        <p class="mt-2 font-semibold">- Look for the section labeled <em>Repository
                                                access</em> (as shown in the screenshot below).</p>
                                        <img class="object-contain mt-2 rounded-md w-full rounded"
                                            alt="Screenshot of the GitHub repository access section"
                                            src="../../../img/github_repository_access_section.jpg">
                                        <p class="mt-4">You have two options:</p>
                                        <p class="mt-2"><span class="font-semibold">- All repositories:</span> This
                                            option gives the app access to all current and future repositories.</p>
                                        <p class="mt-2"><span class="font-semibold">- Only select repositories:</span>
                                            This option allows you to choose specific repositories. To connect another
                                            repository, select <span class="font-semibold">Only select
                                                repositories</span>. A list of your repositories will appear. Tick the
                                            box next to each repository you want to grant access to.</p>
                                        <p class="mt-4 font-semibold">Ensure you carefully select the repositories you
                                            want to grant access to. Granting access to the wrong repositories can lead
                                            to other OpenPledgers viewing your GitHub issues.</p>
                                        <p class="mt-4 font-semibold">Once you've selected the desired repositories,
                                            click the <img class="inline pb-1" width="40"
                                                alt="Screenshot of the GitHub save button"
                                                src="../../../img/github_save_button.png"> button.</p>
                                    </div>
                                </template>

                                <template #footer>
                                    <a :href="githubAppInstallationUrl">
                                        <Button class="mt-9 px-8 h-11" color="primary">
                                            Connect
                                        </Button>
                                    </a>
                                </template>
                            </DialogModal>
                        </div>
                    </div>
                    <div>
                        <div class="w-full flex mt-8 items-center">
                            <p class="w-2/12 text-tundora dark:text-spun-pearl uppercase text-xs">About</p>
                        </div>

                        <div class="w-full flex mt-8 items-center">
                            <p class="w-2/12 text-tundora dark:text-spun-pearl uppercase text-xs">Languages</p>
                            <div class="w-10/12">
                                <div class="flex gap-2 text-oil dark:text-lavender-mist">
                                    <Pill v-for="language in repository.programming_languages" :key="language.id"
                                        color="present" :contentClasses="['px-2']">
                                        {{ language.name }}
                                    </Pill>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-28">
                        <div class="flex justify-between">
                            <p class="text-2xl dark:text-lavender-mist text-oil">Issues</p>
                            <div class="flex gap-2" id="issue-types-container">
                                <Pill :color="selectedPledgedIssues ? 'secondary' : 'primary'"
                                    @click="selectedPledgedIssues = true">
                                    Pledged {{ repository.issues_count }}
                                </Pill>
                                <Pill :color="selectedPledgedIssues ? 'primary' : 'secondary'"
                                    @click="selectedPledgedIssues = false">
                                    Open {{ listOfIssues.length }}
                                </Pill>
                            </div>
                        </div>
                        <div>
                            <IssuesTable v-if="selectedPledgedIssues" :issues="repository.issues" :pledged="true" />
                            <IssuesTable v-else :issues="listOfIssues" :repository="repository" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex-grow flex-shrink-0 w-4/12">
                <GithubRepositoryConnect />
                <TrendingToday />
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import Icon from '@/Components/Icon.vue'
import GithubRepositoryConnect from '@/Components/Custom/GithubRepositoryConnect.vue'
import { ref, onMounted } from 'vue'
import Pill from '@/Components/Form/Pill.vue'
import Button from '@/Components/Button.vue'
import IssuesTable from '@/Components/Custom/IssuesTable.vue'
import DialogModal from '@/Components/DialogModal.vue'
import TrendingToday from '@/Components/Custom/TrendingToday.vue'
import { useToast } from "vue-toastification";
import { getRepositoryTour } from '@/utils/onboardingWalkthrough.js';
import { router } from '@inertiajs/vue3';

const props = defineProps
    ({
        repository: Object,
        issues: Array,
        isRepositoryOwner: Boolean,
        isGithubAppConnected: Boolean
    });

const loadingConnect = ref(false)
const selectedPledgedIssues = ref(props.repository.issues_count > 0)
const listOfIssues = ref(props.issues)

const githubAppInstallationUrl = import.meta.env.VITE_GITHUB_APP_INSTALLATION_URL

const showConnectRepositoryModal = ref(false)
const showContactOwnerModal = ref(false)

const addFavorites = (repository) => {
    const toast = useToast()
    axios.post(route('favorites.store'), {
        favorable_id: repository.id,
        favorable_type: 'Repository',
    })
        .then(response => {
            const toastOptions = response.data.message.includes('added') 
                ? {
                    onClick: () => router.visit(route('profile.favorites-show')),
                    toastClassName: 'cursor-pointer hover:opacity-90'
                } 
                : {};
            toast.success(response.data.message, toastOptions);
            repository.favorite = !repository.favorite
        })
        .catch(error => {
            toast.error('Something went wrong!')
            console.error(error);
        });
}

onMounted(() => {
    if (localStorage.getItem('isTutorialInProgress') === 'true') {
        const repositoryTour = getRepositoryTour(props.repository.issues[0]?.id || listOfIssues.value[0]?.id);
        repositoryTour.start();
    }
});
</script>