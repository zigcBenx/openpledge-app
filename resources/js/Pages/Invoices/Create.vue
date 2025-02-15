<template>
    <AppLayout title="Invoices">
        <Link :href="route('invoices.index')" class="dark:text-white text-black"><i class="fa fa-arrow-left" /> Back to invoices</Link>
        <form @submit.prevent="submit" class="dark:bg-oil bg-white dark:text-white text-black p-6 rounded-lg">
            <div class="mb-4">
                <label class="block mb-1">Customer</label>
                <textarea v-model="form.customer.name" type="text" class="w-full p-2 dark:bg-oil bg-white border border-gray-600 rounded">
                </textarea>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block mb-1">Invoice Date</label>
                    <input v-model="form.invoice.invoice_date" type="date" class="w-full p-2 dark:bg-oil bg-white border border-gray-600 rounded">
                </div>
                <div>
                    <label class="block mb-1">Payment Date</label>
                    <input v-model="form.invoice.payment_date" type="date" class="w-full p-2 dark:bg-oil bg-white border border-gray-600 rounded">
                </div>
                <div>
                    <label class="block mb-1">Service Date</label>
                    <input v-model="form.invoice.service_date" type="date" class="w-full p-2 dark:bg-oil bg-white border border-gray-600 rounded">
                </div>
            </div>

            <div class="mb-4">
                <label class="block mb-1">Payment Method</label>
                <input v-model="form.invoice.payment_method" type="text" class="w-full p-2 dark:bg-oil bg-white border border-gray-600 rounded">
            </div>

            <div class="mb-4">
                <label class="block mb-1">VAT</label>
                <input v-model="form.invoice.vat" type="number" class="w-full p-2 dark:bg-oil bg-white border border-gray-600 rounded">
            </div>

            <div class="mb-4">
                <label class="block mb-2">Items</label>
                <div v-for="(item, index) in form.items" :key="index" class="mb-2 flex space-x-2">
                    <input v-model="item.name" placeholder="Item Name" class="p-2 dark:bg-oil bg-white border border-gray-600 rounded w-1/4">
                    <input v-model="item.price_per_unit" type="number" placeholder="Price" class="p-2 dark:bg-oil bg-white border border-gray-600 rounded w-1/6">
                    <input v-model="item.quantity" type="number" min="1" placeholder="Qty" class="p-2 dark:bg-oil bg-white border border-gray-600 rounded w-1/6">
                    <input v-model="item.currency" type="text" class="p-2 dark:bg-oil bg-white border border-gray-600 rounded w-1/6">
                    <button type="button" @click="removeItem(index)" class="bg-red-600 p-2 rounded">&times;</button>
                </div>
                <button type="button" @click="addItem" class="bg-blue-600 p-2 rounded mt-2">+ Add Item</button>
            </div>

            <button type="submit" class="dark:bg-turquoise bg-dark-green p-3 rounded w-full">Create Invoice</button>
        </form>
    </AppLayout>
</template>

<script setup>
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { addDays, endOfMonth, format, subMonths } from 'date-fns';
import { Link } from '@inertiajs/vue3';

const props = defineProps({ invoice: Object });

const form = useForm({
    customer: {
        name: props.invoice?.customer || '',
    },
    invoice: {
        invoice_date: props.invoice?.invoice_date || '',
        payment_date: props.invoice?.payment_date || '',
        service_date: props.invoice?.service_date || '',
        payment_method: props.invoice?.payment_method || '',
        vat: props.invoice?.vat || '',
    },
    items: props.invoice?.items ? JSON.parse(props.invoice.items) : [{
        name: '',
        price_per_unit: '',
        quantity: 1,
        currency: '€',
    }],
});

const addItem = () => {
    form.items.push({ name: '', price_per_unit: '', quantity: 1, currency: '€'});
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const submit = () => {
    form.post(route('invoices.store'));
};

watch(() => form.invoice.invoice_date, (newDate) => {
  if (newDate) {
    // Apply +10 days to payment_date if it's null
    if (!form.invoice.payment_date) {
      const invoiceDate = new Date(newDate)
      form.invoice.payment_date = format(addDays(invoiceDate, 10), 'yyyy-MM-dd')
    }

    // Apply last day of previous month to service_date if it's null
    if (!form.invoice.service_date) {
      const invoiceDate = new Date(newDate)
      const prevMonthLastDay = endOfMonth(subMonths(invoiceDate, 1))
      form.invoice.service_date = format(prevMonthLastDay, 'yyyy-MM-dd')
    }
  }
})
</script>
