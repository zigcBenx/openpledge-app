<template>
  <span 
    class="cursor-pointer"
  >
    <component
      :is="components[name]"
      :stroke="stroke"
      :fill="fill"
    />
  </span>
</template>
<script>
  import Bell from './../assets/icons/bell.svg';
  import User from './../assets/icons/user.svg';
  import Moon from './../assets/icons/moon.svg';
  import Settings from './../assets/icons/settings.svg';
  import Key from './../assets/icons/key.svg';
  import Search from './../assets/icons/search.svg';
  import Vertical from './../assets/icons/vertical.svg';
  import Close from './../assets/icons/close.svg';
  import { ref, watch, onMounted, onUnmounted } from 'vue';

  export default {
    props: {
      name:{
        type:String
      },
      stroke: {
        type: String,
        default: 'none'
      },
      fill: {
        type: String,
        default: 'none'
      },
    },
    components: {
      Bell,
      User,
      Moon,
      Settings,
      Key,
      Search,
      Vertical,
      Close
    },
    setup(props) {
      const components = {search: Search, bell: Bell, user: User, moon: Moon, settings: Settings, key: Key, vertical: Vertical, close:Close};

      return {
        components
      }
    }
  };

</script>