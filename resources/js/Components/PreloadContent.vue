<script setup lang="ts">
import { onMounted } from 'vue'
import dataStore from '@/store/dataStore.ts'

// Preload emoji fonts in the background by rendering to canvas
onMounted(() => {
  // Use requestIdleCallback if available, otherwise setTimeout
  const schedulePreload = (callback: () => void) => {
    if ('requestIdleCallback' in window) {
      requestIdleCallback(callback)
    } else {
      setTimeout(callback, 1)
    }
  }

  schedulePreload(() => {
    const canvas = document.createElement('canvas')
    canvas.width = 100
    canvas.height = 100
    const ctx = canvas.getContext('2d', { willReadFrequently: false })

    if (!ctx) return

    // Just render a few emojis to trigger the system emoji font to load
    // Once the font file loads, ALL emojis will be available
    const triggerEmojis = ['😀', '👍', '❤️', '⭐', '🎉']

    // Render at a few different sizes to ensure font is fully loaded
    const sizes = ['16px', '32px', '48px']

    sizes.forEach((size, sizeIndex) => {
      ctx.font = `${size} system-ui, -apple-system, "Apple Color Emoji", "Segoe UI Emoji", sans-serif`
      triggerEmojis.forEach((emoji, emojiIndex) => {
        ctx.fillText(emoji, emojiIndex * 20, (sizeIndex + 1) * 25)
      })
    })

    // Force a composite operation to ensure rendering completes
    ctx.getImageData(0, 0, 1, 1)
  })
})
</script>

<template>
  <aside
    class="absolute right-[200%] w-0 h-0 overflow-hidden"
    aria-hidden="true"
    inert
  >
    <!-- Preload all slide images and content -->
    <div
      v-for="(slide, index) in dataStore.data"
      :key="index"
      class="absolute top-0 left-0"
      v-html="slide"
    ></div>
  </aside>
</template>
