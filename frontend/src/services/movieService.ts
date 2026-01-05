import { apiClient } from './apiClient'
import type { Movie, MoviesResponse, Genre } from '@/types/movie'

export class MovieService {
  private readonly baseUrl = '/movies'

  async searchMovies(query: string, page: number = 1): Promise<MoviesResponse> {
    if (!query.trim()) {
      return this.getEmptyResponse()
    }

    return await apiClient.get<MoviesResponse>(
      `${this.baseUrl}/search?query=${encodeURIComponent(query)}&page=${page}`,
    )
  }

  async getPopular(page: number = 1): Promise<MoviesResponse> {
    return await apiClient.get<MoviesResponse>(`${this.baseUrl}/popular?page=${page}`)
  }

  async getNowPlaying(page: number = 1): Promise<MoviesResponse> {
    return await apiClient.get<MoviesResponse>(`${this.baseUrl}/now-playing?page=${page}`)
  }

  async getUpcoming(page: number = 1): Promise<MoviesResponse> {
    return await apiClient.get<MoviesResponse>(`${this.baseUrl}/upcoming?page=${page}`)
  }

  async getTopRated(page: number = 1): Promise<MoviesResponse> {
    return await apiClient.get<MoviesResponse>(`${this.baseUrl}/top-rated?page=${page}`)
  }

  async getById(id: number): Promise<Movie> {
    return await apiClient.get<Movie>(`${this.baseUrl}/${id}`)
  }

  async getGenres(): Promise<Genre[]> {
    const response = await apiClient.get<{ genres: Genre[] }>(`${this.baseUrl}/genres`)
    return response.genres || []
  }

  async getByGenre(genreId: number, page: number = 1): Promise<MoviesResponse> {
    return await apiClient.get<MoviesResponse>(
      `${this.baseUrl}/discover?with_genres=${genreId}&page=${page}`,
    )
  }

  private getEmptyResponse(): MoviesResponse {
    return {
      results: [],
      page: 1,
      total_pages: 1,
      total_results: 0,
    }
  }
}

export const movieService = new MovieService()
