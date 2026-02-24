<script setup lang="ts">
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { parseSpotifyCallback, setSpotifyToken } from './services/spotify'
import AppHeader from './components/layout/AppHeader.vue'

const router = useRouter()

onMounted(async () => {
  // Handle Spotify OAuth callback (PKCE flow: ?code= in query params)
  const result = await parseSpotifyCallback()
  if (result) {
    setSpotifyToken(result.token)
    let path = '/'
    try {
      path = new URL(result.returnUrl).pathname
    } catch {
      path = result.returnUrl
    }
    window.history.replaceState(null, '', '/')
    router.replace(path)
  }
})
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <AppHeader />
    <main class="flex-1">
      <router-view />
    </main>
  </div>
</template>
