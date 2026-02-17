<script setup lang="ts">
import { onMounted } from 'vue'
import { useYouTubePlayer } from '../../composables/useYouTubePlayer'
import { usePlayerStore } from '../../stores/player'

const playerStore = usePlayerStore()
const { ready, onEnded, init, loadVideo } = useYouTubePlayer('yt-player')

const emit = defineEmits<{
  ended: []
}>()

onEnded.value = () => emit('ended')

onMounted(async () => {
  await init()
})

defineExpose({ loadVideo, ready })
</script>

<template>
  <div class="crt-effect border-2 border-neon-cyan/40 shadow-neon-cyan">
    <!-- Video container with 16:9 aspect ratio -->
    <div class="relative w-full" style="padding-bottom: 56.25%;">
      <div id="yt-player" class="absolute inset-0"></div>
    </div>

    <!-- Now Playing bar -->
    <div
      v-if="playerStore.currentTitle"
      class="bg-surface-card/90 px-4 py-2 flex items-center gap-3 border-t border-neon-cyan/20"
    >
      <span
        class="w-3 h-3 rounded-full shrink-0"
        :class="playerStore.isPlaying ? 'bg-neon-green animate-glow-pulse' : 'bg-neon-yellow'"
      ></span>
      <span class="font-mono text-base text-neon-cyan truncate">
        {{ playerStore.currentTitle }}
      </span>
    </div>
  </div>
</template>
