import { apiClient } from './apiClient'
import type { Movie } from '@/types/movie'

export interface Favorite {
  id: number
  user_id: number
  movie_id: number
  movie_title: string
  poster_path: string | null
  overview: string | null
  vote_average: number | null
  release_date: string | null
  genre_ids: number[] | null
  created_at: string
  updated_at: string
}

export interface FavoriteResponse {
  success: boolean
  favorites?: Favorite[]
  favorite?: Favorite
  message?: string
}

export interface FavoriteCheckResponse {
  success: boolean
  is_favorite: boolean
}

export class FavoriteService {
  private readonly baseUrl = '/favorites'

  async getAll(): Promise<Favorite[]> {
    const response = await apiClient.get<FavoriteResponse>(this.baseUrl)
    return response.favorites || []
  }

  async add(movie: Movie): Promise<Favorite> {
    const payload = {
      movie_id: movie.id,
      movie_title: movie.title,
      poster_path: movie.poster_path ?? '',
      overview: movie.overview ?? '',
      vote_average: movie.vote_average,
      release_date: movie.release_date,
      genre_ids: movie.genre_ids || [],
    }

    const response = await apiClient.post<FavoriteResponse>(this.baseUrl, payload)
    return response.favorite!
  }

  async remove(movieId: number): Promise<void> {
    await apiClient.delete<FavoriteResponse>(`${this.baseUrl}/${movieId}`)
  }

  async check(movieId: number): Promise<boolean> {
    const response = await apiClient.get<FavoriteCheckResponse>(
      `${this.baseUrl}/check/${movieId}`,
    )
    return response.is_favorite
  }
}

export const favoriteService = new FavoriteService()
