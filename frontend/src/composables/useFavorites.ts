import { ref, computed } from 'vue'
import axios from 'axios'
import type { Movie } from '@/types/movie'
import { eventBus, AppEvents } from '@/utils/eventBus'

interface Favorite {
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

const favorites = ref<Favorite[]>([])
const loading = ref(false)
const error = ref<string | null>(null)

export function useFavorites() {
  const favoriteMovieIds = computed(() => 
    favorites.value.map(f => f.movie_id)
  )

  const isFavorite = (movieId: number): boolean => {
    return favoriteMovieIds.value.includes(movieId)
  }

  const fetchFavorites = async (): Promise<void> => {
    loading.value = true
    error.value = null

    try {
      const response = await axios.get('/api/favorites')
      favorites.value = response.data.favorites
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch favorites'
    } finally {
      loading.value = false
    }
  }

  const addFavorite = async (movie: Movie): Promise<void> => {
    loading.value = true
    error.value = null

    try {
      const response = await axios.post('/api/favorites', {
        movie_id: movie.id,
        movie_title: movie.title,
        poster_path: (movie.poster_path ?? '') as string, 
        overview: (movie.overview ?? '') as string,
        vote_average: movie.vote_average,
        release_date: movie.release_date,
        genre_ids: movie.genre_ids || [],
      })

      console.log('➕ Added to favorites:', response.data)
      favorites.value.push(response.data.favorite || response.data)
      
      // Observer Pattern - Emit event
      eventBus.emit(AppEvents.FAVORITE_ADDED, movie)
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to add favorite'
      eventBus.emit(AppEvents.ERROR_OCCURRED, { message: error.value, context: 'addFavorite' })
      throw new Error(error.value || 'Failed to add favorite')
    } finally {
      loading.value = false
    }
  }

  const removeFavorite = async (movieId: number): Promise<void> => {
    loading.value = true
    error.value = null

    try {
      await axios.delete(`/api/favorites/${movieId}`)
      favorites.value = favorites.value.filter(f => f.movie_id !== movieId)
      
      // Observer Pattern - Emit event
      eventBus.emit(AppEvents.FAVORITE_REMOVED, { movieId })
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to remove favorite'
      eventBus.emit(AppEvents.ERROR_OCCURRED, { message: error.value, context: 'removeFavorite' })
      throw new Error(error.value || 'Failed to remove favorite')
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