<script setup lang="ts">
import { onMounted } from 'vue'

import SlideView from '@/Components/SlideView.vue';
import { INSTRUCTIONS_URL } from '@/constants/general.ts';
import SlideLayout from '@/Layouts/SlideLayout.vue';
import QueryParams from '@/interfaces/queryParams.ts';
import dataStore from '@/store/dataStore.ts'
import { processQueryParams } from '@/utils/handleQueryParams.ts'

const props = defineProps<
    QueryParams & {
        encodedSlides?: string,
    }
>();

const getSlidesUrl = (): string => {
    if (!props.encodedSlides) {
        localStorage.setItem('slidesUrl', '');

        return INSTRUCTIONS_URL;
    }

    const url = atob(props.encodedSlides as string);

    localStorage.setItem('slidesUrl', url);
    return url;
};

onMounted(() => {
    processQueryParams(props);

    const slidesUrl = getSlidesUrl();
    dataStore.fetchAndProcessData(slidesUrl);
});
</script>

<template>
    <SlideLayout>
        <SlideView />
    </SlideLayout>
</template>
