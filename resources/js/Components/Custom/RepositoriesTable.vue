<template>
    <div v-if="!repositories.length" class="flex flex-col items-center justify-center w-full h-screen">
        <div class="flex flex-col items-center gap-1 mt-20">
            <Icon name="error" class="fill-tundora dark:fill-spun-pearl"></Icon>
        </div>
        <div class="text-[1.56rem] text-oil dark:text-lavender-mist mt-7 text-center">Oops! No matches found...</div>
        <div class="dark:text-spun-pearl text-tundora text-xs text-center">We couldn’t find any matches for your current
            filters. Please try changing your search criteria or clear the filters to see more repositories.</div>
    </div>
    <table v-else class="w-full border-separate border-spacing-x-0 border-spacing-y-4">
        <thead>
            <tr class="text-tundora dark:text-spun-pearl uppercase text-xs text-left">
                <th class="pb-5 font-normal">Name</th>
                <th class="pb-5 font-normal">Pledged Issues</th>
                <th class="pb-5 font-normal">Open Issues</th>
                <th class="pb-5 font-normal">Issue Donations Total</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="repository in repositories" :key="repositories.id"
                class="text-sm bg-white dark:bg-charcoal-gray border-separate">
                <RepositoryItem :repository="repository" />
            </tr>
            <tr v-intersection-observer="onIntersectionObserver"></tr>
        </tbody>
    </table>
</template>

<script setup>
import Icon from '@/Components/Icon.vue'
import { vIntersectionObserver } from '@vueuse/components'
import RepositoryItem from '@/Components/Custom/RepositoryItem.vue'

defineProps({
    repositories: {
        type: Array,
        required: true,
        default: [],
        validator: function (value) {
            return value.every(item => {
                return typeof item === 'object' && item.hasOwnProperty('id')
            });
        }
    },
    repository: Object,
})

const emit = defineEmits(['onLazyLoading'])

const onIntersectionObserver = ([{ isIntersecting }]) => {
    if (isIntersecting) {
        emit('onLazyLoading');
    }
}
</script>