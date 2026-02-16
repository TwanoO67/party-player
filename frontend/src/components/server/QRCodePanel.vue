<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import QRCode from 'qrcode'

const props = defineProps<{ sessid: string }>()
const qrDataUrl = ref('')
const visible = ref(true)

const clientUrl = computed(() => {
  const base = window.location.origin
  return `${base}/jukebox/${props.sessid}`
})

onMounted(async () => {
  try {
    qrDataUrl.value = await QRCode.toDataURL(clientUrl.value, {
      width: 250,
      margin: 2,
      color: {
        dark: '#00f0ff',
        light: '#0a0a1a',
      },
    })
  } catch (e) {
    console.error('QR code generation failed:', e)
  }
})
</script>

<template>
  <div
    v-if="visible"
    class="bg-surface-card border border-neon-yellow/30 rounded-xl overflow-hidden"
  >
    <div class="px-4 py-3 flex items-center justify-between border-b border-neon-yellow/20">
      <h3 class="font-display text-xs text-neon-yellow">
        JukeBox <span class="text-white">{{ sessid }}</span>
      </h3>
      <button
        class="text-white/40 hover:text-white text-sm cursor-pointer"
        @click="visible = false"
      >
        &#10005;
      </button>
    </div>
    <div class="p-4 flex flex-col items-center gap-3">
      <img
        v-if="qrDataUrl"
        :src="qrDataUrl"
        alt="QR Code"
        class="w-48 h-48 rounded-lg"
      />
      <a
        :href="clientUrl"
        target="_blank"
        class="font-mono text-xs text-neon-cyan hover:text-glow-cyan transition-all break-all text-center"
      >
        {{ clientUrl }}
      </a>
    </div>
  </div>
</template>
