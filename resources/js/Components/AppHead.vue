<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3'

import {
    BASE_TITLE,
} from '@/constants/meta.ts';
import Meta from '@/interfaces/meta.ts';

const { meta } = usePage<{
    meta?: Meta
    auth: any // Unused
}>().props;

</script>

<template>
  <Head :title="meta?.title || BASE_TITLE">
    <slot />
  </Head>
</template>
