<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import type { PlaylistItem } from '../../types/playlist'
import { getVideoInfo } from '../../services/youtube'

const props = defineProps<{
  item: PlaylistItem
  mode: 'server' | 'client'
  isPlaying?: boolean
}>()

const emit = defineEmits<{
  play: [id: string]
  delete: [id: string]
  vote: [id: string, value: 'plus' | 'moins']
}>()

const title = ref(props.item.id)
const thumbnail = ref('')

onMounted(async () => {
  try {
    const res = await getVideoInfo(props.item.id)
    if (res.items?.length) {
      title.value = res.items[0].snippet.title
      thumbnail.value = res.items[0].snippet.thumbnails.default.url
    }
  } catch {
    // Keep ID as fallback title
  }
})

const isRead = computed(() => props.item.alreadyRead !== false)
const dateStr = computed(() => {
  const d = new Date(props.item.addTime * 1000)
  return d.toLocaleDateString('fr-FR') + ' ' + d.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
})
</script>

<template>
  <div
    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all group"
    :class="[
      isRead ? 'bg-surface-dark/50 opacity-50' : 'bg-surface-card',
      isPlaying ? 'border border-neon-green/60 shadow-neon-green' : 'border border-transparent',
      !isRead && !isPlaying ? 'border-l-4 border-l-neon-cyan hover:bg-surface-elevated/50' : '',
    ]"
  >
    <!-- Thumbnail -->
    <img
      v-if="thumbnail"
      :src="thumbnail"
      :alt="title"
      class="w-12 h-9 object-cover rounded shrink-0 hidden sm:block"
    />

    <!-- Info -->
    <div class="flex-1 min-w-0">
      <p class="text-sm font-body text-white truncate">{{ title }}</p>
      <p class="text-xs font-mono text-white/30">
        {{ item.addUser }} - {{ dateStr }}
      </p>
    </div>

    <!-- Actions -->
    <div class="flex items-center gap-1.5 shrink-0">
      <!-- Server actions -->
      <template v-if="mode === 'server'">
        <button
          class="w-8 h-8 rounded-full flex items-center justify-center bg-neon-green/10 text-neon-green hover:bg-neon-green/30 transition-colors cursor-pointer text-sm"
          title="Lire"
          @click="emit('play', item.id)"
        >
          &#9654;
        </button>
        <button
          class="w-8 h-8 rounded-full flex items-center justify-center bg-neon-pink/10 text-neon-pink hover:bg-neon-pink/30 transition-colors cursor-pointer text-xs"
          title="Supprimer"
          @click="emit('delete', item.id)"
        >
          &#10005;
        </button>
      </template>

      <!-- Client vote actions -->
      <template v-if="mode === 'client' && !isRead">
        <button
          class="w-8 h-8 rounded-full flex items-center justify-center bg-neon-green/10 text-neon-green hover:bg-neon-green/30 transition-colors cursor-pointer text-sm"
          title="Vote +"
          @click="emit('vote', item.id, 'plus')"
        >
          &#9650;
        </button>
        <button
          class="w-8 h-8 rounded-full flex items-center justify-center bg-neon-pink/10 text-neon-pink hover:bg-neon-pink/30 transition-colors cursor-pointer text-sm"
          title="Vote -"
          @click="emit('vote', item.id, 'moins')"
        >
          &#9660;
        </button>
      </template>

      <!-- Vote count -->
      <span
        class="min-w-6 text-center font-mono text-xs px-1.5 py-0.5 rounded"
        :class="item.vote > 0 ? 'text-neon-green bg-neon-green/10' : item.vote < 0 ? 'text-neon-pink bg-neon-pink/10' : 'text-white/30'"
      >
        {{ item.vote > 0 ? '+' : '' }}{{ item.vote }}
      </span>
    </div>
  </div>
</template>
