<script setup>
import Avatar from '@/Components/Avatar.vue';
import dayjs from 'dayjs';
import { marked } from 'marked';
import { ref, computed, onMounted, watch } from 'vue';

const props = defineProps({
  commentActivity: {
    type: Object,
    required: true,
    default: () => {
      return {}
    }
  },
})

const isCommentFullyVisible = ref(false);
const showReadMoreButton = ref(false);
const commentRef = ref(null);

marked.setOptions({
  gfm: true,
  breaks: true,
  headerIds: false,
});

const parsedComment = computed(() => {
  if (!props.commentActivity.body) return null;
  return marked(props.commentActivity.body);
});

const checkCommentHeight = () => {
  if (commentRef.value) {
    const maxHeight = 96;
    showReadMoreButton.value = commentRef.value.scrollHeight > maxHeight;
  }
};

onMounted(() => {
  checkCommentHeight();
});

watch(() => props.commentActivity.body, () => {
  checkCommentHeight();
});
</script>

<template>
  <div class="flex gap-3 pl-3">
    <Avatar :url="commentActivity.actor.avatar_url" />
    <div class="pt-1.5">
      <p class="flex gap-1.5 mb-4 text-sm text-tundora dark:text-spun-pearl">
        <span class="font-medium text-mondo dark:text-seashell">{{ commentActivity.actor.login }}</span>
        commented
      </p>
      <p class="text-sm text-oil dark:text-lavender-mist">
      <div v-if="parsedComment">
        <div ref="commentRef" :class="['markdown-body dark:text-lavender-mist text-oil text-sm overflow-hidden', {
          'max-h-24': !isCommentFullyVisible
        }]">
          <div v-html="parsedComment"></div>
        </div>
        <button v-if="showReadMoreButton" class='font-medium text-mondo dark:text-seashell'
          @click="isCommentFullyVisible = !isCommentFullyVisible">
          {{ isCommentFullyVisible ? 'Read less' : 'Read more' }}
        </button>
      </div>
      </p>
    </div>
    <span class="pt-1.5 text-xs text-tundora dark:text-spun-pearl ml-auto whitespace-nowrap">{{
      dayjs(commentActivity.created_at).fromNow() }}</span>
  </div>
</template>