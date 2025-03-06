<script setup lang="ts">
import { DRAFT_MESSAGE } from '@/constants/general.ts';
</script>

<template>
    <aside class="
        [ text-white text-sm bg-red-400 ]
        [ fixed top-0 left-1/2 -translate-x-1/2 ]
        [ px-4 py-2 ]
        [ flex justify-center items-center ]
    ">
        <p id="draft-message">{{ DRAFT_MESSAGE }}</p>
    </aside>
</template>
