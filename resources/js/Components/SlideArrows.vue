<script setup lang="ts">

defineProps<{
  isFirstSlide: boolean,
  isLastSlide: boolean,
}>();

const emit = defineEmits<{
  (e: 'next'): void
  (e: 'previous'): void
}>();

</script>

<template>
  <section>
    <button
      v-if="!isFirstSlide"
      id="previous"
      type="button"
      @click="emit('previous')"
      title="Go back 1 slide"
      class="
        browsershot-hide slide-arrow-button left-6
        border-r-[30px] border-r-gray-300/50 hover:border-r-gray-300"
    ></button>
    <button
      v-if="!isLastSlide"
      id="next"
      type="button"
      @click="emit('next')"
      title="Go forward 1 slide"
      class="
        browsershot-hide slide-arrow-button right-6
        border-l-[30px] border-l-gray-300/50 hover:border-l-gray-300"
    ></button>
  </section>
</template>
