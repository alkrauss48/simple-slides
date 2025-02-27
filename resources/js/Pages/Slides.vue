<script setup lang="ts">
import { onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'

import AppHead from '@/Components/AppHead.vue';
import CogIcon from '@/Components/icons/CogIcon.vue';
import PreloadContent from '@/Components/PreloadContent.vue';
import SlideView from '@/Components/SlideView.vue';
import ProgressType from '@/enums/progressType.ts';
import dataStore from '@/store/dataStore.ts'
import slideStore from '@/store/slideStore.ts'

const props = defineProps<{
    index?: number,
    loop?: number,
    progress?: ProgressType,
    slides?: string,
    content?: string,
    delimiter?: string,
}>();

const processQueryParams = (): void =>  {
    slideStore.index = props.index ?? 0;
    slideStore.loop = props.loop ?? 0;
    slideStore.progress = props.progress ?? ProgressType.Bar;
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

onMounted(() => {
    processQueryParams();

    if (props.content) {
        dataStore.processData(props.content, props.delimiter);
        return;
    }

    const slidesUrl = getSlidesUrl();
    dataStore.fetchAndProcessData(slidesUrl);
});

const onBeforeSettingsVisit = () => {
    const fullPath = location.pathname + location.search;

    localStorage.setItem('appCurrentUrl', fullPath);
};
</script>

<template>
    <main>
        <AppHead />
        <Link
            href="/settings"
            :onBefore="onBeforeSettingsVisit"
            class="
            browsershot-hide fixed top-6 right-8
            text-5xl text-gray-300/50
            hover:text-gray-300 focus:text-gray-300
            "
            ><CogIcon /></Link>
        <div v-if="dataStore.data.length > 0">
            <PreloadContent />
            <SlideView />
        </div>
    </main>
</template>
