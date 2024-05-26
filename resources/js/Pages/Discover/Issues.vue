<template>
    <AppLayout title="Issues">
        <template #header>
        </template>
        <div class="flex gap-10">
            <div class="flex flex-grow">
                <Page title="Issues" class="pb-10" description="Search issues you are interested in...">
                    <template #actions>
                        <button @click="displayFilterModal = true" class="justify-center hover:bg-mint-green text-dark-green flex dark:text-green text-dark-green p-1.5 dark:hover:bg-tropical-rain-forest dark:hover:text-green rounded-full py-3 px-3.5">
                            Filters {{ queryFilters.length ? '('+queryFilters.length+')' : '' }}
                            <Icon class="pl-1 dark:fill-green fill-dark-green" name="vertical" />
                        </button>
                    </template>
                    <template #filters>
                        <div class="relative">
                            <div class="flex gap-2 flex-wrap overflow-hidden" 
                                :class="{ 'h-10': hiddenFilters, 'h-20': isMobile() && hiddenFilters, 'w-4/5': !isMobile() }"    
                                ref="el">
                                <div 
                                    class="gap-2 flex"
                                    v-for="(selectedValue, index) in queryFilters"   
                                >
                                    <Pill 
                                        :key="selectedValue"
                                        v-if="getValue(selectedValue)"
                                        color="secondary"
                                        :contentClasses="['px-2', 'py-1']"
                                        :dismissable="true"
                                        @dismiss="() => handleRemoveOption(selectedValue, selectedValue.key)"
                                    >
                                    
                                    {{ 
                                        getValue(selectedValue)
                                    }}
                                    </Pill>
                                </div>
                                <div 
                                    v-if="!hiddenFilters && queryFilters.length > count"
                                    class="text-sm dark:text-seashell text-mondo items-center pl-2 cursor-pointer flex" 
                                    @click="showMoreFilters" >
                                        Hide
                                        <Icon name="up" class="ml-1 fill-mondo dark:fill-seashell" />
                                </div>
                            </div>
                            <div 
                                v-if="hiddenFilters && queryFilters.length > count"
                                @click="showMoreFilters" 
                                class="text-sm dark:text-seashell text-mondo cursor-pointer flex items-center" 
                                :class="{'absolute top-2 right-3': (hiddenFilters && !isMobile())}">
                                    Show {{ queryFilters.length - count }} more
                                    <Icon name="down" class="ml-1 fill-mondo dark:fill-seashell" />
                            </div>
                        </div>
                    </template>
                    <template v-slot="">
                        <IssuesTable :issues="issues" @onLazyLoading="handleLazyLoadingIssues"  class="hidden md:table"/>
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
            :keys="keys"
        />
    </AppLayout>
</template>
<script setup>
  import { ref, onMounted, watch } from 'vue';
  import { parseQueryFilters, updateQueryFilters } from '../../utils/parseQuery.js';
  import { languages as languagesList, labels as labelsList, issues as issuesList, trendingToday, topContributors, topDonators } from '../../assets/mockedData.js';

  import Page from '@/Components/Page.vue';
  import Filters from './Filters.vue';
  import Icon from '@/Components/Icon.vue';
  import Pill from '@/Components/Form/Pill.vue';
  import AppLayout from '@/Layouts/AppLayout.vue';
  import IssuesTable from '@/Components/Custom/IssuesTable.vue';
  import Sidebar from './Partials/Sidebar.vue';
  import { useElementSize } from '@vueuse/core';

  const keys = { labels: 'labels', languages: 'languages', range: 'range', date: 'date', storageDiscoverKey: 'discover' };

  const labels = ref(labelsList);
  const languages = ref(languagesList);
  const pagedIssues = ref(0);
  const issues = ref([]);
  const displayFilterModal = ref(false);
  const queryFilters = ref({});
  const removedFilters = ref(0);
  const count = ref(0);

  const el = ref(null)  
  const { width } = useElementSize(el);
  const hiddenFilters = ref(true);
  const childNodesWidth = ref(0);
  const isMobile = () => {
    var check = false;
    (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
    return check;
  }

  watch(width, (newWidth) => {
    const childNodes = el.value.childNodes;
    if(isMobile()) {
        newWidth *= 2;
    }
    childNodes.forEach((childNode) => {
        if(typeof childNode.offsetWidth === 'number') {
            childNodesWidth.value += childNode.offsetWidth;
            if(childNodesWidth.value < newWidth) {
                count.value++;
            }
        }
    }); 
  });

  const updateFilterList = (value) => {
    updateQueryFilters(value);
    queryFilters.value = [...value];
  };

  const handleRemoveOption = (value, key) => {
    let filterToRemove = queryFilters.value.findIndex(item => item.key === key && item.value === value.value);
    if(filterToRemove !== -1) {
        queryFilters.value.splice(filterToRemove, 1);
    }

    updateQueryFilters(queryFilters.value);
    removedFilters.value++;
  };

  const handleDisplayModal = () => {
    displayFilterModal.value = !displayFilterModal.value;
  };

  const handleLazyLoadingIssues = () => {
    pagedIssues.value = pagedIssues.value + 1;
    issues.value = issuesList.slice(pagedIssues.value, pagedIssues.value * 10 + 20);
  };

  onMounted(() => {
    if(localStorage.getItem(keys.storageDiscoverKey)) {
        queryFilters.value = JSON.parse(localStorage.getItem(keys.storageDiscoverKey));
    } else {
        queryFilters.value = parseQueryFilters();
    }
  });

  const showMoreFilters = () => {
    hiddenFilters.value = !hiddenFilters.value;
  }

  const getValue = (value) => {
    if(labels.value.find(item => keys.labels === value.key && item.value === value.value)) {
        return labels.value.find(item => keys.labels === value.key && item.value === value.value).label;
    } else if(languages.value.find(item => keys.languages === value.key && item.value === value.value)) {
        return languages.value.find(item => keys.languages === value.key && item.value === value.value).label;
    } else if(value.key === keys.range) {
        return '$'+value.value.start +'-$'+value.value.end;
    } else if(value.key === keys.date) {
        return value.value.year;
    }
    return false;
  }
</script>