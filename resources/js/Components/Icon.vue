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
import Github from './../assets/icons/github.svg';
import ChevronRight from './../assets/icons/chevron_right.svg';
import OpenInNew from './../assets/icons/open_in_new.svg';
import ChevronDown from './../assets/icons/chevron_down.svg';
import Link from './../assets/icons/link.svg';
import Clock from './../assets/icons/clock.svg';
import Dollar from './../assets/icons/dollar.svg';
import Sun from './../assets/icons/sun.svg';
import Up from './../assets/icons/up.svg';
import Down from './../assets/icons/down.svg';
import Letter from './../assets/icons/letter.svg';
import Master from './../assets/icons/master.svg';
import Visa from './../assets/icons/visa.svg';
import Info from './../assets/icons/info.svg';

export default {
  props: {
    name: {
      type: String
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
    Pencil,
    Github,
    ChevronRight,
    OpenInNew,
    ChevronDown,
    Link,
    Clock,
    Dollar,
    Sun,
    Up,
    Down,
    Letter,
    Master,
    Visa,
    Info
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
      close: Close,
      star: Star,
      error: Error,
      pencil: Pencil,
      github: Github,
      link: Link,
      clock: Clock,
      dollar: Dollar,
      sun: Sun,
      up: Up,
      down: Down,
      letter: Letter,
      master: Master,
      info: Info,
      visa: Visa,
      'chevron-right': ChevronRight,
      'chevron-down': ChevronDown,
      'open-in-new': OpenInNew,
    };
    const icon = components[props.name];

    return {
      components,
      icon
    }
  }
};
</script>