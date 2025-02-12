<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ invoice: Object });

const form = ref({
    number: props.invoice.number,
    amount: props.invoice.amount
});

const submit = () => {
    router.put(route('invoices.update', props.invoice.id), form.value);
};
</script>

<template>
    <AppLayout title="Invoices">
        <div class="p-6 bg-white dark:bg-oil text-gray-900 dark:text-gray-100">
            <h1 class="text-2xl font-bold">Edit Invoice</h1>
            <form @submit.prevent="submit" class="mt-4">
                <div>
                    <label class="block">Invoice Number</label>
                    <input v-model="form.number" type="text" class="border p-2 w-full dark:bg-gray-800 dark:text-white">
                </div>
                <div>
                    <label class="block">Amount</label>
                    <input v-model="form.amount" type="number" class="border p-2 w-full dark:bg-gray-800 dark:text-white">
                </div>
                <button type="submit" class="bg-blue-500 text-white p-2 rounded mt-2">Update</button>
            </form>
        </div>
    </AppLayout>
</template>
