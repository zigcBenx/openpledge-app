<template>
    <DialogModal :show="displayFilterModal" @close="displayHandler(false)" :closeable="true">
        <template #title>
            <span>Filters</span>
        </template>
        <template #content>
            <Row>
                <Col class="">Label</Col>
                <Col class="flex flex-wrap gap-2">
                    <div 
                        v-for="selectedValue in labels"
                    >
                        <Pill 
                            :key="selectedValue.value"
                            :color="filters.labels?.find(item => item == selectedValue.value) ? 'secondary' : 'primary'"
                            @select="() => handleSelectOption(selectedValue.value)"
                            @dismiss="() => handleRemoveOption(selectedValue.value)"
                        >
                            {{ selectedValue.label }}
                        </Pill>
                    </div>
                </Col>
                <Col>Language</Col>
                <Col class="flex flex-wrap gap-2">
                    <div 
                        v-for="selectedValue in languages"
                        class="float-left flex"
                    >
                        <Pill
                            :key="selectedValue.value"
                            :color="filters.languages?.find(item => item == selectedValue.value) ? 'secondary' : 'primary'"
                            @select="() => handleSelectOption(selectedValue.value)"
                            @dismiss="() => handleRemoveOption(selectedValue.value)"
                        >
                            {{ selectedValue.label }}
                        </Pill>
                    </div>
                </Col>
                <Col>Pledge Range</Col>
                <Col class="mt-2">
                    <RangeSlider
                        @input="handleRangeChange"
                        :start="queryFilters.start && queryFilters.start !== 'null' ? parseInt(queryFilters.start) : 0"
                        :end="queryFilters.end && queryFilters.end !== 'null' ? parseInt(queryFilters.end) : 1000"
                    />
                </Col>
                <Col>
                    <VueDatePicker v-model="month" month-picker :clearable="true"/>
                </Col>
            </Row>
        </template>
        <template #footer>
            <Row class="pt-8">
                <Col :span="6">
                    <Button @click="clearFilters" color="outline">
                        Clear
                    </Button>
                </Col>
                <Col :span="6">
                    <Button color="primary" @click="submit">
                        Apply Filters
                    </Button>
                </Col>
            </Row>
        </template>
    </DialogModal>
</template>
<script>
    import Pill from '@/Components/Form/Pill.vue';
    import { ref, onMounted, watch } from 'vue';
    import RangeSlider from '@/Components/Form/RangeSlider.vue';
    import Row from '@/Components/Grid/Row.vue';
    import Col from '@/Components/Grid/Col.vue';
    import DialogModal from '@/Components/DialogModal.vue';
    import Button from '@/Components/Button.vue';
    import VueDatePicker from '@vuepic/vue-datepicker';

    export default {
        components: {
            DialogModal,
            Button,
            Col,
            Row,
            RangeSlider,
            Pill,
            VueDatePicker
        },
        emits: ["display", "submit"],
        props: {
            displayFilterModal: {
                type: Boolean,
                default: false
            },
            labels: {
                type: Object,
                default: []
            },
            languages: {
                type: Object,
                default: []
            },
            queryFilters: {
                type: Object,
                default: []
            },
            removedFilters: {
                type: Number,
                default: 0
            }
        },
        setup(props, {emit}) {
            let initialFilter = {labels: [],languages: [], start: null, end: null, date:null}
            const filters = ref(initialFilter);
            const storageDiscoverKey = 'discover';
            const handleSelectOption = (value) => {
                if (filters.value.labels?.find(item => item === value)) {
                    filters.value.labels = filters.value.labels.filter(label => label !== value);
                } else if (props.labels?.find(item => item.value === value)) {
                    filters.value.labels.push(value);
                }

                if (filters.value.languages?.find(item => item === value)) {
                    filters.value.languages = filters.value.languages?.filter(language => language !== value);
                }else if (props.languages?.find(lang => lang.value === value)) {
                    filters.value.languages.push(value);
                }
            }

            onMounted(() => {
                if(Object.keys(props.queryFilters).length) {
                    filters.value = props.queryFilters;
                } else {
                    if(localStorage.getItem(storageDiscoverKey)) {
                        filters.value = JSON.parse(localStorage.getItem(storageDiscoverKey));
                        emit("submit", filters.value);
                    }
                }
            });

            const handleRemoveOption = (value) => {
                const indexToRemove = props.labels.findIndex(labelObj => labelObj.value === value);
                if (indexToRemove !== -1) {
                    filters.value.labels.splice(indexToRemove, 1);
                }
                const indexLangToRemove = props.languages.findIndex(lang => lang.value === value);
                if (indexLangToRemove !== -1) {
                    filters.value.languages.splice(indexLangToRemove, 1);
                }
            }

            const handleRangeChange = (value) => {
                filters.value.start = value.start;
                filters.value.end = value.end;
            }

            const displayHandler = (value) => {
                emit("display", value);
            };

            const submit = () => {
                emit("submit", filters.value);
                localStorage.setItem(storageDiscoverKey, JSON.stringify(filters.value));
                displayHandler(false);
            }
            const clearFilters = () => {
                filters.value = initialFilter;
                emit("submit", initialFilter);
                localStorage.setItem(storageDiscoverKey, JSON.stringify(initialFilter));
            }

            const month = ref({
                month: new Date().getMonth(),
                year: new Date().getFullYear()
            });

            const updateFiltersFromQuery = () => {
                const { labels, languages, start, end, date } = props.queryFilters;
                filters.value.labels = [...labels];
                filters.value.languages = [...languages];
                filters.value.start = start || null;
                filters.value.end = end || null;
                filters.value.date = date || null;
                localStorage.setItem(storageDiscoverKey, JSON.stringify(filters.value));
            };

            watch(() => props.removedFilters, updateFiltersFromQuery, { deep: true });

            return {
                handleRangeChange,
                handleSelectOption,
                filters,
                displayHandler,
                handleRemoveOption,
                submit,
                clearFilters,
                month
            }
        }
    }
</script>