<script setup>
import { computed, ref, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import CountrySelect from '@/Components/Custom/CountrySelect.vue';

const user = usePage().props.auth.user;

const form = useForm({
    _method: 'POST',
    companyId: user.company?.id,
    companyName: user.company?.name,
    companyAddress: user.company?.address,
    companyPostalCode: user.company?.postal_code,
    companyCity: user.company?.city,
    companyState: user.company?.state,
    companyCountry: user.company?.country,
    companyVatId: user.company?.vat_id,
});

const updateCompanyInformation = () => {
    form.post(route('profile.settings.company'), {
        errorBag: 'updateCompanyInformation',
        preserveScroll: true,
    });
};

const hasExistingCompany = computed(() => !!user.company?.id);
const hasCompany = ref(hasExistingCompany.value);

const isNonEmpty = (field) =>
    computed(() => !!form[field] && form[field].toString().trim() !== '');

const requiredFields = [
    'companyName',
    'companyAddress',
    'companyPostalCode',
    'companyCity',
    'companyState',
    'companyCountry',
    'companyVatId',
];

const fieldValidators = Object.fromEntries(
    requiredFields.map((field) => [field, isNonEmpty(field)])
);

const isCompanyFormIncomplete = computed(() =>
    requiredFields.some((field) => !fieldValidators[field].value)
);
</script>

<template>
    <FormSection @submitted="updateCompanyInformation">
        <template #title>
            Company Information
        </template>

        <template #description>
            {{
                hasExistingCompany
                    ? "Update your company's information."
                    : "Create a company to get started."
            }}
        </template>

        <template #form v-if="hasCompany">
            <div v-for="field in [
                { id: 'companyName', label: 'Company Name', type: 'text' },
                { id: 'companyAddress', label: 'Company Address', type: 'text' },
                { id: 'companyPostalCode', label: 'Company Postal Code', type: 'text' },
                { id: 'companyCity', label: 'Company City', type: 'text' },
                { id: 'companyState', label: 'Company State', type: 'text' }
            ]" :key="field.id" class="col-span-6 sm:col-span-4">
                <InputLabel :for="field.id" :value="field.label" />
                <TextInput :id="field.id" v-model="form[field.id]" :type="field.type" class="mt-1 block w-full" required
                    :autocomplete="field.id" />
                <InputError :message="form.errors[field.id]" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="companyCountry" value="Company Country" />
                <CountrySelect id="companyCountry" v-model="form.companyCountry"
                    class="mt-1 dark:text-white dark:bg-oil bg-white dark:placeholder-spun-pearl dark:border-mondo focus:border-spun-pearl border-gray-300" />
                <InputError :message="form.errors.companyCountry" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="companyVatId" value="Company VAT ID" />
                <TextInput id="companyVatId" v-model="form.companyVatId" type="text" class="mt-1 block w-full" required
                    autocomplete="companyVatId" />
                <InputError :message="form.errors.companyVatId" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="me-3">
                Saved.
            </ActionMessage>
            <PrimaryButton v-if="!hasCompany" type="button" @click="hasCompany = true">
                Create Company
            </PrimaryButton>
            <PrimaryButton v-else :class="{ 'opacity-25': form.processing || isCompanyFormIncomplete }"
                :disabled="form.processing || isCompanyFormIncomplete">
                Save
            </PrimaryButton>
        </template>
    </FormSection>
</template>
