export interface YouTubeSearchResult {
  kind: string
  etag: string
  id: {
    kind: string
    videoId?: string
    playlistId?: string
  }
  snippet: {
    publishedAt: string
    channelId: string
    title: string
    description: string
    thumbnails: {
      default: YouTubeThumbnail
      medium: YouTubeThumbnail
      high: YouTubeThumbnail
    }
    channelTitle: string
  }
}

export interface YouTubeThumbnail {
  url: string
  width: number
  height: number
}

export interface YouTubeSearchResponse {
  kind: string
  etag: string
  pageInfo: { totalResults: number; resultsPerPage: number }
  items: YouTubeSearchResult[]
}

export interface YouTubeVideo {
  kind: string
  etag: string
  id: string
  snippet: {
    publishedAt: string
    channelId: string
    title: string
    description: string
    thumbnails: {
      default: YouTubeThumbnail
      medium: YouTubeThumbnail
      high: YouTubeThumbnail
    }
    channelTitle: string
  }
  contentDetails?: {
    duration: string
    dimension: string
    definition: string
  }
}

export interface YouTubeVideoResponse {
  kind: string
  etag: string
  items: YouTubeVideo[]
}

export interface YouTubePlaylistItemResponse {
  kind: string
  etag: string
  nextPageToken?: string
  pageInfo: { totalResults: number; resultsPerPage: number }
  items: YouTubeSearchResult[]
}
