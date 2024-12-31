<template>
    <Row class="dark:bg-rich-black bg-seashell rounded-md gap-0 md:gap-0 lg:gap-0 xl:gap-0">
        <!-- Conditional Checkbox Row -->
        <Col 
            v-if="checkboxLabel" 
            class="dark:text-seashell pt-4 hover:ring-2 hover:ring-green rounded-md group relative"
        >
            <label class="pb-2.5 px-5 flex items-center space-x-4" :class="[isCheckboxDisabled ? 'cursor-not-allowed' : 'cursor-pointer']">
                <Checkbox v-model:checked="isChecked" :disabled="isCheckboxDisabled" :class="[isCheckboxDisabled ? 'cursor-not-allowed' : 'cursor-pointer']" />
                <span>{{ checkboxLabel }}</span>
            </label>
            
            <!-- Tooltip -->
            <div v-if="isCheckboxDisabled && tooltipText.length > 0" 
                 class="invisible group-hover:visible absolute -top-12 left-1/2 transform -translate-x-1/2 px-3 py-2 
                        bg-gunmetal dark:bg-rich-black text-white text-sm rounded-md shadow-lg ring-2 ring-green
                        before:content-[''] before:absolute before:top-full before:left-1/2 before:-translate-x-1/2 
                        before:border-8 before:border-transparent before:border-t-gunmetal dark:before:border-t-rich-black
                        whitespace-nowrap z-10">
                {{ tooltipText }}
            </div>
        </Col>

        <!-- Dynamic Rows -->
        <Col
            v-for="(item, index) in data"
            :key="item.name"
            :class="['dark:text-seashell', { 'pt-4': index !== 0 }]"
        >
            <div class="pb-2.5 px-5">{{ item.name }}</div>
            <DropdownLink
                v-for="value in item.values"
                :key="value.id"
                :href="getSearchItemHref(item.name, value)"
                :contentClasses="['dark:text-spun-pearl', 'text-sm', 'py-3.5']"
            >
                {{ value.text }}
            </DropdownLink>
        </Col>
    </Row>
</template>
<script>
    import Row from '@/Components/Grid/Row.vue';
    import Col from '@/Components/Grid/Col.vue';
    import DropdownLink from '@/Components/DropdownLink.vue';
    import Checkbox from '@/Components/Checkbox.vue';
    import { ref, watch } from 'vue';

    export default {
        props: {
            isDark: {
                type: Boolean,
                default: false
            },
            data: {
                type: Array,
                default: () => []
            },
            checkboxLabel: {
                type: String,
                default: null
            },
            getSearchItemHref: {
                type: Function,
                required: true
            },
            isCheckboxDisabled: {
                type: Boolean,
                default: false
            },
            tooltipText: {
                type: String,
                default: ''
            }
        },
        components: {
            Row,
            Col,
            DropdownLink,
            Checkbox
        },
        setup(_, { emit }) {
            const isChecked = ref(false);

            watch(isChecked, (newValue) => {
                emit('checkbox-toggled', newValue);
            });

            return {
                isChecked
            };
        }
    };
</script>