import { ref, computed } from 'vue'
import type { Movie } from '@/types/movie'
import { favoriteService, type Favorite } from '@/services/favoriteService'
import { eventBus, AppEvents } from '@/utils/eventBus'
import { errorHandler } from '@/utils/errorHandler'

const favorites = ref<Favorite[]>([])
const loading = ref(false)
const error = ref<string | null>(null)

export function useFavorites() {
  const favoriteMovieIds = computed(() => favorites.value.map((f) => f.movie_id))

  const isFavorite = (movieId: number): boolean => {
    return favoriteMovieIds.value.includes(movieId)
  }

  const fetchFavorites = async (): Promise<void> => {
    loading.value = true
    error.value = null

    try {
      favorites.value = await favoriteService.getAll()
    } catch (err: unknown) {
      const apiError = err as { response?: { data?: { message?: string } } }
      const errorMessage = apiError.response?.data?.message || 'Failed to fetch favorites'
      error.value = errorMessage
      errorHandler.handle(errorMessage, 'fetchFavorites')
    } finally {
      loading.value = false
    }
  }

  const addFavorite = async (movie: Movie): Promise<void> => {
    loading.value = true
    error.value = null

    try {
      const favorite = await favoriteService.add(movie)
      
      favorites.value.push(favorite)

      eventBus.emit(AppEvents.FAVORITE_ADDED, movie)
    } catch (err: unknown) {
      const apiError = err as { response?: { data?: { message?: string } } }
      const errorMessage = apiError.response?.data?.message || 'Failed to add favorite'
      error.value = errorMessage
      errorHandler.handle(errorMessage, 'addFavorite')
      eventBus.emit(AppEvents.ERROR_OCCURRED, { message: errorMessage, context: 'addFavorite' })
      throw new Error(errorMessage)
    } finally {
      loading.value = false
    }
  }

  const removeFavorite = async (movieId: number): Promise<void> => {
    loading.value = true
    error.value = null

    try {
      await favoriteService.remove(movieId)
      favorites.value = favorites.value.filter((f) => f.movie_id !== movieId)

      eventBus.emit(AppEvents.FAVORITE_REMOVED, { movieId })
    } catch (err: unknown) {
      const apiError = err as { response?: { data?: { message?: string } } }
      const errorMessage = apiError.response?.data?.message || 'Failed to remove favorite'
      error.value = errorMessage
      errorHandler.handle(errorMessage, 'removeFavorite')
      eventBus.emit(AppEvents.ERROR_OCCURRED, {
        message: errorMessage,
        context: 'removeFavorite',
      })
      throw new Error(errorMessage)
    } finally {
      loading.value = false
    }
  }

  const toggleFavorite = async (movie: Movie): Promise<void> => {
    if (isFavorite(movie.id)) {
      await removeFavorite(movie.id)
    } else {
      await addFavorite(movie)
    }
  }

  return {
    favorites: computed(() => favorites.value),
    loading: computed(() => loading.value),
    error: computed(() => error.value),
    favoriteMovieIds,
    isFavorite,
    fetchFavorites,
    addFavorite,
    removeFavorite,
    toggleFavorite,
  }
}
