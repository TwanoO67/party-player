import type { ApiResponse, PlaylistData } from '../types/playlist'

const API_URL = '/api.php'

async function request<T>(params: Record<string, string>): Promise<ApiResponse<T>> {
  const url = new URL(API_URL, window.location.origin)
  for (const [key, value] of Object.entries(params)) {
    url.searchParams.set(key, value)
  }
  const res = await fetch(url.toString())
  let text = await res.text()
  // Strip any PHP warnings/notices before JSON
  const jsonStart = text.indexOf('{')
  if (jsonStart > 0) {
    text = text.substring(jsonStart)
  }
  try {
    return JSON.parse(text)
  } catch {
    return { result: 'error', error: 'Invalid JSON response' } as ApiResponse<T>
  }
}

export function createSession(sessid: string) {
  return request({ mode: 'create', sessid })
}

export function readPlaylist(sessid: string, lastUpdateTime: number) {
  return request<PlaylistData | 'no_news'>({
    mode: 'read',
    sessid,
    lastUpdateTime: String(lastUpdateTime),
  })
}

export function addTrack(sessid: string, id: string, user: string) {
  return request({ mode: 'add', sessid, id, user })
}

export function deleteTrack(sessid: string, id: string, user: string) {
  return request({ mode: 'delete', sessid, id, user })
}

export function vote(
  sessid: string,
  itemId: string,
  user: string,
  voteValue: 'plus' | 'moins',
  voteToken: string,
) {
  return request({
    mode: 'vote',
    sessid,
    item_id: itemId,
    user,
    vote: voteValue,
    vote_token: voteToken,
  })
}

export function nextTrack(sessid: string, lastPlayed: string) {
  return request<string>({ mode: 'next_track', sessid, last_played: lastPlayed })
}

export function markRead(sessid: string, id: string) {
  return request({ mode: 'mark_read', sessid, id })
}

export function unreadAll(sessid: string) {
  return request({ mode: 'unread_all', sessid })
}
