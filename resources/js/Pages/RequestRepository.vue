<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Request repository
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white flex justify-center dark:text-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white">Find missing repository</h5>
                        </div>
                        <SelectRepository @selected="selectionPicked" />
                        <div v-if="selectedRepository" class="mt-2">
                            <div class="text-gray-700 dark:text-white">
                                <ul>
                                    <li>{{ selectedRepository.id }}</li>
                                    <li>{{ selectedRepository.full_name }}</li>
                                    <li>{{ selectedRepository.description }}</li>
                                    <li>{{ selectedRepository.open_issues_count }} opened issues</li>
                                    <li>#{{ selectedRepository.topics.join(' #')}}</li>
                                </ul>
                            </div>
                            <button class="bg-green-400 text-white p-3" @click="createRepository" :disabled="!selectedRepository">Add repository to Open Pledge</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import SelectRepository from '@/Components/SelectRepository.vue'
import { useToast } from "vue-toastification";

export default {
  data() {
    return {
      selectedRepository: null,
    };
  },
  components: {AppLayout, SelectRepository},
  methods: {
    selectionPicked(repo){
        this.selectedRepository = repo
    },
    createRepository() {
        axios.post('/repositories/create', {
            title: this.selectedRepository.full_name,
            github_id: this.selectedRepository.id,
            github_url: this.selectedRepository.html_url,
        })
        .then((response) => {
            const toast = useToast()
            toast.success('Repository added to OpenPledge!')
        }).catch((err) => {
            const toast = useToast()
            toast.error(err.response.data.message)
        }).finally(() => {
            this.loading = false
        });
    }
  }
}

</script>