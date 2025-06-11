<template>
    <td :class="['rounded-bl-md font-medium overflow-hidden border-l-[6px] border-ocean-green dark:border-green pl-3.5 py-6 rounded-tl-md', {
        '!border-tundora': issue.state === 'closed'
        }]">
        <div
            :class="['text-ocean-green dark:text-green', {
            '!text-spun-pearl': issue.state === 'closed'
            }]"
        >
            {{ issue.state }}
        </div>
    </td>
    <td class="py-6">
        <Link
            :class="['dark:text-white dark:hover:text-green hover:text-green text-base pr-4', {
                '!text-spun-pearl': issue.state === 'closed'
            }]"
            :href="'/issues/' + issue.id"
            >
            {{ issue.title }}
        </Link>
        <a :href="issue.github_url" target="_blank">
            <i class="fa-brands fa-github dark:text-white hover:text-green dark:hover:text-green"/>
        </a>

        <div class="flex gap-1 mt-3">
            <Avatar :url="issue.user_avatar" size="sm" />
            <span class="dark:text-spun-pearl text-tundora text-xs font-medium">{{ issue.github_username }}</span>
            <span class="dark:text-spun-pearl text-tundora text-xs font-light">{{ dayjs(issue.github_created_at).fromNow() }}</span>
        </div>

        <div class="flex gap-1 mt-3" v-if="issue.resolved_by">
            <Avatar :url="issue.resolved_by.profile_photo_url" size="sm" />
            <span class="text-ocean-green dark:text-green text-xs font-medium">{{ issue.resolved_by.name }}</span>
            <span class="text-ocean-green dark:text-green text-xs font-light">was paid out</span>
            <span class="text-ocean-green dark:text-green text-xs font-medium">{{ issue.donations_sum_net_amount ?? 0 }} €</span>
            <span class="text-ocean-green dark:text-green text-xs font-light">{{ dayjs(issue.resolved_at).fromNow() }}</span>
        </div>
    </td>
    <td class="py-6 pr-4 align-middle">
        <div class="flex flex-wrap gap-1">
            <Pill
                v-if="issue.labels && issue.labels.length > 0"
                v-for="label in issue.labels"
                :key="label.id"
                color="present"
                size="sm"
                :disabled="issue.state === 'closed'"
            >
                {{ label.name.charAt(0).toUpperCase() + label.name.slice(1) }}
            </Pill>
        </div>
    </td>
    <td class="py-6">
        <span class="dark:text-white font-medium text-xs pr-4">
            <Link :href="'/repositories/' + issue.repository.title">
                {{ issue.repository.title }}
            </Link>
        </span>
    </td>
    <td class="py-6 pr-4 align-middle">
        <div class="flex flex-wrap gap-1">
            <Pill
                v-if="issue.programming_languages?.length > 0"
                v-for="issue_lang in issue.programming_languages"
                :key="issue_lang"
                color="present"
                size="sm"
                :disabled="issue.state === 'closed'"
            >
                {{ issue_lang.name }}
            </Pill>
            <Pill
                v-else-if="issue.repository?.programming_languages?.length > 0"
                v-for="lang in issue.repository.programming_languages"
                :key="lang"
                color="present"
                size="sm"
                :disabled="issue.state === 'closed'"
            >
                {{ lang.name }}
            </Pill>
        </div>
    </td>
    <td class="py-6">
        <span
            :class="['text-purple-heart font-medium', {
            '!dark:text-spun-pearl text-tundora': issue.state === 'closed'
            }]"
        >
            {{ issue.donations_sum_net_amount ?? 0 }} €
        </span>
    </td>
    <td class="rounded-br-md rounded-tr-md pr-6">
        <div class="flex justify-end">
            <Icon
                name="star"
                width="1.375rem"
                :class="getIconStrokeColor(issue.favorite, issue.state === 'closed')"
                :disabled="issue.state === 'closed'"
                @click="addFavorites(issue)"
            />
            <Link
                :class="['dark:text-white dark:hover:text-green hover:text-green text-base', {
                '!text-spun-pearl': issue.state === 'closed'
            }]"
                :href="'/issues/' + issue.id"
            >
                <Pill
                    color="secondary"
                    class="ml-4"
                >
                    Pledge
                </Pill>
            </Link>
        </div>
    </td>
</template>

<script setup>
    import dayjs from '@/libs/dayjs'
    import { useDark } from '@vueuse/core'
    import { ref } from 'vue'
    import Icon from '@/Components/Icon.vue'
    import Avatar from '@/Components/Avatar.vue'
    import { Link } from '@inertiajs/vue3'
    import Pill from '@/Components/Form/Pill.vue'
    import { useToast } from "vue-toastification";
    import { router } from '@inertiajs/vue3';
    import { usePage } from '@inertiajs/vue3';

    const props = defineProps({
        issue: {
            type: Object,
            required: true,
        },
        isAuthenticated: {
            type: Boolean,
            default: true,
        },
    })

    const isDark = useDark()
    const page = usePage()
    const isAuthenticated = page.props.auth.user !== null

    const getIconStrokeColor = (isFavorite, isDisabled) => {
        if (isDark.value) {
            if (isDisabled) return 'dark:stroke-mondo dark:fill-mondo';
                return isFavorite ? 'dark:stroke-green dark:fill-green' : 'dark:stroke-spun-pearl dark:hover:stroke-green';
            }
        if (isDisabled) {
            return 'stroke-spun-pearl dark:fill-spun-pearl';
        }
        return isFavorite ? 'stroke-ocean-green fill-ocean-green' : 'stroke-tundora hover:stroke-ocean-green';
    }

    const addFavorites = (issue) => {
        const toast = useToast()

        if (!isAuthenticated) {
            toast.error('Please log in to add this issue to favorites');
            return;
        }

        axios.post(route('favorites.store'), {
            favorable_id: issue.id,
            favorable_type: 'Issue',
        })
        .then(response => {
            const toastOptions = response.data.message.includes('added')
                ? {
                    onClick: () => router.visit(route('profile.favorites-show')),
                    toastClassName: 'cursor-pointer hover:opacity-90'
                }
                : {};
            toast.success(response.data.message, toastOptions);
            issue.favorite = !issue.favorite;
        })
        .catch(error => {
            toast.error('Something went wrong!')
            console.error(error);
        });
    }
</script>
