<template>
  <div v-if="editor">
    <button
      :class="{ 'is-active': editor.isActive('bold') }" 
      type="button"
      class="menubar__button"
      @click.prevent="editor.chain().focus().toggleBold().run()"
    >
      <i class="fa fa-bold" />
    </button>
    <button
      :class="{ 'is-active': editor.isActive('italic') }" 
      type="button"
      class="menubar__button pl-2"
      @click.prevent="editor.chain().focus().toggleItalic().run()"
    >
      <i class="fa fa-italic" />
    </button>
    <button
      :class="{ 'is-active': editor.isActive('strike') }" 
      type="button"
      class="menubar__button pl-2"
      @click.prevent="editor.chain().focus().toggleStrike().run()"
    >
      <i class="fa fa-strikethrough" />
    </button>
    <button
      :class="{ 'is-active': editor.isActive('underline') }" 
      type="button"
      class="menubar__button pl-2"
      @click.prevent="editor.chain().focus().toggleUnderline().run()"
    >
      <i class="fa fa-underline" />
    </button>
    <button
      :class="{ 'is-active': editor.isActive('link') }" 
      type="button"
      class="menubar__button pl-2"
      @click.prevent="setLink"
    >
      <i class="fa fa-link" />
    </button>
    <button
      :class="{ 'is-active': editor.isActive('heading', { level: 1 }) }" 
      type="button"
      class="menubar__button pl-2"
      @click.prevent="editor.commands.toggleHeading({ level: 1 })"
    >
      <span><i class="fa fa-header" />1</span>
    </button>
    <button
      :class="{ 'is-active': editor.isActive('heading', { level: 2 }) }" 
      type="button"
      class="menubar__button pl-2"
      @click.prevent="editor.commands.toggleHeading({ level: 2 })"
    >
      <span><i class="fa fa-header" />2</span>
    </button>
    <button
      :class="{ 'is-active': editor.isActive('heading', { level: 3 }) }" 
      type="button"
      class="menubar__button pl-2"
      @click.prevent="editor.commands.toggleHeading({ level: 3 })"
    >
      <span><i class="fa fa-header" />3</span>
    </button>
    <button
      :class="{ 'is-active': editor.isActive('bulletList') }" 
      type="button"
      class="menubar__button pl-2"
      @click.prevent="editor.commands.toggleBulletList()"
    >
      <i class="fa fa-list-ul" />
    </button>
    <button
      :class="{ 'is-active': editor.isActive('orderedList') }" 
      type="button"
      class="menubar__button pl-2"
      @click.prevent="editor.commands.toggleOrderedList()"
    >
      <i class="fa fa-list-ol" />
    </button>
    <!-- <button
      :class="{ 'is-active': editor.isActive({ textAlign: 'left' }) }" 
      type="button"
      class="menubar__button pl-2"
      @click.prevent="editor.chain().focus().setTextAlign('left').run()"
    >
      <i class="fa fa-align-left" />
    </button>
    <button
      :class="{ 'is-active': editor.isActive({ textAlign: 'center' }) }" 
      type="button"
      class="menubar__button pl-2"
      @click.prevent="editor.chain().focus().setTextAlign('center').run()"
    >
      <i class="fa fa-align-center" />
    </button>
    <button
      :class="{ 'is-active': editor.isActive({ textAlign: 'right' }) }" 
      type="button"
      class="menubar__button pl-2"
      @click.prevent="editor.chain().focus().setTextAlign('right').run()"
    >
      <i class="fa fa-align-right" />
    </button> -->
  </div>
  <editor-content :editor="editor" />
</template>

<script>
import { Editor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Underline from '@tiptap/extension-underline'
import Link from '@tiptap/extension-link'
import Heading from '@tiptap/extension-heading'
import ListItem from '@tiptap/extension-list-item'
import OrderedList from '@tiptap/extension-ordered-list'
import BulletList from '@tiptap/extension-bullet-list'

export default {
  components: {
    EditorContent,
  },

  data() {
    return {
      editor: null,
    }
  },

  props: {
    modelValue: {
      type: String,
      default: '',
    },
  },
  emits: ['update:modelValue'],
  watch: {
    modelValue(value) {
      // HTML
      const isSame = this.editor.getHTML() === value

      // JSON
      // const isSame = JSON.stringify(this.editor.getJSON()) === JSON.stringify(value)

      if (isSame) {
        return
      }

      this.editor.commands.setContent(value, false)
    },
  },

  mounted() {
    this.editor = new Editor({
      extensions: [
        StarterKit,
        Underline,
        Link,
        Heading,
        ListItem,
        OrderedList,
        BulletList
      ],
      content: this.modelValue,
      onUpdate: () => {
        // HTML
        this.$emit('update:modelValue', this.editor.getHTML())

        // JSON
        // this.$emit('update:modelValue', this.editor.getJSON())
      },
    })
  },

  methods: {
    setLink() {
      const previousUrl = this.editor.getAttributes('link').href
      const url = window.prompt('URL', previousUrl)

      // cancelled
      if (url === null) {
        return
      }

      // empty
      if (url === '') {
        this.editor
          .chain()
          .focus()
          .extendMarkRange('link')
          .unsetLink()
          .run()

        return
      }

      // update link
      this.editor
        .chain()
        .focus()
        .extendMarkRange('link')
        .setLink({ href: url })
        .run()
    },
  },

  beforeUnmount() {
    this.editor.destroy()
  },
}
</script>

<style lang="scss">
.tiptap {
  border-width: 2px;
  border-radius: 0.5rem;
  border-color: rgb(75 85 99 / var(--tw-border-opacity));
}
.tiptap h1 {
  font-size: 1.55rem;
}

.tiptap a {
    color: blue; /* Default link color */
    text-decoration: underline; /* Underline by default */
}

.tiptap a:hover {
    text-decoration: underline; /* Maintain underline on hover */
}

.tiptap a:visited {
    color: purple; /* Visited link color */
}

.tiptap h2 {
  font-size: 1.35rem;
}

.tiptap h3 {
  font-size: 1.15rem;
}

.tiptap ul>li {
  list-style-type: disc;
}

.tiptap ul {
  padding-left: 2rem;
}

.tiptap ol>li {
  list-style-type: decimal;
}

.tiptap ol {
  padding-left: 2rem;
}

.html-editor {
  padding: 0 12px;
}
.menubar__button {
  font-weight: 700;
  display: inline-flex;
  background: rgba(0, 0, 0, 0);
  border: 0;
  color: #fff;
  padding: 5px 10px;
  border-radius: 2px;
  cursor: pointer;
}
.menubar__button:hover {
  background-color: rgba(0, 0, 0, 0.05);
}
.menubar__button.is-active {
  background-color: rgba(255, 255, 255, 0.1);
}
.html-editor li,
.html-editor li > p {
  padding-bottom: 0 !important;
  margin-bottom: 0 !important;
}
.html-editor p:last-of-type {
  padding-bottom: 0;
}
.menu-bar {
  background: #fafafa;
  position: sticky;
  top: 55px;
  z-index: 1;
  border-radius: 10px 10px 10px 10px;
}
</style>
