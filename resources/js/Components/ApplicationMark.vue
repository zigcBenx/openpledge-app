<template>
    <LogoBlack
      v-if="isDark"
      class="w-[13rem] h-[5.4286rem]"
    />
    <LogoWhite
      v-else
      class="w-[13rem] h-[5.4286rem]"
    />
</template>
<script>
  import LogoWhite from './../assets/logo_white.svg';
  import LogoBlack from './../assets/logo_black.svg';
  export default {
    components: {
      LogoBlack,
      LogoWhite
    },
    props: {
      isDark: {
        type: Boolean,
        default: false,
      },
    },
  };
</script>
  