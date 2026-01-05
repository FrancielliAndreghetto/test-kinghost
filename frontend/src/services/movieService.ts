import { apiClient } from './apiClient'
import type { Movie, MoviesResponse, Genre } from '@/types/movie'

/**
 * Movie API Service
 * Handles all movie-related API calls
 * Following Service Pattern and Single Responsibility Principle
 */
export class MovieService {
  private readonly baseUrl = '/movies'

  /**
   * Search movies by query
   */
  async searchMovies(query: string, page: number = 1): Promise<MoviesResponse> {
    if (!query.trim()) {
      return this.getEmptyResponse()
    }

    return await apiClient.get<MoviesResponse>(
      `${this.baseUrl}/search?query=${encodeURIComponent(query)}&page=${page}`,
    )
  }

  /**
   * Get popular movies
   */
  async getPopular(page: number = 1): Promise<MoviesResponse> {
    return await apiClient.get<MoviesResponse>(`${this.baseUrl}/popular?page=${page}`)
  }

  /**
   * Get now playing movies
   */
  async getNowPlaying(page: number = 1): Promise<MoviesResponse> {
    return await apiClient.get<MoviesResponse>(`${this.baseUrl}/now-playing?page=${page}`)
  }

  /**
   * Get upcoming movies
   */
  async getUpcoming(page: number = 1): Promise<MoviesResponse> {
    return await apiClient.get<MoviesResponse>(`${this.baseUrl}/upcoming?page=${page}`)
  }

  /**
   * Get top rated movies
   */
  async getTopRated(page: number = 1): Promise<MoviesResponse> {
    return await apiClient.get<MoviesResponse>(`${this.baseUrl}/top-rated?page=${page}`)
  }

  /**
   * Get movie by ID
   */
  async getById(id: number): Promise<Movie> {
    return await apiClient.get<Movie>(`${this.baseUrl}/${id}`)
  }

  /**
   * Get movie genres
   */
  async getGenres(): Promise<Genre[]> {
    const response = await apiClient.get<{ genres: Genre[] }>(`${this.baseUrl}/genres`)
    return response.genres || []
  }

  /**
   * Get movies by genre
   */
  async getByGenre(genreId: number, page: number = 1): Promise<MoviesResponse> {
    return await apiClient.get<MoviesResponse>(
      `${this.baseUrl}/discover?with_genres=${genreId}&page=${page}`,
    )
  }

  /**
   * Helper method to return empty response
   */
  private getEmptyResponse(): MoviesResponse {
    return {
      results: [],
      page: 1,
      total_pages: 1,
      total_results: 0,
    }
  }
}

// Singleton instance
export const movieService = new MovieService()
