<template>
    <AppLayout title="Issues">
        <template #header>
        </template>
            <Row>
                <Col :span="8">
                    <Page title="Issues" description="Search issues you are interested in...">
                        <template #actions>
                            <button @click="displayFilterModal = true" class="w-[107px] justify-center hover:bg-mint-green text-dark-green flex dark:text-green text-dark-green p-1.5 dark:hover:bg-tropical-rain-forest dark:hover:text-green rounded-full py-3 px-3.5">
                                Filters 
                                <Icon class="pl-1" name="vertical" :fill="isDark ? theme.colors?.green : theme.colors['dark-green']" />
                            </button>
                        </template>
                        <template #filters>
                            <div 
                                v-for="selectedValue in labels"    
                            >
                                <Pill 
                                    :key="selectedValue.value"
                                    color="secondary"
                                    :colors="theme.colors"
                                    :contentClasses="['px-2', 'py-1']"
                                    :dismissable="true"
                                    @select="() => handleSelectOption(selectedValue.value)"
                                    @dismiss="() => handleRemoveOption(selectedValue.value)"
                                >
                                    {{ selectedValue.label }}
                                </Pill>
                            </div>
                        </template>
                        <template v-slot="">
                            <IssuesTable :issues="issues" />
                        </template>
                    </Page>
                </Col>
                <Col :span="4">
                    
                </Col>
            </Row>

            <Filters :displayFilterModal="displayFilterModal" :colors="theme.colors" @display="() => handleDisplayModal()" />
            <div class="py-8">
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
    </AppLayout>
</template>
<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import ListIssues from '@/Components/Custom/ListIssues.vue';
import Pill from '@/Components/Form/Pill.vue';
import { ref } from 'vue';
import Row from '@/Components/Grid/Row.vue';
import Col from '@/Components/Grid/Col.vue';
import Icon from '@/Components/Icon.vue';
import { useDark } from '@vueuse/core';
import Filters from './Filters.vue';
import Page from '@/Components/Page.vue';
import IssuesTable from './Partials/IssuesTable.vue'
import resolveConfig from 'tailwindcss/resolveConfig';
import tailwindConfig from '/tailwind.config.js';

export default {
    components: {
        Page,
        Filters,
        Icon,
        Row,
        Col,
        Pill,
        ListIssues,
        AppLayout,
        Link,
        IssuesTable
    },
    props: {
        issues: {
            type: Array,
            default: [],
        },
        labels: {
            type: Array,
            default: [],
        },
        languages: {
            type: Array,
            default: [],
        },
    },
    setup() {
        const { theme } = resolveConfig(tailwindConfig);
        const isDark = useDark();
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
        const issues = [{
            id: 1,
            state: 'open',
            title: 'This Is The Issue Title',
            user: {
                username: 'test',
                user_avatar: '/images/avatar.png'

            },
            created_at: 'Wed Apr 17 2024',
            labels: ['bug', 'feature'],
            repository: 'strapi/strapi',
            languages: ['Javascript', 'Java', 'Python', 'Ruby', 'Go'],
            donations: '$300',
            favorite: false
        }, {
            id: 2,
            state: 'open',
            title: 'Favorite issue',
            user: {
                username: 'test',
                user_avatar: '/images/avatar.png'

            },
            created_at: 'Wed Apr 17 2024',
            labels: ['bug', 'feature'],
            repository: 'strapi/strapi',
            languages: ['Javascript', 'Java', 'Python', 'Ruby', 'Go'],
            donations: '$300',
            favorite: true
        }, {
            id: 3,
            state: 'closed',
            title: 'Issue title 2',
            user: {
                username: 'test',
                user_avatar: '/images/avatar.png'

            },
            created_at: 'Wed Apr 10 2024',
            labels: ['bug', 'feature'],
            repository: 'strapi/strapi',
            languages: ['Javascript', 'Java', 'Python', 'Ruby', 'Go'],
            donations: '$400',
            favorite: true
        }]
        const filters = ref({labels: [],languages: []});
        const displayFilterModal = ref(false);

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
                labels.value.splice(indexToRemove, 1);
            }
            const indexLangToRemove = languages.value.findIndex(lang => lang.value === value);
            if (indexLangToRemove !== -1) {
                filters.value.languages.splice(indexLangToRemove, 1);
            }
        }

        const handleDisplayModal = () => {
            displayFilterModal.value = !displayFilterModal.value;
        }

        return {
            labels,
            languages,
            issues,
            isDark,
            filters,
            displayFilterModal,
            handleDisplayModal,
            handleRemoveOption,
            handleSelectOption,
            theme
        }
    }
}
</script>