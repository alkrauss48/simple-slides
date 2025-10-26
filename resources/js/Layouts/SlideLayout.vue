<script setup lang="ts">
import AppHead from '@/Components/AppHead.vue';
import PreloadContent from '@/Components/PreloadContent.vue';
import SettingsMenu from '@/Components/SettingsMenu.vue';
import dataStore from '@/store/dataStore.ts'

const props = defineProps<{
    auth?: any,
}>();
</script>

<template>
    <main>
        <AppHead />
        <SettingsMenu :auth="props.auth" />
        <div v-if="dataStore.data.length > 0">
            <PreloadContent />
            <slot />
        </div>
    </main>
</template>
