import Cookies from 'js-cookie'
import type {
  SpotifyUser,
  SpotifyPlaylistsResponse,
  SpotifyTracksResponse,
} from '../types/spotify'

const SPOTIFY_CLIENT_ID = import.meta.env.VITE_SPOTIFY_CLIENT_ID || ''
const PKCE_VERIFIER_KEY = 'spotify_pkce_verifier'

export function getSpotifyToken(): string {
  return Cookies.get('spotify_token') || ''
}

export function setSpotifyToken(token: string) {
  Cookies.set('spotify_token', token)
}

export function clearSpotifyToken() {
  Cookies.remove('spotify_token')
}

function generateRandomString(length: number): string {
  const possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
  const values = crypto.getRandomValues(new Uint8Array(length))
  return Array.from(values)
    .map((x) => possible[x % possible.length])
    .join('')
}

function base64urlEncode(buffer: ArrayBuffer): string {
  const bytes = new Uint8Array(buffer)
  let str = ''
  for (const byte of bytes) str += String.fromCharCode(byte)
  return btoa(str).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '')
}

async function sha256(plain: string): Promise<ArrayBuffer> {
  const data = new TextEncoder().encode(plain)
  return crypto.subtle.digest('SHA-256', data)
}

export async function authorizeSpotify(redirectUrl: string) {
  if (!SPOTIFY_CLIENT_ID) {
    console.error('VITE_SPOTIFY_CLIENT_ID not set')
    return
  }
  const codeVerifier = generateRandomString(64)
  const codeChallenge = base64urlEncode(await sha256(codeVerifier))
  sessionStorage.setItem(PKCE_VERIFIER_KEY, codeVerifier)

  const params = new URLSearchParams({
    client_id: SPOTIFY_CLIENT_ID,
    response_type: 'code',
    redirect_uri: window.location.origin + '/',
    scope: 'user-library-read playlist-read-private playlist-read-collaborative',
    state: window.btoa(redirectUrl),
    code_challenge_method: 'S256',
    code_challenge: codeChallenge,
  })
  window.location.href = 'https://accounts.spotify.com/authorize?' + params.toString()
}

export async function parseSpotifyCallback(): Promise<{ token: string; returnUrl: string } | null> {
  const params = new URLSearchParams(window.location.search)
  const code = params.get('code')
  const state = params.get('state')
  const error = params.get('error')

  if (error) {
    console.error('Spotify auth error:', error)
    // Clean up URL
    window.history.replaceState(null, '', window.location.pathname)
    return null
  }

  if (!code) return null

  const codeVerifier = sessionStorage.getItem(PKCE_VERIFIER_KEY)
  if (!codeVerifier) {
    console.error('Spotify PKCE: no code verifier found in sessionStorage')
    return null
  }
  sessionStorage.removeItem(PKCE_VERIFIER_KEY)

  const returnUrl = state ? window.atob(state) : '/'

  const body = new URLSearchParams({
    grant_type: 'authorization_code',
    code,
    redirect_uri: window.location.origin + '/',
    client_id: SPOTIFY_CLIENT_ID,
    code_verifier: codeVerifier,
  })

  const res = await fetch('https://accounts.spotify.com/api/token', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body.toString(),
  })

  if (!res.ok) {
    console.error('Spotify token exchange failed:', await res.text())
    return null
  }

  const data = await res.json()
  const token: string = data.access_token || ''
  return { token, returnUrl }
}

async function spotifyFetch<T>(url: string, token: string): Promise<T> {
  const res = await fetch(url, {
    headers: { Authorization: 'Bearer ' + token },
  })
  if (!res.ok) {
    if (res.status === 401) {
      clearSpotifyToken()
      throw new Error('Spotify token expired')
    }
    throw new Error(`Spotify API error: ${res.status}`)
  }
  return res.json()
}

export function fetchUserProfile(token: string) {
  return spotifyFetch<SpotifyUser>('https://api.spotify.com/v1/me', token)
}

export function fetchUserPlaylists(token: string, url?: string) {
  return spotifyFetch<SpotifyPlaylistsResponse>(
    url || 'https://api.spotify.com/v1/me/playlists?limit=50',
    token,
  )
}

export function fetchPlaylistTracks(token: string, url: string) {
  return spotifyFetch<SpotifyTracksResponse>(url, token)
}

export function isSpotifyConfigured(): boolean {
  return !!SPOTIFY_CLIENT_ID
}
