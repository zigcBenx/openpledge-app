<template>
    <Row class="dark:bg-rich-black bg-seashell rounded-md gap-0 md:gap-0 lg:gap-0 xl:gap-0">
        <!-- Conditional Checkbox Row -->
        <Col v-if="checkboxLabel" class="dark:text-seashell pt-4 hover:ring-2 hover:ring-green rounded-md">
            <label class="pb-2.5 px-5 flex items-center space-x-4 cursor-pointer">
                <Checkbox v-model:checked="isChecked" />
                <span>{{ checkboxLabel }}</span>
            </label>
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