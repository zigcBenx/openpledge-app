<template>
    <AppLayout title="Issues">
        <template #header>
        </template>
        <div class="flex gap-10">
            <div class="flex flex-grow">
                <Page title="Issues" class="pb-10" description="Search issues you are interested in...">
                    <template #actions>
                        <button @click="displayFilterModal = true" class="w-[6.688rem] justify-center hover:bg-mint-green text-dark-green flex dark:text-green text-dark-green p-1.5 dark:hover:bg-tropical-rain-forest dark:hover:text-green rounded-full py-3 px-3.5">
                            Filters 
                            <Icon class="pl-1 dark:fill-green fill-dark-green" name="vertical" />
                        </button>
                    </template>
                    <template #filters>
                        <div 
                            class="gap-2 flex"
                            v-for="selectedValue in queryFilters.labels"    
                        >
                            <Pill 
                                v-if="selectedValue"
                                :key="selectedValue"
                                color="secondary"
                                :contentClasses="['px-2', 'py-1']"
                                :dismissable="true"
                                @dismiss="() => handleRemoveOption(selectedValue, keys.labels)"
                            >
                                {{ labels.find(item => item.value === selectedValue)?.label }}
                            </Pill>
                        </div>
                        <div 
                            class="gap-2 flex"
                            v-for="selectedValue in queryFilters.languages"    
                        >
                            <Pill 
                                v-if="selectedValue"    
                                :key="selectedValue"
                                color="secondary"
                                :contentClasses="['px-2', 'py-1']"
                                :dismissable="true"
                                @dismiss="() => handleRemoveOption(selectedValue, keys.languages)"
                            >
                                {{ languages.find(item => item.value === selectedValue)?.label }}
                            </Pill>
                        </div>
                        <div v-if="queryFilters.start !== 'null' && queryFilters.end">
                            <Pill 
                                color="secondary"
                                :contentClasses="['px-2', 'py-1']"
                                :dismissable="true"
                                @dismiss="() => handleRemoveOption(null, keys.range)"
                            >
                                ${{ queryFilters.start }} - ${{ queryFilters.end }}
                            </Pill>
                        </div>
                    </template>
                    <template v-slot="">
                        <IssuesTable :issues="issues" @onLazyLoading="handleLazyLoadingIssues" />
                    </template>
                </Page>
                </div>
                <div class="w-[27.188rem] hidden xl:block">
                <Sidebar 
                    :trendingToday="trendingToday" 
                    :topContributors="topContributors"
                    :topDonators="topDonators"
                />
            </div>
        </div>
        <Filters 
            @submit="updateFilterList" 
            @display="handleDisplayModal"
            :displayFilterModal="displayFilterModal" 
            :labels="labels"
            :languages="languages"
            :queryFilters="queryFilters"
            :removedFilters="removedFilters"
        />
    </AppLayout>
</template>
<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import ListIssues from '@/Components/Custom/ListIssues.vue';
import Pill from '@/Components/Form/Pill.vue';
import { onMounted, ref } from 'vue';
import Row from '@/Components/Grid/Row.vue';
import Col from '@/Components/Grid/Col.vue';
import Icon from '@/Components/Icon.vue';
import { useDark } from '@vueuse/core';
import Filters from './Filters.vue';
import Page from '@/Components/Page.vue';
import IssuesTable from '@/Components/Custom/IssuesTable.vue'
import Sidebar from './Partials/Sidebar.vue';
import { parseQueryFilters, updateQueryFilters } from '../../libs/queryLibs.js'
import { 
    languages as languagesList, 
    labels as labelsList, 
    issues as issuesList, 
    trendingToday, 
    topContributors, 
    topDonators
} from '../../assets/mockedData.js'

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
        IssuesTable,
        Sidebar
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
        const isDark = useDark();
        const labels = ref(labelsList);
        const languages = ref(languagesList);
        const pagedIssues = ref(0);
        const issues = ref([]);
        const displayFilterModal = ref(false);        
        const queryFilters = ref({});
        const removedFilters = ref(0);
        const keys = {labels: 'labels', languages: 'languages', range: 'range'}

        const updateFilterList = (value) => {
            updateQueryFilters(value);
            queryFilters.value = parseQueryFilters();
        }

        onMounted(() => {
            queryFilters.value = parseQueryFilters();
        });

        const handleRemoveOption = (value, key) => {
            if(key === keys.labels) {
                const indexToRemove = queryFilters.value.labels?.indexOf(value);
                if (indexToRemove !== -1) {
                    queryFilters.value.labels.splice(indexToRemove, 1);
                }
            } else if(key === keys.languages) {
                const indexLangToRemove = queryFilters.value.languages?.indexOf(value);
                if (indexLangToRemove !== -1) {
                    queryFilters.value.languages.splice(indexLangToRemove, 1);
                }
            } else if(key === keys.range) {
                queryFilters.value.start = null;
                queryFilters.value.end = null;
            } else if(key) {
                queryFilters.value[key] = null;
            }
            updateQueryFilters(queryFilters.value);
            removedFilters.value++;
        }

        const handleDisplayModal = () => {
            displayFilterModal.value = !displayFilterModal.value;
        }

        const handleLazyLoadingIssues = () => {
            pagedIssues.value = pagedIssues.value + 1;
            issues.value = issuesList.slice(pagedIssues.value, pagedIssues.value * 10 + 20)
        }

        return {
            labels,
            languages,
            issues,
            isDark,
            displayFilterModal,
            handleDisplayModal,
            handleRemoveOption,
            trendingToday,
            updateFilterList,
            queryFilters,
            topDonators,
            topContributors,
            handleLazyLoadingIssues,
            removedFilters,
            keys
        }
    }
}
</script>