<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

import AppHead from '@/Components/AppHead.vue';
import CogIcon from '@/Components/icons/CogIcon.vue';
import PreloadContent from '@/Components/PreloadContent.vue';
import SlideView from '@/Components/SlideView.vue';
import dataStore from '@/store/dataStore.ts'

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
            browsershot-hide fixed top-6 right-8 z-10
            text-5xl text-gray-300/50
            hover:text-gray-300 focus:text-gray-300
            "
            ><CogIcon /></Link>
        <div v-if="dataStore.data.length > 0">
            <PreloadContent />
            <slot />
        </div>
    </main>
</template>
