<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
import { useSpotifyStore } from '../../stores/spotify'

const props = defineProps<{ sessid: string }>()
const spotify = useSpotifyStore()

const selectedTrackIds = ref<Set<string>>(new Set())

const MAX_SELECTION = 5

onMounted(async () => {
  // Init connection if token exists
  await spotify.init()
})

function startImport() {
  if (spotify.isConnected) {
    spotify.loadPlaylists()
  } else {
    spotify.authorize()
  }
}

async function selectPlaylist(tracksUrl: string) {
  selectedTrackIds.value = new Set()
  await spotify.loadPlaylistTracks(tracksUrl)
}

function toggleTrack(trackId: string) {
  const set = selectedTrackIds.value
  if (set.has(trackId)) {
    set.delete(trackId)
  } else if (set.size < MAX_SELECTION) {
    set.add(trackId)
  }
  // Force reactivity
  selectedTrackIds.value = new Set(set)
}

const selectionCount = computed(() => selectedTrackIds.value.size)
const selectionFull = computed(() => selectedTrackIds.value.size >= MAX_SELECTION)

async function importSelected() {
  for (const trackId of selectedTrackIds.value) {
    await spotify.convertAndAddSingle(props.sessid, trackId)
  }
  selectedTrackIds.value = new Set()
}
</script>

