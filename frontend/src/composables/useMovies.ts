import { ref } from 'vue'
import type { Movie, MoviesResponse, Genre } from '@/types/movie'
import { movieService } from '@/services/movieService'
import { errorHandler } from '@/utils/errorHandler'

export function useMovies() {
  const movies = ref<Movie[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  const handleServiceCall = async <T>(
    serviceCall: () => Promise<T>,
    errorMessage: string,
  ): Promise<T | null> => {
    loading.value = true
    error.value = null

    try {
      return await serviceCall()
    } catch (err) {
      const message = err instanceof Error ? err.message : errorMessage
      error.value = message
      errorHandler.handle(message, 'useMovies')
      throw err
    } finally {
      loading.value = false
    }
  }

  const searchMovies = async (query: string, page: number = 1): Promise<MoviesResponse> => {
    const result = await handleServiceCall(
      () => movieService.searchMovies(query, page),
      'Erro ao buscar filmes',
    )
    
    if (result) {
      movies.value = result.results || []
      return result
    }
    
    return { results: [], page: 1, total_pages: 1, total_results: 0 }
  }

  const getPopularMovies = async (page: number = 1): Promise<Movie[]> => {
    const result = await handleServiceCall(
      () => movieService.getPopular(page),
      'Erro ao buscar filmes populares',
    )
    return result?.results || []
  }

  const getNowPlayingMovies = async (page: number = 1): Promise<Movie[]> => {
    const result = await handleServiceCall(
      () => movieService.getNowPlaying(page),
      'Erro ao buscar filmes em cartaz',
    )
    return result?.results || []
  }

  const getUpcomingMovies = async (page: number = 1): Promise<Movie[]> => {
    const result = await handleServiceCall(
      () => movieService.getUpcoming(page),
      'Erro ao buscar próximos lançamentos',
    )
    return result?.results || []
  }

  const getTopRatedMovies = async (page: number = 1): Promise<MoviesResponse> => {
    const result = await handleServiceCall(
      () => movieService.getTopRated(page),
      'Erro ao buscar filmes mais bem avaliados',
    )
    return result || { results: [], page: 1, total_pages: 1, total_results: 0 }
  }

  const getMovieById = async (id: number): Promise<Movie | null> => {
    return await handleServiceCall(
      () => movieService.getById(id),
      'Erro ao buscar detalhes do filme',
    )
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
      genres.value = await movieService.getGenres()
    } catch (err) {
      const message = err instanceof Error ? err.message : 'Erro ao buscar gêneros'
      error.value = message
      errorHandler.handle(message, 'useGenres')
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
