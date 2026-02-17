import { ref, onBeforeUnmount } from 'vue'
import { usePlayerStore } from '../stores/player'

declare global {
  interface Window {
    YT: typeof YT
    onYouTubeIframeAPIReady: () => void
  }
}

let apiLoaded = false
let apiReady = false
const readyCallbacks: (() => void)[] = []

function loadYouTubeAPI(): Promise<void> {
  return new Promise((resolve) => {
    if (apiReady) {
      resolve()
      return
    }
    readyCallbacks.push(resolve)
    if (apiLoaded) return

    apiLoaded = true
    const tag = document.createElement('script')
    tag.src = 'https://www.youtube.com/iframe_api'
    document.head.appendChild(tag)

    window.onYouTubeIframeAPIReady = () => {
      apiReady = true
      readyCallbacks.forEach((cb) => cb())
      readyCallbacks.length = 0
    }
  })
}

export function useYouTubePlayer(containerId: string) {
  const playerStore = usePlayerStore()
  let player: YT.Player | null = null
  const ready = ref(false)
  let progressTimer: ReturnType<typeof setInterval> | null = null

  const onEnded = ref<(() => void) | null>(null)

  async function init() {
    await loadYouTubeAPI()
    return new Promise<void>((resolve) => {
      player = new window.YT.Player(containerId, {
        height: '100%',
        width: '100%',
        playerVars: {
          autoplay: 1,
          controls: 1,
          modestbranding: 1,
          rel: 0,
          fs: 1,
          playsinline: 1,
        },
        events: {
          onReady: () => {
            ready.value = true
            resolve()
          },
          onStateChange: (event: YT.OnStateChangeEvent) => {
            switch (event.data) {
              case window.YT.PlayerState.PLAYING:
                playerStore.setPlaying(true)
                startProgressTracking()
                break
              case window.YT.PlayerState.PAUSED:
                playerStore.setPlaying(false)
                stopProgressTracking()
                break
              case window.YT.PlayerState.ENDED:
                playerStore.setPlaying(false)
                stopProgressTracking()
                if (onEnded.value) onEnded.value()
                break
            }
          },
        },
      })
    })
  }

  function loadVideo(videoId: string) {
    if (player && ready.value) {
      player.loadVideoById({ videoId, suggestedQuality: 'default' })
      // loadVideoById should auto-play, but force it as fallback
      player.playVideo()
    }
  }

  function play() {
    player?.playVideo()
  }

  function pause() {
    player?.pauseVideo()
  }

  function setVolume(volume: number) {
    player?.setVolume(volume)
  }

  function startProgressTracking() {
    stopProgressTracking()
    progressTimer = setInterval(() => {
      if (player) {
        playerStore.setProgress(
          player.getCurrentTime?.() || 0,
          player.getDuration?.() || 0,
        )
      }
    }, 1000)
  }

  function stopProgressTracking() {
    if (progressTimer) {
      clearInterval(progressTimer)
      progressTimer = null
    }
  }

  function destroy() {
    stopProgressTracking()
    player?.destroy()
    player = null
    ready.value = false
  }

  onBeforeUnmount(() => {
    destroy()
  })

  return {
    ready,
    onEnded,
    init,
    loadVideo,
    play,
    pause,
    setVolume,
    destroy,
  }
}
