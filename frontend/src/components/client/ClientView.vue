<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useSessionStore } from '../../stores/session'
import { usePlaylistStore } from '../../stores/playlist'
import { usePlayerStore } from '../../stores/player'
import SearchBar from './SearchBar.vue'
import PlaylistItem from '../shared/PlaylistItem.vue'

const props = defineProps<{ sessid: string }>()

const sessionStore = useSessionStore()
const playlistStore = usePlaylistStore()
const playerStore = usePlayerStore()

const showUsernamePrompt = ref(false)
const usernameInput = ref('')

onMounted(async () => {
  sessionStore.setSession(props.sessid, 'client')
  if (!sessionStore.username || sessionStore.username.startsWith('Guest')) {
    showUsernamePrompt.value = true
  }
  playlistStore.startPolling(props.sessid, 15000)
})

onBeforeUnmount(() => {
  playlistStore.stopPolling()
})

function saveUsername() {
  if (usernameInput.value.trim()) {
    sessionStore.setUsername(usernameInput.value.trim())
    showUsernamePrompt.value = false
  }
}

async function handleAddTrack(videoId: string) {
  return playlistStore.addTrack(props.sessid, videoId, sessionStore.username)
}

async function handleVote(id: string, value: 'plus' | 'moins') {
  await playlistStore.voteTrack(
    props.sessid,
    id,
    sessionStore.username,
    value,
    sessionStore.voteToken,
  )
}
</script>

<template>
  <div class="w-full px-4 py-4">
    <!-- Username prompt -->
    <div
      v-if="showUsernamePrompt"
      class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4"
    >
      <div class="bg-surface-card border border-neon-cyan/40 rounded-xl p-6 w-full max-w-sm border-glow-cyan">
        <h3 class="font-display text-sm text-neon-cyan mb-4 text-center">
          Votre nom ?
        </h3>
        <input
          v-model="usernameInput"
          type="text"
          placeholder="Entrez votre nom"
          class="w-full bg-surface-dark border border-neon-cyan/30 rounded-lg px-4 py-3 text-white font-mono text-center mb-4 focus:outline-none focus:border-neon-cyan focus:shadow-neon-cyan transition-all"
          autofocus
          @keyup.enter="saveUsername"
        />
        <button class="btn-neon btn-neon-cyan w-full" @click="saveUsername">
          C'est parti !
        </button>
      </div>
    </div>

    <!-- Header with session info -->
    <div class="flex items-center justify-between mb-4">
      <div>
        <h2 class="font-display text-sm text-neon-pink">
          JukeBox <span class="text-neon-cyan">{{ sessid }}</span>
        </h2>
        <button
          class="text-sm font-mono text-white/40 hover:text-neon-cyan transition-colors cursor-pointer mt-1"
          @click="showUsernamePrompt = true"
        >
          {{ sessionStore.username }} &#9998;
        </button>
      </div>
    </div>

    <!-- Search -->
    <SearchBar @add="handleAddTrack" />

    <!-- Now Playing -->
    <div
      v-if="playerStore.currentTitle"
      class="mt-4 bg-surface-card border border-neon-green/30 rounded-xl p-3 flex items-center gap-3"
    >
      <span class="w-3 h-3 rounded-full bg-neon-green animate-glow-pulse shrink-0"></span>
      <div class="min-w-0">
        <p class="text-sm font-mono text-white/40">En lecture</p>
        <p class="text-base font-body text-neon-green truncate">{{ playerStore.currentTitle }}</p>
      </div>
    </div>

    <!-- Playlist -->
    <div class="mt-4">
      <h3 class="font-display text-sm text-neon-purple mb-3">
        Playlist
      </h3>

      <div v-if="playlistStore.unreadItems.length" class="space-y-1.5">
        <PlaylistItem
          v-for="item in playlistStore.unreadItems"
          :key="item.id"
          :item="item"
          mode="client"
          @vote="handleVote"
        />
      </div>

      <!-- Read separator -->
      <div
        v-if="playlistStore.readItems.length"
        class="my-3 text-center"
      >
        <span class="font-mono text-sm text-white/20 bg-surface-dark px-3">Deja lu</span>
      </div>
      <div v-if="playlistStore.readItems.length" class="space-y-1.5 opacity-50">
        <PlaylistItem
          v-for="item in playlistStore.readItems"
          :key="item.id"
          :item="item"
          mode="client"
        />
      </div>

      <div
        v-if="!playlistStore.items.length"
        class="text-center py-8"
      >
        <p class="font-mono text-base text-white/30">Playlist vide</p>
        <p class="font-mono text-sm text-white/20 mt-1">Cherchez et ajoutez des musiques !</p>
      </div>
    </div>
  </div>
</template>
