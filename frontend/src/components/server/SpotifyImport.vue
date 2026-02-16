<script setup lang="ts">
import { onMounted } from 'vue'
import { useSpotifyStore } from '../../stores/spotify'

const props = defineProps<{ sessid: string }>()
const spotify = useSpotifyStore()

onMounted(async () => {
  // Handle OAuth callback
  spotify.handleCallback()
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
  await spotify.loadPlaylistTracks(tracksUrl)
}

function convertAll() {
  spotify.convertAndAdd(props.sessid)
}
</script>

<template>
  <div>
    <!-- Connect / Import button -->
    <button
      v-if="spotify.currentStep === 'idle' && !spotify.importing"
      class="btn-neon btn-neon-green text-xs !px-4 !py-2 w-full"
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
        <h4 class="font-display text-xs text-neon-green">Vos playlists Spotify</h4>
        <button
          class="text-white/40 hover:text-white text-xs cursor-pointer"
          @click="spotify.reset()"
        >
          &#10005;
        </button>
      </div>

      <div v-if="spotify.loading" class="p-4 text-center">
        <span class="font-mono text-sm text-neon-green animate-glow-pulse">Chargement...</span>
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
            <p class="text-sm text-white truncate">{{ pl.name }}</p>
            <p class="text-xs font-mono text-white/30">{{ pl.tracks.total }} titres</p>
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
        <h4 class="font-display text-xs text-neon-green">{{ spotify.tracks.length }} titres</h4>
        <div class="flex gap-2">
          <button
            class="text-xs font-mono text-white/40 hover:text-white cursor-pointer"
            @click="spotify.reset(); spotify.loadPlaylists()"
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
          v-for="(item, i) in spotify.tracks"
          :key="i"
          class="px-4 py-1.5 text-xs font-mono text-white/50 border-b border-white/5"
        >
          {{ item.track?.artists?.[0]?.name }} - {{ item.track?.name }}
        </div>
      </div>

      <div class="p-3">
        <button
          class="btn-neon btn-neon-green text-xs !px-4 !py-2 w-full"
          @click="convertAll"
        >
          Ajouter tout a la playlist
        </button>
      </div>
    </div>

    <!-- Converting progress -->
    <div
      v-if="spotify.currentStep === 'converting' || spotify.importing"
      class="mt-3 bg-surface-card border border-neon-green/30 rounded-xl p-4"
    >
      <div class="flex items-center justify-between mb-2">
        <span class="font-display text-xs text-neon-green">Conversion en cours...</span>
        <span class="font-mono text-xs text-white/50">
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
        <p class="font-mono text-xs text-neon-pink">
          {{ spotify.importErrors.length }} erreur(s)
        </p>
      </div>
    </div>
  </div>
</template>
