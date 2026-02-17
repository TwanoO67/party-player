<script setup lang="ts">
import { useRouter } from 'vue-router'

const router = useRouter()
</script>

<template>
  <header class="bg-surface-card/80 backdrop-blur-sm border-b border-neon-purple/30 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center gap-3">
      <button
        class="flex items-center gap-3 cursor-pointer"
        @click="router.push('/')"
      >
        <div class="w-10 h-10 rounded-full border-2 border-neon-pink flex items-center justify-center shadow-neon-pink">
          <span class="text-neon-pink text-lg">&#9654;</span>
        </div>
        <h1 class="font-display text-base md:text-lg">
          <span class="text-neon-pink text-glow-pink">Party</span>
          <span class="text-neon-cyan text-glow-cyan ml-1">Player</span>
        </h1>
      </button>
      <span class="text-white/40 text-base md:text-lg font-mono hidden sm:inline ml-2">
        le Jukebox de vos soirées!
      </span>
    </div>
  </header>
</template>
