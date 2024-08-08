<template>
    <Row class="dark:bg-rich-black bg-seashell rounded-md gap-0 md:gap-0 lg:gap-0 xl:gap-0">
        <Col v-for="item in data" class="dark:text-seashell pt-8">
            <div class="pb-2.5 px-5">{{ item.name }}</div>
            <DropdownLink v-for="value in item.values" href="#" :contentClasses="['dark:text-spun-pearl', 'text-sm', 'py-3.5']">
                {{ value }}
            </DropdownLink>
        </Col>
    </Row>
</template>
<script>
    import Row from '@/Components/Grid/Row.vue';
    import Col from '@/Components/Grid/Col.vue';
    import { ref } from 'vue';
    import { Link } from "@inertiajs/vue3";
    import DropdownLink from '@/Components/DropdownLink.vue';

    export default {
        props: {
            isDark: {
                type: Boolean,
                default: false
            }
        },
        components: {
            Link,
            Row,
            Col,
            DropdownLink
        },
        setup() {
            const data = ref([
                {name:'Owners', values: ['openai','aksonov','microsoft','OpenPledge']},
                {name:'Repositories', values: ['openai/evals','aksonov','microsoft/vscode','OpenPledge/OpenPledge']}
            ])
            return {data}
        }
    }
</script>