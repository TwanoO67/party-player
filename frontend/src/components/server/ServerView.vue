<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useSessionStore } from '../../stores/session'
import { usePlaylistStore } from '../../stores/playlist'
import { usePlayerStore } from '../../stores/player'
import { getVideoInfo } from '../../services/youtube'
import { createSession } from '../../services/api'
import VideoPlayer from './VideoPlayer.vue'
import QRCodePanel from './QRCodePanel.vue'
import SpotifyImport from './SpotifyImport.vue'
import PlaylistItem from '../shared/PlaylistItem.vue'

const props = defineProps<{ sessid: string }>()

const sessionStore = useSessionStore()
const playlistStore = usePlaylistStore()
const playerStore = usePlayerStore()

const videoPlayer = ref<InstanceType<typeof VideoPlayer> | null>(null)
const lastPlayedId = ref('')

onMounted(async () => {
  sessionStore.setSession(props.sessid, 'server')
  // Try to read first; only create if session doesn't exist
  const res = await playlistStore.fetchPlaylist(props.sessid)
  if (playlistStore.lastUpdateTime === 0) {
    await createSession(props.sessid)
  }
  playlistStore.startPolling(props.sessid, 5000)
})

onBeforeUnmount(() => {
  playlistStore.stopPolling()
})

async function playTrack(videoId: string) {
  lastPlayedId.value = videoId
  try {
    const res = await getVideoInfo(videoId)
    const title = res.items?.[0]?.snippet?.title || videoId
    playerStore.setCurrentTrack(videoId, title)
  } catch {
    playerStore.setCurrentTrack(videoId, videoId)
  }
  videoPlayer.value?.loadVideo(videoId)
  await playlistStore.markRead(props.sessid, videoId)
  await playlistStore.fetchPlaylist(props.sessid)
}

async function loadNextTrack() {
  const res = await playlistStore.getNextTrack(props.sessid, lastPlayedId.value)
  if (res.result === 'success' && res.content) {
    playTrack(res.content as string)
  } else {
    playerStore.clear()
  }
}

async function handleDelete(videoId: string) {
  await playlistStore.deleteTrack(props.sessid, videoId, sessionStore.username)
}

async function handleUnreadAll() {
  await playlistStore.unreadAll(props.sessid)
}
</script>

<template>
  <div class="max-w-7xl mx-auto px-4 py-4">
    <div class="flex flex-col lg:flex-row gap-4">
      <!-- Left column: Video Player -->
      <div class="flex-1 min-w-0">
        <!-- Title -->
        <h2
          v-if="playerStore.currentTitle"
          class="font-display text-xs md:text-sm text-neon-pink text-glow-pink mb-3 truncate"
        >
          {{ playerStore.currentTitle }}
        </h2>
        <h2
          v-else
          class="font-display text-xs md:text-sm text-white/40 mb-3"
        >
          En attente de musique...
        </h2>

        <VideoPlayer
          ref="videoPlayer"
          @ended="loadNextTrack"
        />

        <!-- Controls under player -->
        <div class="flex gap-2 mt-3">
          <button
            class="btn-neon btn-neon-cyan text-xs !px-4 !py-2"
            @click="loadNextTrack"
          >
            &#9654;&#9654; Suivant
          </button>
          <button
            class="btn-neon btn-neon-purple text-xs !px-4 !py-2"
            @click="handleUnreadAll"
          >
            &#8634; Tout relire
          </button>
        </div>
      </div>

      <!-- Right column: QR + Playlist -->
      <div class="w-full lg:w-96 flex flex-col gap-4 shrink-0">
        <QRCodePanel :sessid="sessid" />

        <!-- Spotify Import -->
        <SpotifyImport :sessid="sessid" />

        <!-- Playlist -->
        <div class="bg-surface-card border border-neon-purple/30 rounded-xl overflow-hidden flex-1">
          <div class="px-4 py-3 border-b border-neon-purple/20">
            <h3 class="font-display text-xs text-neon-purple">
              Playlist
            </h3>
          </div>

          <div class="max-h-96 overflow-y-auto">
            <!-- Unread items -->
            <div v-if="playlistStore.unreadItems.length" class="p-2 space-y-1">
              <PlaylistItem
                v-for="item in playlistStore.unreadItems"
                :key="item.id"
                :item="item"
                mode="server"
                :is-playing="item.id === playerStore.currentVideoId"
                @play="playTrack"
                @delete="handleDelete"
              />
            </div>

            <!-- Separator -->
            <div
              v-if="playlistStore.readItems.length"
              class="px-4 py-1.5 bg-surface-elevated/50 text-center"
            >
              <span class="font-mono text-xs text-white/30">Deja lu</span>
            </div>

            <!-- Read items -->
            <div v-if="playlistStore.readItems.length" class="p-2 space-y-1">
              <PlaylistItem
                v-for="item in playlistStore.readItems"
                :key="item.id"
                :item="item"
                mode="server"
                :is-playing="item.id === playerStore.currentVideoId"
                @play="playTrack"
                @delete="handleDelete"
              />
            </div>

            <!-- Empty state -->
            <div
              v-if="!playlistStore.items.length"
              class="p-8 text-center"
            >
              <p class="font-mono text-sm text-white/30">
                Playlist vide
              </p>
              <p class="font-mono text-xs text-white/20 mt-2">
                Scannez le QR code pour ajouter des musiques
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
