<script setup lang="ts">
import { computed, CSSProperties } from 'vue'

import dataStore from '../store/dataStore.ts'
import slideStore from '../store/slideStore.ts'

const percentage = computed<number>(() => {
  if (dataStore.data.length <= 1) {
    return 0;
  }

  return slideStore.index / (dataStore.data.length - 1) * 100;
});

const percentageLabel = computed<string>(() => {
  return `${percentage.value}%`;
});

const style = computed<CSSProperties>(() => {
  return {
    width: percentageLabel.value,
  };
});
</script>

<template>
  <aside
    class="bg-gray-400/50 fixed bottom-0 left-0 h-2"
    :style="style"
  ></aside>
</template>
