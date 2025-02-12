<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({ invoices: Object });

// Modal state
const showModal = ref(false);
const pdfUrl = ref(null);

const deleteInvoice = (id) => {
    if (confirm('Are you sure?')) {
        router.delete(route('invoices.destroy', id));
    }
};

const generatePdf = (id) => {
    router.post(route('invoices.generatePdf', id));
};

// Open modal to view PDF
const openPdfModal = (pdfPath) => {
    pdfUrl.value = pdfPath;
    showModal.value = true;
};

// Close the modal
const closeModal = () => {
    showModal.value = false;
    pdfUrl.value = null;
};
</script>

<template>
    <AppLayout title="Invoices">
        <div class="p-6 bg-white dark:bg-oil text-gray-900 dark:text-gray-100">
            <h1 class="text-2xl font-bold">Invoices</h1>
            <button @click="router.visit(route('invoices.create'))" class="bg-blue-500 text-white p-2 rounded">New Invoice</button>
            <table class="w-full mt-4 border dark:border-gray-700">
                <thead>
                    <tr class="bg-gray-200 dark:bg-gray-800">
                        <th class="border p-2">Number</th>
                        <th class="border p-2">Amount</th>
                        <th class="border p-2">Created At</th>
                        <th class="border p-2">PDF</th>
                        <th class="border p-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="invoice in invoices.data" :key="invoice.id">
                        <td class="border p-2">{{ invoice.number }}</td>
                        <td class="border p-2">${{ invoice.total }}</td>
                        <td class="border p-2">{{ new Date(invoice.created_at).toLocaleDateString() }}</td>
                        <td class="border p-2">
                            <a v-if="invoice.pdf_path" @click.prevent="openPdfModal(invoice.pdf_path)" class="bg-green-500 text-white p-1 rounded cursor-pointer">View PDF</a>
                        </td>
                        <td class="border p-2">
                            <template v-if="invoice.donation_id === null">
                                <button @click="router.visit(route('invoices.edit', invoice.id))" class="bg-yellow-500 text-white p-1 rounded">Edit</button>
                                <button @click="deleteInvoice(invoice.id)" class="bg-red-500 text-white p-1 ml-2 rounded">Delete</button>
                            </template>
                            <template v-else>
                                /
                            </template>
                        </td>
                    </tr>
                </tbody>
            </table>

            <Pagination :links="invoices.links" />

            <!-- Modal for PDF -->
            <div v-if="showModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white p-6 rounded-lg w-full max-w-4xl">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold">Invoice PDF</h2>
                        <button @click="closeModal" class="text-gray-500 hover:text-gray-700">&times;</button>
                    </div>
                    <div class="mt-4">
                        <iframe :src="pdfUrl" class="w-full h-96" frameborder="0"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
