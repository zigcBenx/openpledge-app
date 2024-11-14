<template>
  <Row class="dark:bg-rich-black xl:gap-1 bg-seashell p-6 rounded-md">
    <Col>
    <Link :href="route('profile.show')">
    <div class="items-center flex dark:bg-charcoal-gray bg-seashell p-4 rounded-md">
      <div class="w-[4.25rem]">
        <img :src="$page.props.auth.user.profile_photo_url" class="rounded-full float-right" />
      </div>
      <div class="pl-4">
        <div class="dark:text-lavender-mist text-xl text-oil brake-all">{{ $page.props.auth.user.name }}</div>
        <div class="text-tundora dark:text-spun-pearl brake-all">{{ $page.props.auth.user.email }}</div>
      </div>
    </div>
    </Link>
    </Col>
    <Col>
    <DropdownLink class="dark:text-platinum rounded-sm text-rich-black" :href="route('profile.settings')">
      <Icon name="settings" class="dark:stroke-platinum stroke-rich-black"></Icon> <span class="pl-2">Settings</span>
    </DropdownLink>
    <DropdownLink class="dark:text-platinum rounded-sm text-rich-black" v-if="$page.props.jetstream.hasApiFeatures"
      :href="route('api-tokens.index')">
      <Icon name="key" class="dark:stroke-platinum stroke-rich-black"></Icon> <span class="pl-2">API Tokens</span>
    </DropdownLink>
    <DropdownLink href="#" class="dark:text-platinum rounded-sm text-rich-black"
      title="This does not work yet in beta!"> <!--@click="connectStripe"-->
      <Icon name="key" class="dark:stroke-platinum stroke-rich-black"></Icon> <span class="pl-2">Connect Stripe</span>
    </DropdownLink>
    <DropdownLink class="dark:text-platinum rounded-sm text-rich-black" :href="route('discover.issues')" @click="setTutorialInProgress()">
      <Icon name="pin" class="dark:stroke-platinum stroke-rich-black"></Icon> <span class="pl-2">Start guided
        tour</span>
    </DropdownLink>
    </Col>
    <Col>
    <form method="POST" @submit.prevent="logout">
      <Button color="secondary" type="submit" class="rounded-md">
        Sign Out
      </Button>
    </form>
    </Col>
    <Col class="dark:text-seashell text-sm py-2">
    <span>Terms & Conditions</span>
    <span class="px-2">.</span>
    <span>Privacy Policy</span>
    </Col>
  </Row>
</template>
<script setup>
import DropdownLink from '@/Components/DropdownLink.vue';
import Button from '@/Components/Button.vue';
import Row from '@/Components/Grid/Row.vue';
import Col from '@/Components/Grid/Col.vue';
import Icon from '@/Components/Icon.vue';
import { Link, router } from '@inertiajs/vue3';

const logout = () => {
  router.post(route('logout'));
};

defineProps({
  isDark: {
    type: Boolean,
    default: false
  }
});

const connectStripe = () => {
  router.post('/stripe-redirect');
}

const setTutorialInProgress = () => {
  localStorage.setItem("isTutorialInProgress", "true");
};
</script>