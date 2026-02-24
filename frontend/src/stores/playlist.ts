import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import * as api from '../services/api'
import type { PlaylistItem, PlaylistData } from '../types/playlist'

export const usePlaylistStore = defineStore('playlist', () => {
  const items = ref<PlaylistItem[]>([])
  const lastUpdateTime = ref(0)
  const loading = ref(false)
  const error = ref('')
  let pollingInterval: ReturnType<typeof setInterval> | null = null

  const unreadItems = computed(() =>
    items.value.filter((i) => i.alreadyRead === false),
  )
  const readItems = computed(() =>
    items.value.filter((i) => i.alreadyRead !== false),
  )

  async function fetchPlaylist(sessid: string) {
    try {
      const res = await api.readPlaylist(sessid, lastUpdateTime.value)
      if (res.result === 'success' && res.content && res.content !== 'no_news') {
        const data = res.content as PlaylistData
        const raw = data.items
        items.value = Array.isArray(raw) ? raw : Object.values(raw ?? {})
        lastUpdateTime.value = data.lastUpdateTime || 0
      }
    } catch (e) {
      error.value = String(e)
    }
  }

  async function addTrack(sessid: string, id: string, user: string) {
    loading.value = true
    try {
      const res = await api.addTrack(sessid, id, user)
      if (res.result === 'success') {
        await fetchPlaylist(sessid)
      }
      return res
    } finally {
      loading.value = false
    }
  }

  async function deleteTrack(sessid: string, id: string, user: string) {
    const res = await api.deleteTrack(sessid, id, user)
    if (res.result === 'success') {
      await fetchPlaylist(sessid)
    }
    return res
  }

  async function voteTrack(
    sessid: string,
    itemId: string,
    user: string,
    vote: 'plus' | 'moins',
    voteToken: string,
  ) {
    const res = await api.vote(sessid, itemId, user, vote, voteToken)
    if (res.result === 'success') {
      await fetchPlaylist(sessid)
    }
    return res
  }

  async function markRead(sessid: string, id: string) {
    return api.markRead(sessid, id)
  }

  async function unreadAll(sessid: string) {
    const res = await api.unreadAll(sessid)
    if (res.result === 'success') {
      // Reset lastUpdateTime to force a full refresh
      lastUpdateTime.value = 0
      await fetchPlaylist(sessid)
    }
    return res
  }

  async function getNextTrack(sessid: string, lastPlayed: string) {
    return api.nextTrack(sessid, lastPlayed)
  }

  async function getPrevTrack(sessid: string, currentPlaying: string) {
    return api.prevTrack(sessid, currentPlaying)
  }

  function reset() {
    stopPolling()
    items.value = []
    lastUpdateTime.value = 0
    error.value = ''
  }

  function startPolling(sessid: string, intervalMs: number) {
    reset()
    fetchPlaylist(sessid)
    pollingInterval = setInterval(() => fetchPlaylist(sessid), intervalMs)
  }

  function stopPolling() {
    if (pollingInterval) {
      clearInterval(pollingInterval)
      pollingInterval = null
    }
  }

  return {
    items,
    lastUpdateTime,
    loading,
    error,
    unreadItems,
    readItems,
    fetchPlaylist,
    addTrack,
    deleteTrack,
    voteTrack,
    markRead,
    unreadAll,
    getNextTrack,
    getPrevTrack,
    reset,
    startPolling,
    stopPolling,
  }
})
