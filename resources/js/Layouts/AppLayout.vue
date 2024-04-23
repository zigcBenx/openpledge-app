<template>
    <div>
        <Head :title="title" />
        <Banner />
        <div class="min-h-screen bg-lavender-mist bg-gray-100 dark:bg-oil">
            <nav>
                <!-- Primary Navigation Menu -->
                <div class="p-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('home')">
                                    <ApplicationMark class="block h-9 w-auto" :isDark="isDark" />
                                </Link>
                            </div>
                            <!-- Navigation Links -->
                            <div class="space-x-8 sm:-my-px sm:ms-10 content-center">
                                <NavLink :href="route('issues.index')" :active="route().current('issues.index')">
                                    Discover
                                </NavLink>
                                <NavLink :href="route('donations.index')" :active="route().current('donations.index')">
                                    Leaderboard
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center gap-8">
                            <div class="space-x-8 sm:-my-px sm:ms-10 content-center">
                                <Dropdown align="right" width="44.375rem">
                                    <template #trigger>
                                        <Input 
                                            inputClass="focus:w-[44.375rem]"
                                            placeholder="Search" 
                                            type="search" 
                                            icon="search"
                                            :closeOnOutside="true"
                                        />
                                    </template>

                                    <template #content>
                                        <SearchCard :isDark="isDark" class="w-[44.375rem]"/>
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
                                    class="dark:stroke-spun-pearl hover:fill-green stroke-tundora"
                                    @click="toggleDark()" 
                                />
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
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
                        <ResponsiveNavLink :href="route('home')" :active="route().current('home')">
                            Home
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('repositories.index')" :active="route().current('repositories.index')">
                            Repositories
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('issues.index')" :active="route().current('issues.index')">
                            Issues
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('donations.index')" :active="route().current('donations.index')">
                            Donations
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('repositories-request-get')" :active="route().current('repositories-request-get')">
                            Request repository
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink v-if="$page.props.user.roles.includes('admin')" :href="route('subscribers')" :active="route().current('subscribers')">
                            Subscribers
                        </ResponsiveNavLink>
                        <ResponsiveNavLink v-if="$page.props.user.roles.includes('admin')" :href="route('campaigns.index')" :active="route().current('campaigns.index')">
                            Campaigns
                        </ResponsiveNavLink>
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
                            <ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')">
                                Profile
                            </ResponsiveNavLink>

                            <ResponsiveNavLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')" :active="route().current('api-tokens.index')">
                                API Tokens
                            </ResponsiveNavLink>

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
            <main class="px-16">
                <slot />
            </main>
        </div>
    </div>
</template>

<script>
    import { ref } from 'vue';
    import { Head, Link, router, usePage } from '@inertiajs/vue3';
    import ApplicationMark from '@/Components/ApplicationMark.vue';
    import Banner from '@/Components/Banner.vue';
    import Dropdown from '@/Components/Dropdown.vue';
    import NavLink from '@/Components/NavLink.vue';
    import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
    import { useDark, useToggle } from '@vueuse/core';
    import Icon from '@/Components/Icon.vue';
    import MenuCard from './Partials/MenuCard.vue';
    import SearchCard from './Partials/SearchCard.vue';
    import Input from '@/Components/Input.vue';

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
            Input
        },
        setup(props) {
            const isDark = useDark();
            const toggleDark = useToggle(isDark);

            const showingNavigationDropdown = ref(false);

            const logout = () => {
                router.post(route('logout'));
            };

            const user = usePage().props.auth.user;

            return {
                showingNavigationDropdown,
                logout,
                user,
                toggleDark,
                isDark
            };
        }
    }
</script>