import { defineStore } from 'pinia'
import { ref } from 'vue'
import Cookies from 'js-cookie'

export const useSessionStore = defineStore('session', () => {
  const sessid = ref('')
  const mode = ref<'server' | 'client'>('client')
  const username = ref(Cookies.get('username') || '')
  const voteToken = ref(Cookies.get('vote_token') || generateToken())

  function generateToken(): string {
    const token = Math.random().toString(36).substring(2, 7)
    Cookies.set('vote_token', token)
    return token
  }

  function setUsername(name: string) {
    username.value = name
    Cookies.set('username', name)
  }

  function clearUsername() {
    username.value = ''
    Cookies.remove('username')
  }

  function setSession(id: string, m: 'server' | 'client') {
    sessid.value = id
    mode.value = m
    if (!username.value) {
      const defaultName = (m === 'server' ? 'Jukebox' : 'Guest') + id
      setUsername(defaultName)
    }
  }

  return { sessid, mode, username, voteToken, setUsername, clearUsername, setSession }
})
