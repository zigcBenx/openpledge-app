<template>
    <DialogModal :show="displayFilterModal" :colors="colors" @close="displayHandler(false)" :closeable="true">
        <template #title>
            <span>Filters</span>
        </template>
        <template #content>
            <Row>
                <Col class="">Label</Col>
                <Col class="flex flex-wrap gap-2">
                    <div 
                        v-for="selectedValue in labels"
                        class=""    
                    >
                        <Pill 
                            :colors="colors"
                            :key="selectedValue.value"
                            :color="filters.labels.includes(selectedValue.value) ? 'secondary' : 'primary'"
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
                            :colors="colors"
                            :key="selectedValue.value"
                            :color="filters.languages.includes(selectedValue.value) ? 'secondary' : 'primary'"
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
                    />
                </Col>
            </Row>
        </template>
        <template #footer>
            <Row class="pt-8">
                <Col :span="6">
                    <Button @click="displayHandler(false)" color="secondary">
                        Clear
                    </Button>
                </Col>
                <Col :span="6">
                    <Button color="primary">
                        Apply Filters
                    </Button>
                </Col>
            </Row>
        </template>
    </DialogModal>
</template>
<script>
    import Pill from '@/Components/Form/Pill.vue';
    import { ref } from 'vue';
    import RangeSlider from '@/Components/Form/RangeSlider.vue';
    import Row from '@/Components/Grid/Row.vue';
    import Col from '@/Components/Grid/Col.vue';
    import DialogModal from '@/Components/DialogModal.vue';
    import Button from '@/Components/Button.vue';

    export default {
        components: {
            DialogModal,
            Button,
            Col,
            Row,
            RangeSlider,
            Pill
        },
        emits: ["display"],
        props: {
            displayFilterModal: {
                type: Boolean,
                default: false
            },
            colors: {
                type: Object,
                required: false
            }
        },
        setup(props, {emit}) {
            const labels = ref([
                {"label":"Test", "value": "test"},
                {"label":"Feature", "value": "feature"},
                {"label":"Bug", "value": "bug"},
                {"label":"Enhancement", "value": "enhancement"},
                {"label":"Documentation", "value": "documentation"},
                {"label":"Question", "value": "question"},
                {"label":"Invalid", "value": "invalid"},
                {"label":"Duplicate", "value": "duplicate"},
                {"label":"Security", "value": "security"}
            ]);

            const languages = ref([
                {"label":"Python", "value": "python"},
                {"label":"TypeScript", "value": "typeScript"},
                {"label":"PHP", "value": "php"},
                {"label":"Ruby", "value": "ruby"},
                {"label":"Swift", "value": "swift"},
                {"label":"Java", "value": "java"},
                {"label":"Scala", "value": "scala"}
            ]);

            const filters = ref({labels: [],languages: []});

            const handleSelectOption = (value) => {
            if (filters.value.labels.includes(value)) {
                filters.value.labels = filters.value.labels.filter(label => label !== value);
            }else if (labels.value.indexOf(value)) {
                filters.value.labels.push(value);
            }

            if (filters.value.languages.includes(value)) {
                filters.value.languages = filters.value.languages.filter(language => language !== value);
            }else if (languages.value.indexOf(value)) {
                filters.value.languages.push(value);
            }
            }

            const handleRemoveOption = (value) => {
                const indexToRemove = labels.value.findIndex(labelObj => labelObj.value === value);
                if (indexToRemove !== -1) {
                    filters.value.labels.splice(indexToRemove, 1);
                }
                const indexLangToRemove = languages.value.findIndex(lang => lang.value === value);
                if (indexLangToRemove !== -1) {
                    filters.value.languages.splice(indexLangToRemove, 1);
                }
            }

            const handleRangeChange = (value) => {
                console.log(value);
            }

            const displayHandler = (value) => {
                emit("display", value);
            };

            return {
                handleRangeChange,
                handleRangeChange,
                handleSelectOption,
                filters,
                languages,
                labels,
                displayHandler,
                handleRemoveOption
            }
        }
    }
</script>