<template>
    <AppLayout :title="title">
        <Page :title="title" class="pb-10 px-48" :description="description">
            <div v-if="isFavoritesPage" class="flex gap-2">
                <Pill :color="showIssues ? 'secondary' : 'primary'" @click="showIssues = true">
                    Issues
                </Pill>
                <Pill :color="showIssues ? 'primary' : 'secondary'" @click="showIssues = false">
                    Repositories
                </Pill>
            </div>
            <IssuesTable v-if="showIssues && issues.length > 0" :issues="issues" @onLazyLoading="fetchData(true)"
                :pledged="true" class="hidden md:table" />
            <RepositoriesTable v-if="!showIssues && repositories.length > 0" :repositories="repositories"
                @onLazyLoading="fetchData(true)" class="hidden md:table" />
            <TableRowSkeleton v-if="loading" class="mt-2" />
        </Page>
    </AppLayout>
</template>

<script setup>
import axios from 'axios'
import { ref, watch } from 'vue'
import Page from '@/Components/Page.vue'
import Pill from '@/Components/Form/Pill.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import IssuesTable from '@/Components/Custom/IssuesTable.vue'
import TableRowSkeleton from '@/Components/Custom/TableRowSkeleton.vue'
import RepositoriesTable from '@/Components/Custom/RepositoriesTable.vue'

const props = defineProps({
    issues: Object,
    repositories: Object,
    noIssuesMessage: {
        type: String,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        required: true,
    },
    routeName: {
        type: String,
        required: true,
    },
    isFavoritesPage: {
        type: Boolean,
        default: false
    },
})

const loading = ref(false)
const currentIssuesPage = ref(1)
const lastIssuesPage = ref(1)
const currentRepositoriesPage = ref(1)
const lastRepositoriesPage = ref(1)
const issues = ref(props.issues || [])
const repositories = ref(props.repositories || [])
const showIssues = ref(true)

watch(showIssues, () => fetchData(false))

const fetchData = async (isLazyLoading) => {
    const isIssues = showIssues.value
    const currentPage = isIssues ? currentIssuesPage : currentRepositoriesPage
    const lastPage = isIssues ? lastIssuesPage : lastRepositoriesPage
    const dataList = isIssues ? issues : repositories

    // Prevent unnecessary fetches
    if (!isLazyLoading && dataList.value.length > 0) return
    if (loading.value || currentPage.value > lastPage.value) return

    // Increment page for lazy loading
    if (isLazyLoading) currentPage.value++

    loading.value = true

    try {
        const response = await axios.get(route(props.routeName), {
            params: {
                page: currentPage.value,
                showIssues: isIssues,
            },
        })

        if (isIssues) {
            issues.value = [...issues.value, ...response.data.issues]
            lastIssuesPage.value = response.data.last_page
        } else {
            repositories.value = [...repositories.value, ...response.data.repositories]
            lastRepositoriesPage.value = response.data.last_page
        }
    } catch (error) {
        console.error('Failed to fetch data:', error)
    } finally {
        loading.value = false
    }
}
</script>
