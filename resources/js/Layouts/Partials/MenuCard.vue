<template>
    <Row class="dark:bg-rich-black xl:gap-1 bg-light-gray p-6 rounded-md">
        <Col>
            <div class="items-center flex dark:bg-charcoal-gray bg-ghost-white p-4 rounded-md">
                <div class="w-[68px]">
                    <img src="images/avatar.png" class="rounded-full float-right" />
                </div>
                <div class="pl-4">
                    <div class="dark:text-light-frost text-xl text-jet-black">{{ $page.props.auth.user.name }}</div>
                    <div class="text-secondary dark:text-primary">{{ $page.props.auth.user.email }}</div>
                </div>
            </div>
        </Col>
        <Col>
            <DropdownLink class="dark:text-gray-tones rounded-sm text-rich-black" :href="route('profile.show')">
                <Icon name="Settings" setColor="#E5E4E7" setFill="none"></Icon> <span>Settings</span>
            </DropdownLink>
        </Col>
        <Col>
            <DropdownLink class="dark:text-gray-tones rounded-sm text-rich-black" v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">
                <Icon name="Key" setColor="#E5E4E7" setFill="none"></Icon> <span>API Tokens</span>
            </DropdownLink>
        </Col>

        <Col>
            <form @submit.prevent="logout">
                <DropdownLink as="button" class="text-green text-center rounded-full dark:bg-shade-green">
                    Sign Out
                </DropdownLink>
            </form>
        </Col>
        <Col>
            <span>Terms & Conditions</span>
            <span>.</span>
            <span>Privacy Policy</span>
        </Col>
    </Row>

</template>
<script>
import DropdownLink from '@/Components/DropdownLink.vue';
import Row from '@/Components/Grid/Row.vue';
import Col from '@/Components/Grid/Col.vue';
import Icon from '@/Components/Icon.vue';

export default {
    components: {
        DropdownLink,
        Row,
        Col,
        Icon
    },
    setup() {
    }
}
</script>