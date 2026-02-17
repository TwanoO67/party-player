<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const sessidInput = ref('')
const generatedId = ref(Math.floor(Math.random() * 9990) + 10)

function createSession() {
  router.push(`/player/${generatedId.value}`)
}

function joinSession() {
  if (sessidInput.value.trim()) {
    router.push(`/jukebox/${sessidInput.value.trim()}`)
  }
}
</script>

<template>
  <div class="max-w-4xl mx-auto px-4 py-12 md:py-20">
    <!-- Hero -->
    <div class="text-center mb-16">
      <h2 class="font-display text-2xl md:text-4xl text-neon-pink text-glow-pink mb-6 leading-relaxed">
        Party Player
      </h2>
      <p class="font-mono text-xl md:text-2xl text-neon-cyan/80 mb-2">
        Le Jukebox collaboratif de vos soirées
      </p>
      <p class="text-white/50 text-base mt-4">
        Chaque invité ajoute ses musiques depuis son smartphone
      </p>
    </div>

    <!-- Tutorial Steps -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
      <div class="bg-surface-card border border-neon-cyan/20 rounded-xl p-6 text-center hover:border-neon-cyan/50 transition-colors">
        <div class="text-4xl mb-4">&#127911;</div>
        <h3 class="font-display text-sm text-neon-cyan mb-3">1. Créer</h3>
        <p class="text-white/60 text-base font-mono">
          Lancez un Jukebox sur l'ordinateur qui diffuse la musique
        </p>
      </div>
      <div class="bg-surface-card border border-neon-pink/20 rounded-xl p-6 text-center hover:border-neon-pink/50 transition-colors">
        <div class="text-4xl mb-4">&#128241;</div>
        <h3 class="font-display text-sm text-neon-pink mb-3">2. Partager</h3>
        <p class="text-white/60 text-base font-mono">
          Partagez le QR code avec vos invités
        </p>
      </div>
      <div class="bg-surface-card border border-neon-purple/20 rounded-xl p-6 text-center hover:border-neon-purple/50 transition-colors">
        <div class="text-4xl mb-4">&#127926;</div>
        <h3 class="font-display text-sm text-neon-purple mb-3">3. Écouter</h3>
        <p class="text-white/60 text-base font-mono">
          Chacun ajoute ses musiques et vote pour ses préférées
        </p>
      </div>
    </div>

    <!-- Actions -->
    <div class="flex flex-col md:flex-row gap-6 items-center justify-center">
      <!-- Create -->
      <div class="bg-surface-card border border-neon-pink/30 rounded-xl p-8 text-center w-full md:w-auto border-glow-pink">
        <h3 class="font-display text-sm text-neon-pink mb-4">Lancer un Jukebox</h3>
        <p class="text-white/50 text-base font-mono mb-6">
          Session #{{ generatedId }}
        </p>
        <button class="btn-neon-pink w-full" @click="createSession">
          Commencer
        </button>
      </div>

      <span class="text-white/30 font-display text-sm">ou</span>

      <!-- Join -->
      <div class="bg-surface-card border border-neon-cyan/30 rounded-xl p-8 text-center w-full md:w-auto">
        <h3 class="font-display text-sm text-neon-cyan mb-4">Rejoindre</h3>
        <input
          v-model="sessidInput"
          type="text"
          placeholder="Code du Jukebox"
          class="w-full bg-surface-dark border border-neon-cyan/30 rounded-lg px-4 py-3 text-white font-mono text-center text-lg mb-4 focus:outline-none focus:border-neon-cyan focus:shadow-neon-cyan transition-all"
          @keyup.enter="joinSession"
        />
        <button class="btn-neon-cyan w-full" @click="joinSession">
          Rejoindre
        </button>
      </div>
    </div>
  </div>
</template>
