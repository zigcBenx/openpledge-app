<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
    user: Object,
});

const form = useForm({
    _method: 'POST',
    companyId: props.user.company.id,
    companyName: props.user.company.name,
    companyAddress: props.user.company.address,
    companyVatId: props.user.company.vat_id,
    shouldBillCompany: props.user.company.should_bill_company,
});

const updateCompanyInformation = () => {
    form.post(route('profile.settings.company'), {
        errorBag: 'updateCompanyInformation',
        preserveScroll: true,
    });
};

const hasCompanyName = computed(() => {
  const name = form.companyName;
  return !!name && name.toString().trim() !== '';
});

const hasCompanyAddress = computed(() => {
    const address = form.companyAddress;
    return !!address && address.toString().trim() !== '';
});

const hasCompanyVatId = computed(() => {
    const vatId = form.companyVatId;
    return !!vatId && vatId.toString().trim() !== '';
});

const isCompanyFormIncomplete = computed(() => {
    if (!hasCompanyName.value) {
        return true;
    }

    if (hasCompanyName.value && (!hasCompanyAddress.value || !hasCompanyVatId.value)) {
        return true;
    }

    return false;
});
</script>

<template>
    <FormSection @submitted="updateCompanyInformation">
        <template #title>
            Company Information
        </template>

        <template #description>
            Update your company's information.
        </template>

        <template #form>
            <!-- Company Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="companyName" value="Company Name" />
                <TextInput
                    id="companyName"
                    v-model="form.companyName"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autocomplete="company"
                />
                <InputError :message="form.errors.companyName" class="mt-2" />
            </div>

             <!-- Company Address -->
            <div class="col-span-6 sm:col-span-4" v-if="hasCompanyName">
                <InputLabel for="companyAddress" value="Company Address" />
                <TextInput
                    id="companyAddress"
                    v-model="form.companyAddress"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autocomplete="companyAddress"
                />
                <InputError :message="form.errors.companyAddress" class="mt-2" />
            </div>

            <!-- Company VAT ID -->
            <div class="col-span-6 sm:col-span-4" v-if="hasCompanyName">
                <InputLabel for="companyVatId" value="Company VAT ID" />
                <TextInput
                    id="companyVatId"
                    v-model="form.companyVatId"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autocomplete="companyVatId"
                />
                <InputError :message="form.errors.companyVatId" class="mt-2" />
            </div>

            <!-- Should Bill Company -->
            <div class="col-span-6 sm:col-span-4 flex items-center gap-2" v-if="hasCompanyName">
                <Checkbox
                    id="shouldBillCompany"
                    v-model:checked="form.shouldBillCompany"
                />
                <InputLabel for="shouldBillCompany" value="Bill my company" />
                <InputError :message="form.errors.shouldBillCompany" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="me-3">
                Saved.
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing || isCompanyFormIncomplete }" :disabled="form.processing || isCompanyFormIncomplete">
                Save
            </PrimaryButton>
        </template>
    </FormSection>
</template>
