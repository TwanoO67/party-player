import type { YouTubeSearchResponse, YouTubeVideoResponse, YouTubePlaylistItemResponse } from '../types/youtube'

const PROXY_URL = '/api/youtube-proxy.php'

export class YouTubeQuotaError extends Error {
  constructor() {
    super('YouTube API quota exceeded')
    this.name = 'YouTubeQuotaError'
  }
}

async function ytRequest<T>(params: Record<string, string>): Promise<T> {
  const url = new URL(PROXY_URL, window.location.origin)
  for (const [key, value] of Object.entries(params)) {
    url.searchParams.set(key, value)
  }
  const res = await fetch(url.toString())
  if (res.status === 403) throw new YouTubeQuotaError()
  if (!res.ok) throw new Error(`YouTube API error: ${res.status}`)
  return res.json()
}

export function searchVideos(query: string, maxResults = 10) {
  return ytRequest<YouTubeSearchResponse>({
    endpoint: 'search',
    q: query,
    type: 'video',
    part: 'snippet',
    maxResults: String(maxResults),
    videoEmbeddable: 'true',
  })
}

export function searchPlaylists(query: string, maxResults = 10) {
  return ytRequest<YouTubeSearchResponse>({
    endpoint: 'search',
    q: query,
    type: 'playlist',
    part: 'snippet',
    maxResults: String(maxResults),
  })
}

export function getVideoInfo(id: string) {
  return ytRequest<YouTubeVideoResponse>({
    endpoint: 'videos',
    id,
    part: 'snippet,contentDetails',
  })
}

export async function getPlaylistItems(
  playlistId: string,
  pageToken?: string,
) {
  const params: Record<string, string> = {
    endpoint: 'playlistItems',
    playlistId,
    part: 'snippet',
    maxResults: '50',
  }
  if (pageToken) params.pageToken = pageToken
  return ytRequest<YouTubePlaylistItemResponse>(params)
}

export async function getAllPlaylistItems(playlistId: string) {
  const allItems: YouTubePlaylistItemResponse['items'] = []
  let pageToken: string | undefined

  do {
    const response = await getPlaylistItems(playlistId, pageToken)
    allItems.push(...response.items)
    pageToken = response.nextPageToken
  } while (pageToken)

  return allItems
}

export function parseDuration(iso: string): number {
  const match = iso.match(/PT(?:(\d+)H)?(?:(\d+)M)?(?:(\d+)S)?/)
  if (!match) return 0
  const hours = parseInt(match[1] || '0')
  const minutes = parseInt(match[2] || '0')
  const seconds = parseInt(match[3] || '0')
  return hours * 3600 + minutes * 60 + seconds
}

export function formatDuration(seconds: number): string {
  const m = Math.floor(seconds / 60)
  const s = seconds % 60
  return `${m}:${s.toString().padStart(2, '0')}`
}
