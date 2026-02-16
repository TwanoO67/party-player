export interface Vote {
  token: string
  user: string
  value: 'plus' | 'moins'
}

export interface PlaylistItem {
  id: string
  addUser: string
  addTime: number
  vote: number
  votes: Vote[]
  alreadyRead: false | number
}

export interface PlaylistData {
  lastUpdateTime: number
  creationTime: number
  lastUpdateIP?: string
  lastReadID?: string
  items: PlaylistItem[]
}

export interface ApiResponse<T = unknown> {
  result: 'success' | 'error'
  content?: T
  error?: string
}
