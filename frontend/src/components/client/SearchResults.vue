<script setup lang="ts">
import { ref } from 'vue'
import type { YouTubeSearchResult } from '../../types/youtube'

defineProps<{
  results: YouTubeSearchResult[]
  loading: boolean
}>()

const emit = defineEmits<{
  add: [videoId: string]
}>()

const addedIds = ref<Set<string>>(new Set())

function handleAdd(videoId: string) {
  addedIds.value.add(videoId)
  emit('add', videoId)
}
</script>

<template>
  <div class="mt-3">
    <!-- Loading -->
    <div v-if="loading" class="text-center py-4">
      <span class="font-mono text-base text-neon-cyan animate-glow-pulse">Recherche...</span>
    </div>

    <!-- Results list -->
    <div v-else-if="results.length" class="space-y-1.5">
      <div
        v-for="item in results"
        :key="item.id.videoId"
        class="flex items-center gap-3 bg-surface-card border border-white/5 rounded-lg p-2 hover:border-neon-cyan/30 transition-colors"
      >
        <!-- Thumbnail -->
        <img
          :src="item.snippet.thumbnails.default.url"
          :alt="item.snippet.title"
          class="w-16 h-12 object-cover rounded shrink-0"
        />

        <!-- Info -->
        <div class="flex-1 min-w-0">
          <p class="text-base font-body text-white truncate">
            {{ item.snippet.title }}
          </p>
          <p class="text-sm font-mono text-white/30 truncate">
            {{ item.snippet.channelTitle }}
          </p>
        </div>

        <!-- Add button -->
        <button
          v-if="!addedIds.has(item.id.videoId!)"
          class="w-10 h-10 rounded-full flex items-center justify-center bg-neon-green/10 text-neon-green hover:bg-neon-green/30 hover:shadow-neon-green transition-all cursor-pointer text-lg shrink-0"
          title="Ajouter"
          @click="handleAdd(item.id.videoId!)"
        >
          +
        </button>
        <span
          v-else
          class="w-10 h-10 rounded-full flex items-center justify-center bg-neon-green/20 text-neon-green text-sm shrink-0"
        >
          &#10003;
        </span>
      </div>
    </div>

    <!-- No results -->
    <div v-else-if="!loading" class="text-center py-4">
      <p class="font-mono text-base text-white/30">Aucun résultat</p>
    </div>
  </div>
</template>
