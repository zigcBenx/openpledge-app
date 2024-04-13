<script>
  import Bell from './../assets/icons/bell.svg';
  import User from './../assets/icons/user.svg';
  import Moon from './../assets/icons/moon.svg';
  import Settings from './../assets/icons/settings.svg';
  import Key from './../assets/icons/key.svg';
  import { ref, watch, onMounted, onUnmounted } from 'vue';

  export default {
    props: {
      name:{
        type:String
      },
      isDark: {
        type: Boolean,
        default: false
      },
      setColor: {
        type: String
      },
      setFill: {
        type: String
      },
    },
    components: {
      Bell,
      User,
      Moon,
      Settings,
      Key
    },
    setup(props) {
      const color = ref('#666170');
      const fill = ref('none');
      const clicked = ref(false);
      if(props.isDark) {
        color.value = '#ACA8B3';
      }

      const onclick = () => {
        clicked.value = true;
      };

      const handleMouseEnter = () => {
        fill.value = '#3FE4BD';
        color.value = '#3FE4BD';
      };
      const handleMouseLeave = () => {
        if(!clicked.value) {
          fill.value = 'none';
          color.value = '#666170';
          if(props.isDark) {
            color.value = '#ACA8B3';
          }
        }
      };

      const handleClickOutside = (event) => {
        const iconElement = document.getElementById('icon-component');

        if (iconElement && !iconElement.contains(event.target)) {
          clicked.value = false;
          fill.value = 'none';
          color.value = props.isDark ? '#ACA8B3' : '#666170';
        }
      };

      onMounted(() => {
        document.addEventListener('click', handleClickOutside);
      });

      onUnmounted(() => {
        document.removeEventListener('click', handleClickOutside);
      });

      const handleClick = () => {
        clicked.value = true;
      };

      watch(
        () => props.isDark,
        (isDark) => {
          if(isDark) {
            color.value = '#ACA8B3';
            fill.value = 'none';
          } else {
            fill.value = 'none';
            color.value = '#666170';
          }
        }
      );

      return {
        color,
        handleMouseEnter,
        handleMouseLeave,
        fill,
        handleClick
      }
    }
  };

</script>
  

<template>
  <span 
    @click="handleClick"
    @mouseenter="handleMouseEnter"
    @mouseleave="handleMouseLeave"
  >
    <Bell v-if="name==='Bell'" :stroke="color" :fill="fill" />
    <User v-if="name==='User'" id="icon-component" :stroke="color" :fill="fill" />
    <Moon v-if="name==='Moon'" :stroke="color" :fill="fill" />
    <Settings v-if="name==='Settings'" :stroke="setColor ? setColor : color" :fill="setFill ? setFill :fill" />
    <Key v-if="name==='Key'" :stroke="setColor ? setColor : color" :fill="setFill ? setFill : fill" />
  </span>
</template>