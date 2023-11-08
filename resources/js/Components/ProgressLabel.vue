<script setup lang="ts">
import { computed } from 'vue'

import dataStore from '../store/dataStore.ts'
import slideStore from '../store/slideStore.ts'

const label = computed<string>(() => {
  return `${slideStore.index + 1} / ${dataStore.data.length}`;
});
</script>

<template>
  <aside class="
    [ text-black bg-gray-300 ]
    [ fixed bottom-0 left-1/2 -ml-12 ]
    [ h-8 w-24 ]
    [ flex justify-center items-center ]
  ">
    <p>{{ label }}</p>
  </aside>
</template>
