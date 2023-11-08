<script setup lang="ts">

const emit = defineEmits<{
  (e: 'next'): void
  (e: 'previous'): void
}>();

</script>

<template>
  <section>
    <button
      id="previous"
      type="button"
      @click="emit('previous')"
      title="Go back 1 slide"
      class="
        slide-arrow-button left-6
        border-r-[30px] border-r-gray-300/50 hover:border-r-gray-300"
    ></button>
    <button
      id="next"
      type="button"
      @click="emit('next')"
      title="Go forward 1 slide"
      class="
        slide-arrow-button right-6
        border-l-[30px] border-l-gray-300/50 hover:border-l-gray-300"
    ></button>
  </section>
</template>
