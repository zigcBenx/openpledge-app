<template>
  <component
    :class="['min-w-5 min-h-5', { 
      'pointer-events-all !cursor-default': disabled,
      'w-7 h-7': size === 'lg' 
    }]"
    class="cursor-pointer"
    :is="icon"
  />
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
  import Error from './../assets/icons/error.svg';
  import Pencil from './../assets/icons/pencil.svg';

  export default {
    props: {
      name:{
        type:String
      },
      disabled: {
        type: Boolean,
        default: false
      },
      size: {
        type: String,
        default: 'md',
        validator: (value) =>
          ["md", "lg"].includes(value),
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
      Close,
      Star,
      Error,
      Pencil
    },
    setup(props) {
      const components = { 
        search: Search, 
        bell: Bell, 
        user: User, 
        moon: Moon, 
        settings: Settings, 
        key: Key, 
        vertical: Vertical, 
        close:Close, 
        star: Star, 
        error: Error,
        pencil: Pencil
      };
      const icon = components[props.name];
      
      return {
        components,
        icon
      }
    }
  };

</script>