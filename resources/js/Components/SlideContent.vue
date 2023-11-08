<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { runTextFit, openAllLinksInNewTab } from '../utils/handleContent.ts';

defineProps<{
  content: string,
}>();

const slideContent = ref<HTMLDivElement>();

const sizeContent = () : void => {
  if (!slideContent.value) {
    return;
  }

  runTextFit(slideContent.value);
};

onMounted(() : void => {
  sizeContent();
  openAllLinksInNewTab();

  window.addEventListener('resize', () : void => {
    sizeContent();
  });
});
</script>

<template>
  <div
    class="
      slide-content typography
      [ h-4/5 w-4/5 text-center ]
      [ flex justify-center items-center ]
    "
    ref="slideContent"
    v-html="content"
  ></div>
</template>
