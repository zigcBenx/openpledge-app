<template>
  <Row class="dark:bg-rich-black xl:gap-1 bg-seashell p-6 rounded-md">
    <Col v-if="isAuthenticated">
      <Col>
        <Link :href="route('profile.show')">
          <div class="items-center flex dark:bg-charcoal-gray bg-seashell p-4 rounded-md">
            <div class="w-[4.25rem]">
              <img 
              :src="authenticatedUser()?.profile_photo_url" 
              class="rounded-full float-right"
              :alt="authenticatedUser()?.name" />
            </div>
            <div class="pl-4">
              <div class="dark:text-lavender-mist text-xl text-oil brake-all">{{ authenticatedUser()?.name }}</div>
              <div class="text-tundora dark:text-spun-pearl brake-all">{{ authenticatedUser()?.email }}</div>
            </div>
          </div>
        </Link>
      </Col>
      <Col>
        <DropdownLink class="dark:text-platinum rounded-sm text-rich-black" :href="route('profile.settings')">
          <Icon name="settings" class="dark:stroke-platinum stroke-rich-black"></Icon> <span class="pl-2">Settings</span>
        </DropdownLink>
        <DropdownLink 
          class="dark:text-platinum rounded-sm text-rich-black" 
          v-if="$page.props.jetstream.hasApiFeatures"
          :href="route('api-tokens.index')"
        >
          <Icon name="key" class="dark:stroke-platinum stroke-rich-black"></Icon>
          <span class="pl-2">API Tokens</span>
        </DropdownLink>
        <DropdownLink 
          class="dark:text-platinum rounded-sm text-rich-black pl-2"
          @click="connectStripe"
        >
          <Icon name="dollar" class="dark:stroke-platinum stroke-rich-black"></Icon> 
          <span class="pl-3">{{ hasUserStripeId() ? 'Open Stripe Dashboard' : 'Connect Stripe' }}</span>
        </DropdownLink>
        <DropdownLink 
          class="dark:text-platinum rounded-sm text-rich-black" 
          :href="route('discover.issues')" 
          @click="setTutorialInProgress()"
        >
          <Icon name="pin" class="dark:stroke-platinum stroke-rich-black"></Icon> 
          <span class="pl-2">Start guided tour</span>
        </DropdownLink>
      </Col>
    </Col>
    <Col>
      <form method="POST" @submit.prevent="isAuthenticated ? logout() : login()">
        <Button color="secondary" type="submit" class="rounded-md">
          {{ isAuthenticated ? 'Sign Out' : 'Log In' }}
        </Button>
      </form>
    </Col>
    <Col class="dark:text-seashell text-sm py-2 flex justify-between">
      <span>Terms & Conditions</span>
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
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const logout = () => {
  router.post(route('logout'), {}, {
    onSuccess: () => {
      isAuthenticated.value = false;
    }
  });
};

const login = () => {
  router.visit(route('login'));
};

const page = usePage()

const connectStripe = async () => {
  if (hasUserStripeId()) {
    await redirectToStripeDashboard()
    return
  }
  router.visit(route('stripe.connect'))
}

const authenticatedUser = () => {
  return page.props.auth?.user
}

const hasUserStripeId = () => {
  return authenticatedUser()?.stripe_id
}

const isAuthenticated = ref(authenticatedUser() !== null);

const redirectToStripeDashboard = async () => {
  const response = await axios.get(route('stripe.dashboard.link'))
  window.open(response.data.url, '_blank')
}

const setTutorialInProgress = () => {
  localStorage.setItem("isTutorialInProgress", "true");
};
</script>