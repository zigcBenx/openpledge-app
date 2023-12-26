<template>
    <AppLayout title="Repository Details">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Repository Details
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white flex justify-center dark:text-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white">{{ repository.title }}</h5>
                        </div>
                        <div class="text-gray-700 dark:text-white">
                            <ul>
                                <li>ID: {{ repository.id }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
                <div class="bg-white flex justify-center dark:text-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
                        <list-issues v-if="repository.issues" :issues="repository.issues" title="Pledged issues" />
                        <br>
                        <hr>
                        <list-issues v-if="openIssues" :issues="openIssues" title="Open issues">
                            <template #actions="{ issue }">
                                <button @click="initialPledge(issue)">Add</button>
                            </template>
                        </list-issues>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import ListIssues from '@/Components/Custom/ListIssues.vue';
import { useToast } from "vue-toastification";


export default {
    props: {
        repository: Object,
    },
    components: { AppLayout, ListIssues },

    data() {
        return {
        openIssues: [],
        };
    },
    mounted() {
        this.getIssues()
    },
    methods: {
        getIssues() {
            axios.get('/github/issues', {
                params: {
                    q: `repo:${this.repository.title}`,
                },
            })
            .then(response => {
                this.openIssues = response.data.items.filter(issue => !this.repository.issues.map((issue) => Number(issue.github_id)).includes(issue.id));
            })
            .catch(error => {
                console.log(error);
            });
        },
        initialPledge(issue) {
            axios.post('/issues', {
                title: issue.title,
                github_id: issue.id,
                github_url: issue.html_url,
                user_avatar: issue.user.avatar_url,
                repository_id: this.repository.id,
            })
            .then((response) => {
                const toast = useToast()
                toast.success('Issue added to OpenPledge!')
                this.getIssues()
            }).catch((err) => {
                const toast = useToast()
                toast.error(err.response.data.message)
            }).finally(() => {
                // this.loading = false
            });
        }
    }
}
</script>