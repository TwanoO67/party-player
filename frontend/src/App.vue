<script setup lang="ts">
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { parseSpotifyCallback, setSpotifyToken } from './services/spotify'
import AppHeader from './components/layout/AppHeader.vue'

const router = useRouter()

onMounted(() => {
  // Handle Spotify OAuth callback at app level
  // Spotify redirects to origin/ with token in hash
  const result = parseSpotifyCallback()
  if (result) {
    setSpotifyToken(result.token)
    // Extract path from the full return URL
    let path = '/'
    try {
      path = new URL(result.returnUrl).pathname
    } catch {
      path = result.returnUrl
    }
    // Clean hash and navigate to the return URL
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
