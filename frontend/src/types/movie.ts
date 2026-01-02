export interface Movie {
  id: number
  title: string
  overview: string
  poster_path: string | null
  backdrop_path: string | null
  release_date: string
  vote_average: number
  vote_count: number
  popularity: number
  genre_ids: number[]
  original_language: string
  original_title: string
  adult: boolean
  video: boolean
}

export interface Genre {
  id: number
  name: string
}

export interface MoviesResponse {
  results: Movie[]
  total_results: number
  total_pages: number
  page: number
}

export interface MovieDetails extends Movie {
  runtime: number
  genres: Genre[]
  budget: number
  revenue: number
  status: string
  tagline: string
}