<template>
  <div>
    <!-- Connect / Import button -->
    <button
      v-if="spotify.currentStep === 'idle' && !spotify.importing"
      class="btn-neon btn-neon-green text-sm !px-4 !py-2 w-full"
      :disabled="!spotify.isConfigured"
      @click="startImport"
    >
      <template v-if="!spotify.isConfigured">Spotify non configure</template>
      <template v-else-if="spotify.isConnected">
        &#9834; Importer depuis Spotify
      </template>
      <template v-else>
        &#9834; Connexion Spotify
      </template>
    </button>

    <!-- Playlists list -->
    <div
      v-if="spotify.currentStep === 'playlists'"
      class="mt-3 bg-surface-card border border-neon-green/30 rounded-xl overflow-hidden"
    >
      <div class="px-4 py-3 border-b border-neon-green/20 flex items-center justify-between">
        <h4 class="font-display text-sm text-neon-green">Vos playlists Spotify</h4>
        <button
          class="text-white/40 hover:text-white text-xs cursor-pointer"
          @click="spotify.reset()"
        >
          &#10005;
        </button>
      </div>

      <div v-if="spotify.loading" class="p-4 text-center">
        <span class="font-mono text-base text-neon-green animate-glow-pulse">Chargement...</span>
      </div>

      <div v-else class="max-h-64 overflow-y-auto">
        <button
          v-for="pl in spotify.playlists"
          :key="pl.id"
          class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-surface-elevated/50 transition-colors text-left cursor-pointer"
          @click="selectPlaylist(pl.tracks.href)"
        >
          <img
            v-if="pl.images?.length"
            :src="pl.images[0].url"
            class="w-10 h-10 rounded object-cover shrink-0"
          />
          <div
            v-else
            class="w-10 h-10 rounded bg-neon-green/10 flex items-center justify-center shrink-0"
          >
            <span class="text-neon-green">&#9834;</span>
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-base text-white truncate">{{ pl.name }}</p>
            <p class="text-sm font-mono text-white/30">{{ pl.tracks.total }} titres</p>
          </div>
        </button>
      </div>
    </div>

    <!-- Tracks list -->
    <div
      v-if="spotify.currentStep === 'tracks'"
      class="mt-3 bg-surface-card border border-neon-green/30 rounded-xl overflow-hidden"
    >
      <div class="px-4 py-3 border-b border-neon-green/20 flex items-center justify-between">
        <h4 class="font-display text-sm text-neon-green">{{ spotify.tracks.length }} titres</h4>
        <div class="flex gap-2">
          <button
            class="text-xs font-mono text-white/40 hover:text-white cursor-pointer"
            @click="selectedTrackIds = new Set(); spotify.reset(); spotify.loadPlaylists()"
          >
            &#8592; Retour
          </button>
          <button
            class="text-white/40 hover:text-white text-xs cursor-pointer"
            @click="spotify.reset()"
          >
            &#10005;
          </button>
        </div>
      </div>

      <div class="max-h-48 overflow-y-auto">
        <div
          v-for="item in spotify.tracks"
          :key="item.track?.id"
          class="flex items-center gap-2 px-4 py-1.5 border-b border-white/5"
          :class="{ 'opacity-40': spotify.trackStates[item.track?.id ?? ''] === 'done' }"
        >
          <!-- Checkbox de sélection -->
          <button
            v-if="spotify.trackStates[item.track?.id ?? ''] !== 'done'"
            class="shrink-0 w-5 h-5 rounded border flex items-center justify-center transition-colors text-xs"
            :class="{
              'border-neon-green bg-neon-green text-black': selectedTrackIds.has(item.track?.id ?? ''),
              'border-white/20 text-transparent hover:border-neon-green/60': !selectedTrackIds.has(item.track?.id ?? '') && !selectionFull,
              'border-white/10 text-transparent cursor-not-allowed': !selectedTrackIds.has(item.track?.id ?? '') && selectionFull,
            }"
            :disabled="(!selectedTrackIds.has(item.track?.id ?? '') && selectionFull) || spotify.trackStates[item.track?.id ?? ''] === 'loading'"
            @click="item.track?.id && toggleTrack(item.track.id)"
          >✓</button>
          <!-- Icône état pour les titres déjà importés -->
          <span v-else class="shrink-0 w-5 h-5 flex items-center justify-center text-xs text-neon-green">✓</span>

          <span
            class="flex-1 text-sm font-mono truncate"
            :class="spotify.trackStates[item.track?.id ?? ''] === 'done' ? 'text-white/30' : 'text-white/50'"
          >
            {{ item.track?.artists?.[0]?.name }} - {{ item.track?.name }}
          </span>

          <!-- État loading/error -->
          <span
            v-if="spotify.trackStates[item.track?.id ?? ''] === 'loading'"
            class="shrink-0 text-xs text-white/30"
          >⏳</span>
          <button
            v-else-if="spotify.trackStates[item.track?.id ?? ''] === 'error'"
            class="shrink-0 text-xs text-neon-pink cursor-pointer"
            title="Réessayer"
            @click="item.track?.id && spotify.convertAndAddSingle(props.sessid, item.track.id)"
          >✕</button>
        </div>
      </div>

      <div class="px-3 py-2 border-t border-neon-green/10 flex items-center justify-between gap-2">
        <span class="font-mono text-xs text-white/30">{{ selectionCount }}/{{ MAX_SELECTION }} sélectionnés</span>
        <button
          class="btn-neon btn-neon-green text-sm !px-3 !py-1.5"
          :disabled="selectionCount === 0"
          @click="importSelected"
        >
          Importer ({{ selectionCount }})
        </button>
      </div>
    </div>

    <!-- Converting progress -->
    <div
      v-if="spotify.currentStep === 'converting' || spotify.importing"
      class="mt-3 bg-surface-card border border-neon-green/30 rounded-xl p-4"
    >
      <div class="flex items-center justify-between mb-2">
        <span class="font-display text-sm text-neon-green">Conversion en cours...</span>
        <span class="font-mono text-sm text-white/50">
          {{ spotify.importProgress }}/{{ spotify.importTotal }}
        </span>
      </div>

      <!-- Progress bar -->
      <div class="h-2 bg-surface-dark rounded-full overflow-hidden">
        <div
          class="h-full bg-neon-green rounded-full transition-all duration-300"
          :style="{ width: spotify.importTotal ? (spotify.importProgress / spotify.importTotal * 100) + '%' : '0%' }"
        ></div>
      </div>

      <div v-if="spotify.importErrors.length" class="mt-2">
        <p class="font-mono text-sm text-neon-pink">
          {{ spotify.importErrors.length }} erreur(s)
        </p>
      </div>
    </div>

    <!-- Quota exceeded (persistant, indépendant de l'étape en cours) -->
    <div
      v-if="spotify.quotaExceeded"
      class="mt-3 bg-neon-pink/10 border border-neon-pink/40 rounded-xl p-4"
    >
      <div class="flex items-start justify-between gap-2">
        <div>
          <p class="font-mono text-sm text-neon-pink">Quota YouTube dépassé</p>
          <p class="font-mono text-xs text-white/50 mt-1">
            {{ spotify.importProgress }}/{{ spotify.importTotal }} titres traités. La recherche YouTube est temporairement indisponible — réessaie demain.
          </p>
        </div>
        <button
          class="text-white/30 hover:text-white text-xs shrink-0 cursor-pointer"
          @click="spotify.reset()"
        >
          &#10005;
        </button>
      </div>
    </div>
  </div>
</template>
