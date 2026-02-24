<script setup lang="ts">
import { ref } from 'vue'
import SearchResults from './SearchResults.vue'
import { searchVideos, YouTubeQuotaError } from '../../services/youtube'
import type { YouTubeSearchResult } from '../../types/youtube'

const emit = defineEmits<{
  add: [videoId: string]
}>()

const query = ref('')
const results = ref<YouTubeSearchResult[]>([])
const searching = ref(false)
const searched = ref(false)
const quotaExceeded = ref(false)

async function search() {
  const q = query.value.trim()
  if (!q) return

  searching.value = true
  searched.value = true
  quotaExceeded.value = false
  try {
    const res = await searchVideos(q)
    results.value = res.items || []
  } catch (e) {
    results.value = []
    if (e instanceof YouTubeQuotaError) {
      quotaExceeded.value = true
    } else {
      console.error('Search failed:', e)
    }
  } finally {
    searching.value = false
  }
}

async function handleAdd(videoId: string) {
  emit('add', videoId)
  // Remove from results after adding
  results.value = results.value.filter((r) => r.id.videoId !== videoId)
}

function clear() {
  query.value = ''
  results.value = []
  searched.value = false
}
</script>

<template>
  <div>
    <!-- Search input -->
    <div class="flex gap-2">
      <div class="flex-1 relative">
        <input
          v-model="query"
          type="text"
          placeholder="Rechercher un artiste ou un titre..."
          class="w-full bg-surface-card border border-neon-cyan/30 rounded-lg pl-4 pr-10 py-3 text-white font-mono text-base focus:outline-none focus:border-neon-cyan focus:shadow-neon-cyan transition-all"
          @keyup.enter="search"
        />
        <button
          v-if="query"
          class="absolute right-3 top-1/2 -translate-y-1/2 text-white/30 hover:text-white cursor-pointer"
          @click="clear"
        >
          &#10005;
        </button>
      </div>
      <button
        class="btn-neon btn-neon-cyan !px-4 !py-0"
        :disabled="searching"
        @click="search"
      >
        {{ searching ? '...' : '&#128269;' }}
      </button>
    </div>

    <!-- Quota exceeded -->
    <div v-if="quotaExceeded" class="mt-3 text-center py-4 px-3 bg-neon-pink/10 border border-neon-pink/30 rounded-lg">
      <p class="font-mono text-base text-neon-pink">Quota YouTube dépassé</p>
      <p class="font-mono text-sm text-white/40 mt-1">La recherche est temporairement indisponible. Réessaie demain.</p>
    </div>

    <!-- Results -->
    <SearchResults
      v-else-if="searched"
      :results="results"
      :loading="searching"
      @add="handleAdd"
    />
  </div>
</template>
