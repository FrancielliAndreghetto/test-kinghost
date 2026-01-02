import { ref } from 'vue'
import type { Movie, MoviesResponse, Genre } from '@/types/movie'
import { API_CONFIG } from '@/config/api'

export function useMovies() {
  const movies = ref<Movie[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  const searchMovies = async (query: string): Promise<void> => {
    if (!query.trim()) return

    loading.value = true
    error.value = null

    try {
      const response = await fetch(
        `${API_CONFIG.BASE_URL}/movies/search?query=${encodeURIComponent(query)}`,
      )

      if (!response.ok) {
        throw new Error('Erro ao buscar filmes')
      }

      const data: MoviesResponse = await response.json()
      movies.value = data.results || []
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Erro desconhecido'
      console.error('Erro ao buscar filmes:', err)
    } finally {
      loading.value = false
    }
  }

  const getPopularMovies = async (page: number = 1): Promise<Movie[]> => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(`${API_CONFIG.BASE_URL}/movies/popular?page=${page}`)

      if (!response.ok) {
        throw new Error('Erro ao buscar filmes populares')
      }

      const data: MoviesResponse = await response.json()
      return data.results || []
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Erro desconhecido'
      console.error('Erro ao buscar filmes populares:', err)
      return []
    } finally {
      loading.value = false
    }
  }

  const getNowPlayingMovies = async (page: number = 1): Promise<Movie[]> => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(`${API_CONFIG.BASE_URL}/movies/now-playing?page=${page}`)

      if (!response.ok) {
        throw new Error('Erro ao buscar filmes em cartaz')
      }

      const data: MoviesResponse = await response.json()
      return data.results || []
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Erro desconhecido'
      console.error('Erro ao buscar filmes em cartaz:', err)
      return []
    } finally {
      loading.value = false
    }
  }

  const getUpcomingMovies = async (page: number = 1): Promise<Movie[]> => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(`${API_CONFIG.BASE_URL}/movies/upcoming?page=${page}`)

      if (!response.ok) {
        throw new Error('Erro ao buscar próximos lançamentos')
      }

      const data: MoviesResponse = await response.json()
      return data.results || []
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Erro desconhecido'
      console.error('Erro ao buscar próximos lançamentos:', err)
      return []
    } finally {
      loading.value = false
    }
  }

  const getTopRatedMovies = async (page: number = 1): Promise<Movie[]> => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(`${API_CONFIG.BASE_URL}/movies/top-rated?page=${page}`)

      if (!response.ok) {
        throw new Error('Erro ao buscar filmes mais bem avaliados')
      }

      const data: MoviesResponse = await response.json()
      return data.results || []
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Erro desconhecido'
      console.error('Erro ao buscar filmes mais bem avaliados:', err)
      return []
    } finally {
      loading.value = false
    }
  }

  const getMovieById = async (id: number): Promise<Movie | null> => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(`${API_CONFIG.BASE_URL}/movies/${id}`)

      if (!response.ok) {
        throw new Error('Erro ao buscar detalhes do filme')
      }

      return await response.json()
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Erro desconhecido'
      console.error('Erro ao buscar filme:', err)
      return null
    } finally {
      loading.value = false
    }
  }

  return {
    movies,
    loading,
    error,
    searchMovies,
    getPopularMovies,
    getNowPlayingMovies,
    getUpcomingMovies,
    getTopRatedMovies,
    getMovieById,
  }
}

export function useGenres() {
  const genres = ref<Genre[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchGenres = async (): Promise<void> => {
    loading.value = true
    error.value = null

    try {
      const response = await fetch(`${API_CONFIG.BASE_URL}/movies/genres`)

      if (!response.ok) {
        throw new Error('Erro ao buscar gêneros')
      }

      const data = await response.json()
      genres.value = data.genres || []
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Erro desconhecido'
      console.error('Erro ao buscar gêneros:', err)
    } finally {
      loading.value = false
    }
  }

  return {
    genres,
    loading,
    error,
    fetchGenres,
  }
}
