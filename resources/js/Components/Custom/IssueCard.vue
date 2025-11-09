<template>
    <div
        :class="['bg-white dark:bg-charcoal-gray rounded-lg border-l-4 p-4 shadow-sm', {
            'border-ocean-green dark:border-green': issue.state === 'open',
            'border-tundora': issue.state === 'closed',
            'dark:bg-rich-black bg-ghost-white': issue.isExternal
        }]"
    >
        <!-- Status Badge & Title -->
        <div class="flex items-start justify-between gap-3 mb-3">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-2">
                    <span
                        :class="['text-xs font-medium uppercase px-2 py-1 rounded', {
                            'text-ocean-green dark:text-green bg-mint-green dark:bg-shade-green': issue.state === 'open',
                            'text-spun-pearl bg-gray-200 dark:bg-gunmetal': issue.state === 'closed'
                        }]"
                    >
                        {{ issue.state }}
                    </span>
                </div>
                <Link
                    :class="['text-base font-medium dark:text-white dark:hover:text-green hover:text-green line-clamp-2', {
                        '!text-spun-pearl': issue.state === 'closed'
                    }]"
                    :href="'/issues/' + issue.id"
                >
                    {{ issue.title }}
                </Link>
            </div>
            <Icon
                name="star"
                width="1.375rem"
                :class="getIconStrokeColor(issue.favorite, issue.state === 'closed')"
                :disabled="issue.state === 'closed'"
                @click="addFavorites(issue)"
            />
        </div>

        <!-- Repository (if pledged) -->
        <div v-if="pledged && issue.repository" class="mb-3">
            <Link
                :href="'/repositories/' + issue.repository.title"
                class="text-sm font-medium dark:text-white hover:text-green dark:hover:text-green"
            >
                {{ issue.repository.title }}
            </Link>
        </div>

        <!-- Author Info -->
        <div class="flex items-center gap-2 mb-3">
            <Avatar :url="issue.user_avatar" size="sm" />
            <span class="dark:text-spun-pearl text-tundora text-xs font-medium">{{ issue.github_username }}</span>
            <span class="dark:text-spun-pearl text-tundora text-xs">{{ dayjs(issue.github_created_at).fromNow() }}</span>
            <a :href="issue.github_url" target="_blank" class="ml-auto">
                <i class="fa-brands fa-github dark:text-white hover:text-green dark:hover:text-green text-lg"/>
            </a>
        </div>

        <!-- Resolved Info (if applicable) -->
        <div v-if="issue.resolved_by" class="flex items-center gap-2 mb-3 p-2 bg-mint-green dark:bg-shade-green rounded">
            <Avatar :url="issue.resolved_by.profile_photo_url" size="sm" />
            <span class="text-ocean-green dark:text-green text-xs font-medium">{{ issue.resolved_by.name }}</span>
            <span class="text-ocean-green dark:text-green text-xs">was paid out</span>
            <span class="text-ocean-green dark:text-green text-xs font-medium">{{ issue.donations_sum_net_amount ?? 0 }} $</span>
            <span class="text-ocean-green dark:text-green text-xs">{{ dayjs(issue.resolved_at).fromNow() }}</span>
        </div>

        <!-- Labels -->
        <div v-if="issue.labels && issue.labels.length > 0" class="mb-3">
            <div class="flex flex-wrap gap-1">
                <Pill
                    v-for="label in issue.labels"
                    :key="label.id"
                    color="present"
                    size="sm"
                    :disabled="issue.state === 'closed'"
                >
                    {{ label.name.charAt(0).toUpperCase() + label.name.slice(1) }}
                </Pill>
            </div>
        </div>

        <!-- Languages -->
        <div class="mb-3">
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
        </div>

        <!-- Bottom Actions -->
        <div class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-gray-700">
            <div v-if="pledged">
                <span
                    :class="['text-lg font-semibold', {
                        'text-purple-heart': issue.state === 'open',
                        'dark:text-spun-pearl text-tundora': issue.state === 'closed'
                    }]"
                >
                    {{ issue.donations_sum_net_amount ?? 0 }} $
                </span>
            </div>
            <div class="flex-1"></div>
            <div>
                <template v-if="issue.state === 'closed'">
                    <span class="text-spun-pearl text-sm font-medium">
                        closed
                    </span>
                </template>
                <template v-else>
                    <Link
                        v-if="canReceiveDonations"
                        :href="'/issues/' + issue.id"
                    >
                        <Pill color="secondary">
                            Pledge
                        </Pill>
                    </Link>
                </template>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import Avatar from '@/Components/Avatar.vue';
import Icon from '@/Components/Icon.vue';
import Pill from '@/Components/Form/Pill.vue';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import axios from 'axios';
import { useToast } from 'vue-toastification';

dayjs.extend(relativeTime);

const props = defineProps({
    issue: {
        type: Object,
        required: true
    },
    pledged: {
        type: Boolean,
        default: false
    },
    repository: Object,
    isAuthenticated: Boolean
});

const toast = useToast();

const canReceiveDonations = props.isAuthenticated || props.issue.state === 'closed';

const getIconStrokeColor = (favorite, isClosed) => {
    if (isClosed) {
        return 'dark:fill-transparent fill-transparent dark:stroke-spun-pearl stroke-spun-pearl';
    }
    return favorite
        ? 'fill-green dark:fill-green stroke-green dark:stroke-green'
        : 'dark:fill-transparent fill-transparent dark:stroke-spun-pearl stroke-tundora hover:fill-green hover:stroke-green dark:hover:fill-green dark:hover:stroke-green';
};

const addFavorites = async (issue) => {
    if (issue.state === 'closed') return;

    try {
        const response = await axios.post(route('favorites.toggle'), {
            favoritable_type: 'Issue',
            favoritable_id: issue.id
        });
        issue.favorite = !issue.favorite;
        toast.success(response.data.message || "Favorite updated!");
    } catch (error) {
        toast.error("Something went wrong!");
        console.error(error);
    }
};
</script>
