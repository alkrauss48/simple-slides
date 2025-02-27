<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue'

import ProgressBar from '@/Components/ProgressBar.vue';
import ProgressLabel from '@/Components/ProgressLabel.vue';
import SlideArrows from '@/Components/SlideArrows.vue';
import SlideContent from '@/Components/SlideContent.vue';
import ProgressType from '@/enums/progressType.ts';
import QueryParams from '@/interfaces/queryParams.ts';
import Keys from '@/constants/keys.ts';
import dataStore from '@/store/dataStore.ts'
import slideStore from '@/store/slideStore.ts'

let loopInterval: null | ReturnType<typeof setInterval> = null;
let fontLoadInterval: null | ReturnType<typeof setInterval> = null;
const fontLoaded = ref(false);
const FONT = '16px Montserrat';

const content = computed(() => {
    return dataStore.data[slideStore.index];
});

const showProgressLabel = computed<boolean>(() => {
    return slideStore.progress === ProgressType.Label;
});

const buildQueryParams = () : QueryParams => {
    const query: QueryParams = { };

    if (slideStore.index > 0) {
        query.index = slideStore.index;
    }

    if (showProgressLabel.value) {
        query.progress = ProgressType.Label;
    }

    return query;
};

const checkAndClearLoopInterval = () : void => {
    if (!loopInterval) {
        return;
    }

    clearInterval(Number(loopInterval));
};

const incrementContent = (count: number) : void => {
    slideStore.increment(count);

    // TODO: Either implement query params, or remove support for progress type
    // const query = buildQueryParams();

    const url = new URL(location.toString());

    if (slideStore.index > 0) {
        url.searchParams.set('index', slideStore.index.toString());
    } else {
        url.searchParams.delete('index');
    }

    history.replaceState({}, "", url);
};

const bindKeyDown = (event: KeyboardEvent): void => {
    const { key } = event;

    if (key == Keys.ENTER || key == Keys.SPACE) {
        var next = document.getElementById('next');
        var previous = document.getElementById('previous');

        if (document.activeElement === next || document.activeElement === previous) {
            return;
        }
    }

    if (Keys.ALL_APP_KEYS.includes(key)) {
        checkAndClearLoopInterval();
    }

    if (Keys.INCREMENTORS.includes(key)) {
        incrementContent(1);
    } else if (Keys.DECREMENTORS.includes(key)) {
        incrementContent(-1);
    } else if (Keys.LARGE_INCREMENTORS.includes(key)) {
        incrementContent(5);
    } else if (Keys.LARGE_DECREMENTORS.includes(key)) {
        incrementContent(-5);
    } else if (key === Keys.DOLLAR_SIGN) {
        incrementContent(dataStore.data.length);
    } else if (key === Keys.ZERO) {
        incrementContent(-1 * dataStore.data.length);
    }
};

onMounted(() => {
    window.addEventListener('keydown',  bindKeyDown)

    // This delay is to allow the font to download prior to textFit being run on the
    // SlideContent component. Otherwise, the text won't be properly sized.
    fontLoadInterval = setInterval(() => {
        if (document.fonts && !document.fonts.check(FONT)) {
            return;
        }

        fontLoaded.value = true;
        clearInterval(Number(fontLoadInterval));
    }, 50);

    if (!slideStore.canLoop()) {
        return;
    }

    loopInterval = setInterval(() => {
        if (slideStore.isEnd()) {
            incrementContent(-1 * dataStore.data.length);
            return;
        }

        incrementContent(1)
    }, slideStore.loop * 1000);
});

onUnmounted(() => {
    window.removeEventListener('keydown',  bindKeyDown);
    checkAndClearLoopInterval();
});
</script>

<template>
    <div class="slide-view w-full h-[100dvh] flex justify-center items-center">
        <SlideContent :key="content" v-if="fontLoaded" :content="content" />
        <SlideArrows
            @next="incrementContent(1)"
            @previous="incrementContent(-1)"
            :is-first-slide="slideStore.isStart()"
            :is-last-slide="slideStore.isEnd()"
            />
        <ProgressLabel v-if="showProgressLabel" />
        <ProgressBar v-else />
    </div>
</template>
