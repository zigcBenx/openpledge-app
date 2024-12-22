<script setup>
import Avatar from '@/Components/Avatar.vue';
import dayjs from 'dayjs';
import { computed } from 'vue';

const props = defineProps({
    issueActivity: {
        type: Object,
        required: false,
        default: null
    }
});

const displayedData = computed(() => {
    if (props.issueActivity) {
        return {
            avatarUrl: props.issueActivity.actor.avatar_url,
            login: props.issueActivity.actor.login,
            text: props.issueActivity.event,
            date: props.issueActivity.created_at,
            isResolved: props.issueActivity.event === 'resolved',
        };
    }
    return null;
});
</script>

<template>
    <div v-if="displayedData" class="flex gap-3 pl-3">
        <Avatar :url="displayedData.avatarUrl" />
        <div class="pt-1.5">
            <p class="flex gap-1.5 mb-4 text-sm text-tundora dark:text-spun-pearl">
                <span class="font-medium text-mondo dark:text-seashell">{{ displayedData.login }}</span>
                <span :class="displayedData.isResolved ? 'font-medium text-ocean-green' : ''">
                    {{ displayedData.text }} this issue
                </span>
            </p>
        </div>
        <span class="pt-1.5 text-xs text-tundora dark:text-spun-pearl ml-auto whitespace-nowrap">
            {{ dayjs(displayedData.date).fromNow() }}
        </span>
    </div>
</template>