export interface SpotifyUser {
  id: string
  display_name: string
  images: { url: string }[]
}

export interface SpotifyPlaylist {
  id: string
  name: string
  description: string
  images: { url: string }[]
  tracks: { total: number; href: string }
  owner: { display_name: string }
}

export interface SpotifyPlaylistsResponse {
  items: SpotifyPlaylist[]
  next: string | null
  total: number
}

export interface SpotifyTrack {
  track: {
    id: string
    name: string
    artists: { name: string }[]
    duration_ms: number
    album: {
      name: string
      images: { url: string }[]
    }
  }
}

export interface SpotifyTracksResponse {
  items: SpotifyTrack[]
  next: string | null
  total: number
}
