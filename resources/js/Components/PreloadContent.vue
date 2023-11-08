<script setup lang="ts">
import dataStore from '../store/dataStore.ts'
</script>

<template>
  <aside
    class="absolute right-[200%] w-0 h-0 overflow-hidden"
    aria-hidden="true"
    inert
  >
    <div
      v-for="(slide, index) in dataStore.data"
      :key="index"
      class="absolute top-0 left-0"
      v-html="slide"
    ></div>
  </aside>
</template>
