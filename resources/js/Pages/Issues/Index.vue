<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import ListIssues from '@/Components/Custom/ListIssues.vue';
import Pill from '@/Components/Form/Pill.vue';
import { ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import RangeSlider from '@/Components/Form/RangeSlider.vue';
import Row from '@/Components/Grid/Row.vue';
import Col from '@/Components/Grid/Col.vue';

defineProps({
    issues: Array,
});

const labels = ref([{"label":"Test", "value": "test"},{"label":"Feature", "value": "feature"},{"label":"Bug", "value": "bug"}]);
const filters = ref({labels: []});

const handleSelectOption = (value) => {
  if (filters.value.labels.includes(value)) {
    filters.value.labels = filters.value.labels.filter(label => label !== value);
  } else {
    filters.value.labels.push(value);
  }
}

const handleRemoveOption = (value) => {
    const indexToRemove = labels.value.findIndex(labelObj => labelObj.value === value);
    if (indexToRemove !== -1) {
        labels.value.splice(indexToRemove, 1);
    }
}

const handleRangeChange = (value) => {
    console.log(value);
}


</script>

<template>
    <AppLayout title="Issues">
        <template #header>
            <!-- <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <Link :href="route('home')" :active="route().current('home')">Issues</Link> / Repositories
            </h2> -->
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <Col>
            <div class="pl-2 pb-3">
                <InputLabel>Label</InputLabel>
            </div>
        </Col>
        <Row class="mb-4">
            <Col>
                <div 
                    v-for="selectedValue in labels"
                    class="pl-2 float-left flex"    
                >
                    <Pill 
                        :key="selectedValue.value"
                        :color="filters.labels.includes(selectedValue.value) ? 'secondary' : 'primary'"
                        :dismissable="true"
                        @select="() => handleSelectOption(selectedValue.value)"
                        @dismiss="() => handleRemoveOption(selectedValue.value)"
                    >
                        {{ selectedValue.label }}
                    </Pill>
                </div>
            </Col>
        </Row>

            <div class="w-1/3">
                <RangeSlider
                    :min="1"
                    :max="100"
                    :start="20"
                    :end="35"
                    @input="handleRangeChange"
                    />
            </div>
                <div class="bg-white flex justify-center dark:text-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
                        <div class="flow-root">
                            <list-issues v-if="issues" :issues="issues" :pledged="true" title="Pledged issues" />
                            <div class="flex flex-col w-full items-center">
                                <p>Can't find your favourite repository?</p>
                                <Link class="underline" :href="route('repositories-request-get')">
                                    Request repository
                                </Link>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>
