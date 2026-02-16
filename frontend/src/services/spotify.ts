import Cookies from 'js-cookie'
import type {
  SpotifyUser,
  SpotifyPlaylistsResponse,
  SpotifyTracksResponse,
} from '../types/spotify'

const SPOTIFY_CLIENT_ID = import.meta.env.VITE_SPOTIFY_CLIENT_ID || ''

export function getSpotifyToken(): string {
  return Cookies.get('spotify_token') || ''
}

export function setSpotifyToken(token: string) {
  Cookies.set('spotify_token', token)
}

export function clearSpotifyToken() {
  Cookies.remove('spotify_token')
}

export function authorizeSpotify(redirectUrl: string) {
  if (!SPOTIFY_CLIENT_ID) {
    console.error('VITE_SPOTIFY_CLIENT_ID not set')
    return
  }
  const url =
    'https://accounts.spotify.com/authorize?client_id=' +
    SPOTIFY_CLIENT_ID +
    '&response_type=token' +
    '&scope=user-library-read' +
    '&state=' +
    window.btoa(redirectUrl) +
    '&redirect_uri=' +
    encodeURIComponent(window.location.origin + '/')
  window.location.href = url
}

export function parseSpotifyCallback(): { token: string; returnUrl: string } | null {
  const hash = window.location.hash
  if (!hash || !hash.includes('access_token')) return null

  const params = new URLSearchParams(hash.substring(1))
  const token = params.get('access_token') || ''
  const state = params.get('state') || ''
  const returnUrl = state ? window.atob(state) : '/'

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
