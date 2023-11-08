<script setup lang="ts">
import { onMounted } from 'vue'
import PreloadContent from '@/Components/PreloadContent.vue';
import SlideView from '@/Components/SlideView.vue';
import CogIcon from '@/Components/icons/CogIcon.vue';
import ProgressType from '../enums/progressType.ts';
import dataStore from '../store/dataStore.ts'
import slideStore from '../store/slideStore.ts'
import { usePage } from '@inertiajs/vue3'

const page = usePage<{
    props: {
        index?: string,
        progress?: ProgressType,
    },
}>();

const props = defineProps<{
  slides: string
}>();

const processQueryParams = (): void =>  {
  slideStore.index = page.props.index ?? 0;
  slideStore.progress = page.props.progress ?? ProgressType.Bar;
};

const getSlidesUrl = (): string => {
  if (!props.slides) {
    localStorage.setItem('slidesUrl', '');

    return '/instructions.md';
  }

  const url = atob(props.slides as string);

  localStorage.setItem('slidesUrl', url);
  return url;
};

onMounted(async () => {
  processQueryParams();
  dataStore.fetchAndProcessData(getSlidesUrl());
});
</script>

<template>
  <main>
    <a
      href="/settings"
      class="
        fixed top-6 right-8
        text-5xl text-gray-300/50
        hover:text-gray-300 focus:text-gray-300
      "
      ><CogIcon /></a>
    <div v-if="dataStore.data.length > 0">
      <PreloadContent />
      <SlideView />
    </div>
  </main>
</template>
