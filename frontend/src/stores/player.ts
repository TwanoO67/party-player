import { defineStore } from 'pinia'
import { ref } from 'vue'

export const usePlayerStore = defineStore('player', () => {
  const currentVideoId = ref('')
  const currentTitle = ref('')
  const isPlaying = ref(false)
  const duration = ref(0)
  const currentTime = ref(0)

  function setCurrentTrack(videoId: string, title: string) {
    currentVideoId.value = videoId
    currentTitle.value = title
  }

  function setPlaying(playing: boolean) {
    isPlaying.value = playing
  }

  function setProgress(time: number, dur: number) {
    currentTime.value = time
    duration.value = dur
  }

  function clear() {
    currentVideoId.value = ''
    currentTitle.value = ''
    isPlaying.value = false
    duration.value = 0
    currentTime.value = 0
  }

  return {
    currentVideoId,
    currentTitle,
    isPlaying,
    duration,
    currentTime,
    setCurrentTrack,
    setPlaying,
    setProgress,
    clear,
  }
})
