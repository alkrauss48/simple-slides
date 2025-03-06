<script setup lang="ts">
import { computed, onMounted } from 'vue'

import DraftBanner from '@/Components/DraftBanner.vue';
import SlideView from '@/Components/SlideView.vue';
import SlideLayout from '@/Layouts/SlideLayout.vue';
import Presentation from '@/interfaces/presentation.ts';
import QueryParams from '@/interfaces/queryParams.ts';
import dataStore from '@/store/dataStore.ts'
import { processQueryParams } from '@/utils/handleQueryParams.ts'

const props = defineProps<
    QueryParams & {
        presentation: Presentation,
    }
>();

const isDraft = computed(() => !props.presentation.is_published);

onMounted(() => {
    processQueryParams(props);

    const { content, slide_delimiter } = props.presentation;

    dataStore.processData(content, slide_delimiter);
});
</script>

<template>
    <SlideLayout>
        <DraftBanner v-if="isDraft" />
        <SlideView />
    </SlideLayout>
</template>
