<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3'

import {
    BASE_TITLE,
    BASE_DESCRIPTION,
    BASE_OG_IMAGE_URL,
    BASE_SITE,
    TWITTER_HANDLE,
} from '@/constants/meta.ts';
import Meta from '@/interfaces/meta.ts';

const { meta } = usePage<{
    meta?: Meta
    auth: any // Unused
}>().props;

</script>

<template>
  <Head :title="meta?.title || BASE_TITLE">
    <meta name="description" :content="meta?.description || BASE_DESCRIPTION">

    <!-- Open Graph -->
    <meta property="og:title" :content="meta?.title || BASE_TITLE">
    <meta property="og:description" :content="meta?.description || BASE_DESCRIPTION">
    <meta property="og:image" :content="meta?.imageUrl || BASE_OG_IMAGE_URL">
    <meta property="og:url" :content="BASE_SITE">
    <meta property="og:type" content="website">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" :content="TWITTER_HANDLE">
    <meta name="twitter:creator" :content="TWITTER_HANDLE">

    <slot />
  </Head>
</template>
