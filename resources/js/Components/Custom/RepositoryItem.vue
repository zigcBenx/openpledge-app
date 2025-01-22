<template>
    <td class="py-6 pl-4 align-middle">
        <div class="flex items-center gap-4">
            <div class="flex items-center space-x-4">
                <img :src="repository.user_avatar" class="rounded-lg h-16 w-16" alt="User Avatar" />
                <div class="flex flex-col">
                    <p class="dark:text-white dark:hover:text-green hover:text-green text-base mb-2">
                        <Link :href="'/repositories/' + repository.title">
                        {{ repository.title }}
                        </Link>
                    </p>
                    <div class="flex flex-wrap gap-1">
                        <Pill v-if="repository.programming_languages && repository.programming_languages.length > 0"
                            v-for="lang in repository.programming_languages" :key="lang" color="present" size="sm">
                            {{ lang.name }}
                        </Pill>
                    </div>
                </div>
            </div>
        </div>
    </td>
    <td class="py-6 pr-4 align-middle">
        <span class="text-base font-medium dark:text-white">{{ repository.pledged_issues_count ?? 0 }}</span>
    </td>
    <td class="py-6 pr-4 align-middle">
        <span class="text-base font-medium dark:text-white">{{ repository.open_issues_count ?? 0 }}</span>
    </td>
    <td class="py-6 align-middle">
        <span class="text-purple-heart font-medium text-base">
            ${{ repository.issues_donations_sum_amount ?? 0 }}
        </span>
    </td>
    <td class="py-6 pr-6 align-middle">
        <Icon name="star" width="1.375rem" :class="getIconStrokeColor(repository.favorite)"
            @click="addFavorites(repository)" />
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

const props = defineProps({
    repository: {
        type: Object,
        required: true,
    },
})

const isDark = useDark()


const getIconStrokeColor = (isFavorite) => {
    if (isDark.value) {
        return isFavorite ? 'dark:stroke-green dark:fill-green' : 'dark:stroke-spun-pearl dark:hover:stroke-green';
    }
    return isFavorite ? 'stroke-ocean-green fill-ocean-green' : 'stroke-tundora hover:stroke-ocean-green';
}

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
            repository.favorite = !repository.favorite;
        })
        .catch(error => {
            toast.error('Something went wrong!')
            console.error(error);
        });
}
</script>