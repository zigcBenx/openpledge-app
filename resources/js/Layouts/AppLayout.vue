<template>
    <div>
        <Head :title="title" />
        <Banner />
        <div class="min-h-screen bg-lavender-mist bg-gray-100 dark:bg-oil">
            <nav>
                <!-- Primary Navigation Menu -->
                <div class="p-4 md:p-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('discover.issues')">
                                    <ApplicationMark class="block h-9 w-auto" :isDark="isDark" />
                                </Link>
                            </div>
                            <!-- Navigation Links -->
                            <div class="space-x-8 sm:-my-px sm:ms-10 content-center">
                                <NavLink :href="route('discover.issues')" :active="route().current('discover.issues')">
                                    Discover
                                </NavLink>
                                <div class="cursor-pointer inline-flex uppercase items-center px-1 pb-2 pt-1 border-b-2 border-transparent text-sm leading-5 text-rich-black dark:text-platinum hover:text-green dark:hover:text-green"
                                    @click="displayLeaderBoardModal = true">
                                    Leaderboard
                                </div>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center gap-8">
                            <div class="space-x-8 sm:-my-px sm:ms-10 content-center">
                                <Dropdown align="right" width="44.375rem">
                                    <template #trigger>
                                        <Input
                                            id="search-input"
                                            v-model="searchQuery"
                                            inputClass="focus:w-[44.375rem]"
                                            placeholder="Search" 
                                            type="search" 
                                            icon="search"
                                            :closeOnOutside="true"
                                            @onInput="searchQuery = $event.target.value"
                                        />
                                    </template>

                                    <template #content>
                                        <SearchCard 
                                            :isDark="isDark"
                                            class="w-[44.375rem]" 
                                            :data="filteredData" 
                                            checkboxLabel="Show GitHub results"
                                            :getSearchItemHref="generateSearchItemHref"
                                            @checkbox-toggled="includeGitHubResults = $event"
                                        />
                                    </template>
                                </Dropdown>                                
                            </div>
                            <div>
                                <Icon name="bell" 
                                    class="dark:stroke-spun-pearl hover:fill-green stroke-tundora"  
                                />
                            </div>
                            <div class="relative">
                                <Dropdown align="right" width="400px">
                                    <template #trigger>
                                        <Icon name="user" 
                                            class="dark:stroke-spun-pearl hover:fill-green stroke-tundora"
                                        />
                                    </template>

                                    <template #content>
                                        <MenuCard :isDark="isDark" class="w-[25rem]"/>
                                    </template>
                                </Dropdown>
                            </div>
                            <div>
                                <Icon name="moon" 
                                    v-if="isDark"
                                    class="dark:stroke-spun-pearl hover:fill-green"
                                    @click="toggleDark()" 
                                />
                                <Icon name="sun"
                                    v-else
                                    class="stroke-tundora hover:fill-green fill-tundora"
                                    @click="toggleDark()"
                                />
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <div class="pr-2">
                                <Icon name="bell" 
                                    class="dark:stroke-spun-pearl hover:fill-green stroke-tundora"  
                                />
                            </div>
                            <button class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out" @click="showingNavigationDropdown = ! showingNavigationDropdown">
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('discover.issues')" :active="route().current('discover.issues')">
                            Discover
                        </ResponsiveNavLink>
                        <div @click="displayLeaderBoardModal=true" :active="route().current('donations.index')">
                            Leaderboard
                        </div>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex items-center px-4">
                            <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 me-3">
                                <img class="h-10 w-10 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                            </div>

                            <div>
                                <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                                    {{ $page.props.auth.user.name }}
                                </div>
                                <div class="font-medium text-sm text-gray-500">
                                    {{ $page.props.auth.user.email }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.settings')" :active="route().current('profile.settings')">
                                Profile
                            </ResponsiveNavLink>

                            <!-- <ResponsiveNavLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')" :active="route().current('api-tokens.index')">
                                API Tokens
                            </ResponsiveNavLink> -->

                            <ResponsiveNavLink @click="toggleDark()" as="button">
                                   <button
                                        class="text-gray-600 dark:text-gray-400"
                                    >
                                        <i :class="{'fa-solid': true, 'fa-sun': !isDark, 'fa-moon': isDark}"></i> Switch theme
                                    </button>
                            </ResponsiveNavLink>

                            <!-- Authentication -->
                            <form method="POST" @submit.prevent="logout">
                                <ResponsiveNavLink as="button">
                                    Log Out
                                </ResponsiveNavLink>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <!--<header v-if="$slots.header" class="shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>-->

            <!-- Page Content -->
            <main class="px-4 md:px-16">
                <slot />
            </main>
        </div>
    </div>
    <div class="fixed bottom-0 left-0 w-full text-white w-100 bg-openpledge-yellow p-2 text-center">
        OpenPledge is in <b>BETA</b>. Things might get a little quirky! 🚀 All donations are fictional.
    </div>
    <Button
            @click="displayFeedbackModal = true"
            class="fixed right-0 top-1/2 rounded-none !w-40"
        >
            Provide Feedback ✏️
    </Button>
    <DialogModal :show="displayFeedbackModal" @close="displayFeedbackModal = false">
        <template #title>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                We Value Your Feedback
            </h2>
        </template>
        <template #content>
            <div v-if="feedbackModalMessage" class="mb-6">{{ feedbackModalMessage }}</div>
            <div v-else>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Your Email
                    </label>
                    <Input
                        type="email"
                        v-model="feedbackData.email"
                        placeholder="your.email@example.com"
                        inputClass="w-full"
                    />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">We'll only use your email to follow up on your feedback.</p>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Your Feedback
                    </label>
                    <TextArea
                        maxlength="500"
                        v-model="feedbackData.content" 
                        placeholder="Let us know what you think..."
                        textAreaClass="w-full h-32" 
                    />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Your thoughts help us improve!</p>
                </div>
            </div>
        </template>
        <template #footer>
            <Button 
                @click="feedbackModalMessage ? displayFeedbackModal = false : submitFeedback()"
                :disabled="!feedbackModalMessage && (!feedbackData.email || !feedbackData.content)"
            >
                {{ feedbackModalMessage ? 'Close' : 'Help Us Improve' }}
            </Button>
        </template>
    </DialogModal>
</template>

<script>
    import { ref, computed, watch } from 'vue';
    import { Head, Link, router, usePage } from '@inertiajs/vue3';
    import ApplicationMark from '@/Components/ApplicationMark.vue';
    import Banner from '@/Components/Banner.vue';
    import Dropdown from '@/Components/Dropdown.vue';
    import NavLink from '@/Components/NavLink.vue';
    import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
    import { useDark, useDebounce, useToggle } from '@vueuse/core';
    import Icon from '@/Components/Icon.vue';
    import MenuCard from './Partials/MenuCard.vue';
    import SearchCard from './Partials/SearchCard.vue';
    import Input from '@/Components/Input.vue';
    import axios from 'axios';
    import DialogModal from '@/Components/DialogModal.vue';
    import Button from '@/Components/Button.vue';
    import TextArea from '@/Components/TextArea.vue';
    import { useToast } from "vue-toastification";

    export default {
        props: {
            title: String
        },
        components: {
            Link,
            Head,
            ApplicationMark,
            Banner,
            Dropdown,
            NavLink,
            ResponsiveNavLink,
            Icon,
            MenuCard,
            SearchCard,
            Input,
            DialogModal,
            Button,
            TextArea
        },
        setup() {
            const isDark = useDark();
            const toggleDark = useToggle(isDark);
            const showingNavigationDropdown = ref(false);
            const searchQuery = ref('');
            const debouncedSearchQuery = useDebounce(searchQuery, 300);
            const displayLeaderBoardModal = ref(false);
            const displayFeedbackModal = ref(false);
            const feedbackModalMessage = ref('');
            const feedbackData = ref({
                email: '',
                content: ''
            });
            const data = ref({
                repositories: [],
                issues: []
            });
            const includeGitHubResults = ref(false);
            const user = usePage().props.auth.user;

            const logout = () => {
                router.post(route('logout'));
            };

            const fetchSearchResults = async (query, includeGitHub) => {
                try {
                    const response = await axios.get(route('search'), {
                        params: {
                            query,
                            includeGitHub
                        }
                    });
                    data.value = response.data;
                } catch (error) {
                    console.error('Error fetching search data:', error);
                }
            };

            watch([debouncedSearchQuery, includeGitHubResults], ([newQuery, includeGitHub]) => {
                if (newQuery.length === 0) {
                    data.value = { repositories: [], issues: [] };
                } else {
                    fetchSearchResults(newQuery, includeGitHub);
                }
            });

            const filteredData = computed(() => {
                const { repositories, issues } = data.value;

                if (debouncedSearchQuery.value.length === 0) {
                    return [];
                }

                if (repositories.length === 0 && issues.length === 0) {
                    return [{ name: "No matches found" }];
                }

                return [
                    {
                        name: 'Repositories',
                        values: repositories.map(repo => ({ id: repo.id, text: repo.title }))
                    },
                    {
                        name: 'Issues',
                        values: issues.map(issue => ({ id: issue.id, text: issue.title, repository_title: issue.repository_title }))
                    }
                ].filter(item => item.values.length > 0);
            });

            const handleInput = (event) => {
                searchQuery.value = event.target.value;
            };

            const generateSearchItemHref = (name, value) => {

                if (value.repository_title) {
                    return route('repositories.show', { githubUser: value.repository_title.split('/')[0], repository: value.repository_title.split('/')[1] })
                }

                switch (name) {
                    case 'Repositories':
                        return route('repositories.show', { githubUser: value.text.split('/')[0], repository: value.text.split('/')[1] })
                    case 'Issues':
                        return route('issues.show', {issue: value.id})
                    default:
                        return '#';
                }
            }

            const submitFeedback = () => {
                const toast = useToast();

                axios.post(route('user.feedback'), {
                    email: feedbackData.value.email,
                    content: feedbackData.value.content
                })
                .then(response => {
                    toast.success(response.data.toastMessage);
                    displayFeedbackModal.value = true;
                    feedbackModalMessage.value = response.data.modalMessage;
                    feedbackData.value.email = '';
                    feedbackData.value.content = '';
                })
                .catch(error => {
                    toast.error('Something went wrong!')
                    console.error(error);
                });
            };

            return {
                showingNavigationDropdown,
                logout,
                user,
                toggleDark,
                isDark,
                searchQuery,
                filteredData,
                handleInput,
                includeGitHubResults,
                generateSearchItemHref,
                displayLeaderBoardModal,
                displayFeedbackModal,
                feedbackData,
                submitFeedback,
                feedbackModalMessage
            };
        }
    };
</script>