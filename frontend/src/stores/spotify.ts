import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import * as spotifyService from '../services/spotify'
import { searchVideos, YouTubeQuotaError } from '../services/youtube'
import { usePlaylistStore } from './playlist'
import { useSessionStore } from './session'
import type { SpotifyUser, SpotifyPlaylist, SpotifyTrack } from '../types/spotify'

export const useSpotifyStore = defineStore('spotify', () => {
  const token = ref(spotifyService.getSpotifyToken())
  const user = ref<SpotifyUser | null>(null)
  const playlists = ref<SpotifyPlaylist[]>([])
  const tracks = ref<SpotifyTrack[]>([])
  const importing = ref(false)
  const importProgress = ref(0)
  const importTotal = ref(0)
  const importErrors = ref<string[]>([])
  const currentStep = ref<'idle' | 'playlists' | 'tracks' | 'converting'>('idle')
  const loading = ref(false)
  const trackStates = ref<Record<string, 'loading' | 'done' | 'error'>>({})
  const quotaExceeded = ref(false)


  const isConnected = computed(() => !!token.value && !!user.value)
  const isConfigured = computed(() => spotifyService.isSpotifyConfigured())

  async function init() {
    if (!token.value) return
    try {
      loading.value = true
      user.value = await spotifyService.fetchUserProfile(token.value)
    } catch {
      token.value = ''
      user.value = null
      spotifyService.clearSpotifyToken()
    } finally {
      loading.value = false
    }
  }

  async function authorize() {
    await spotifyService.authorizeSpotify(window.location.href)
  }

  function disconnect() {
    token.value = ''
    user.value = null
    playlists.value = []
    tracks.value = []
    currentStep.value = 'idle'
    spotifyService.clearSpotifyToken()
  }

  async function loadPlaylists() {
    if (!token.value) return
    loading.value = true
    currentStep.value = 'playlists'
    try {
      const allPlaylists: SpotifyPlaylist[] = []
      let url: string | undefined
      do {
        const res = await spotifyService.fetchUserPlaylists(token.value, url)
        allPlaylists.push(...res.items)
        url = res.next || undefined
      } while (url)
      playlists.value = allPlaylists
    } catch (e) {
      console.error('Failed to load playlists:', e)
    } finally {
      loading.value = false
    }
  }

  async function loadPlaylistTracks(tracksUrl: string) {
    if (!token.value) return
    loading.value = true
    currentStep.value = 'tracks'
    try {
      const allTracks: SpotifyTrack[] = []
      let url: string | null = tracksUrl
      while (url) {
        const res = await spotifyService.fetchPlaylistTracks(token.value, url)
        allTracks.push(...res.items)
        url = res.next
      }
      tracks.value = allTracks
    } catch (e) {
      console.error('Failed to load tracks:', e)
    } finally {
      loading.value = false
    }
  }

  async function convertAndAdd(sessid: string) {
    const playlistStore = usePlaylistStore()
    const sessionStore = useSessionStore()

    importing.value = true
    currentStep.value = 'converting'
    importTotal.value = tracks.value.length
    importProgress.value = 0
    importErrors.value = []
    quotaExceeded.value = false

    for (const item of tracks.value) {
      const track = item.track
      if (!track || !track.name || !track.artists?.length) {
        importProgress.value++
        continue
      }

      const query = `${track.artists[0].name} - ${track.name}`
      try {
        const results = await searchVideos(query, 3)
        if (results.items?.length) {
          const videoId = results.items[0].id.videoId
          if (videoId) {
            await playlistStore.addTrack(sessid, videoId, sessionStore.username)
          }
        } else {
          importErrors.value.push(query)
        }
      } catch (e) {
        if (e instanceof YouTubeQuotaError) {
          quotaExceeded.value = true
          break
        }
        importErrors.value.push(query)
      }
      importProgress.value++

      // Small delay to avoid rate limiting
      await new Promise((r) => setTimeout(r, 300))
    }

    importing.value = false
    currentStep.value = 'idle'
  }

  async function convertAndAddSingle(sessid: string, trackId: string) {
    const item = tracks.value.find((t) => t.track?.id === trackId)
    const track = item?.track
    if (!track?.name || !track.artists?.length) return

    const playlistStore = usePlaylistStore()
    const sessionStore = useSessionStore()
    const query = `${track.artists[0].name} - ${track.name}`
    trackStates.value[trackId] = 'loading'
    try {
      const results = await searchVideos(query, 3)
      const videoId = results.items?.[0]?.id?.videoId
      if (videoId) {
        await playlistStore.addTrack(sessid, videoId, sessionStore.username)
        trackStates.value[trackId] = 'done'
      } else {
        trackStates.value[trackId] = 'error'
      }
    } catch (e) {
      if (e instanceof YouTubeQuotaError) quotaExceeded.value = true
      trackStates.value[trackId] = 'error'
    }
  }

  function reset() {
    tracks.value = []
    playlists.value = []
    currentStep.value = 'idle'
    importProgress.value = 0
    importTotal.value = 0
    importErrors.value = []
    trackStates.value = {}
    quotaExceeded.value = false
  }

  return {
    token,
    user,
    playlists,
    tracks,
    importing,
    importProgress,
    importTotal,
    importErrors,
    currentStep,
    loading,
    isConnected,
    isConfigured,
    init,
    authorize,
    disconnect,
    trackStates,
    quotaExceeded,
    loadPlaylists,
    loadPlaylistTracks,
    convertAndAdd,
    convertAndAddSingle,
    reset,
  }
})
