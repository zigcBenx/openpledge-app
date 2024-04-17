<template>
  <span 
    class="cursor-pointer"
  >
    <component
      :class="[{ 
        'pointer-events-all !cursor-default': disabled 
      }]"
      :is="icon"      
      @mouseover="handleMouseOver()"
      @mouseleave="handleMouseLeave()"
      :stroke="stroke"
      :fill="fillColor"
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
  import Star from './../assets/icons/star.svg';
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
      hover: {
        type: String,
        default: 'none'
      },
      disabled: {
        type: Boolean,
        default: false
      }
    },
    components: {
      Bell,
      User,
      Moon,
      Settings,
      Key,
      Search,
      Vertical,
      Close,
      Star
    },
    setup(props) {
      const components = { search: Search, bell: Bell, user: User, moon: Moon, settings: Settings, key: Key, vertical: Vertical, close:Close, star: Star };
      const icon = components[props.name];
      const fillColor = ref(props.fill);

      const handleMouseOver = (type) => {
        if(props.hover !== 'none') {
          fillColor.value = props.hover;
        }
      }
      const handleMouseLeave = () => {
        fillColor.value = props.fill !== 'none' ? props.fill : 'none';
      }

      return {
        components,
        handleMouseOver,
        handleMouseLeave,
        fillColor,
        icon
      }
    }
  };

</script>