<template>
    <AppLayout title="Campaign create">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Create Campaign
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white flex justify-center dark:text-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
                        <div class="flex">
                            <div class="flex items-center justify-between mb-4">
                                <form @submit.prevent="submitForm">                                    
                                    <div class="mb-4">
                                        <div class="relative">
                                            <input v-model="form.name" type="text" id="name" class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " />
                                            <label for="name" class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-900 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">Name</label>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <div class="relative">
                                            <input v-model="form.template" type="text" id="template" class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " />
                                            <label for="template" class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-900 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">Template</label>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="inline-flex items-center mb-5 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                v-model="form.is_enabled"
                                                class="sr-only peer"
                                            />
                                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Is enabled</span>
                                        </label>
                                    </div>
                                    <div class="mb-4">
                                        <label class="inline-flex items-center mb-5 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                v-model="form.is_recurring_for_new_users"
                                                class="sr-only peer"
                                            />
                                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Is recurring for new users</span>
                                        </label>
                                    </div>
                                    <div class="mb-4">
                                        <div class="relative">
                                            <input v-model="form.new_user_delay_days" type="text" id="new_user_delay_days" class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " />
                                            <label for="new_user_delay_days" class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-900 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">New users delay days</label>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <input v-model="form.start_time" type="date" placeholder="Campaign start date" class="text-black">
                                    </div>

                                    <div class="mt-4">
                                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">
                                            Create Campaign
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useToast } from "vue-toastification";
import { router } from '@inertiajs/vue3';

export default {
    components: { AppLayout },
    data() {
        return {
            form: {}
        };
    },
    methods: {
        submitForm() {
            const toast = useToast()
            // Implement the logic to update the campaign
            axios.post(`/campaigns/`, this.form)
                .then(() => {
                    toast.success('Campaign created!')
                    router.visit(route('campaigns.index'))
                })
                .catch((error) => {
                    console.log(error)
                    toast.error('Something went wrong!')
                });
        },
    },
}
</script>