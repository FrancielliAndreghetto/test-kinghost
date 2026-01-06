<template>
  <div class="favorites-view">
    <AppHeader @search="handleSearch" />

    <main>
      <div v-if="isSearchActive" class="search-results-section">
        <div class="section-header">
          <h2 class="section-title">
            {{ searchResultsCount }} resultado(s) para "{{ currentSearchQuery }}"
          </h2>
          <button class="clear-search-button" @click="clearSearch">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="20"
              height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
            Limpar busca
          </button>
        </div>

        <div v-if="searchLoading" class="loading-state">
          <div class="spinner"></div>
          <p>Buscando nos favoritos...</p>
        </div>

        <div v-else-if="searchResults.length > 0" class="movies-grid">
          <MovieCard
            v-for="movie in searchResults"
            :key="movie.id"
            :movie="movie"
            @click="handleMovieClick"
          />
        </div>

        <div v-else-if="!searchLoading && searchResults.length === 0" class="no-results-inline">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="64"
            height="64"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
          >
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="15" y1="9" x2="9" y2="15"></line>
            <line x1="9" y1="9" x2="15" y2="15"></line>
          </svg>
          <h3>Nenhum favorito encontrado</h3>
          <p>Tente buscar com outros termos</p>
        </div>
      </div>

      <div v-else>
        <div class="favorites-header">
          <h1 class="page-title">MEUS FAVORITOS</h1>
          <p class="page-subtitle">{{ favorites.length }} filme(s) favorito(s)</p>
        </div>

        <div v-if="loading" class="loading-state">
          <div class="spinner"></div>
          <p>Carregando favoritos...</p>
        </div>

        <div v-else-if="favorites.length === 0" class="empty-state">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="80"
            height="80"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
          >
            <path
              d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"
            ></path>
          </svg>
          <h2>Nenhum favorito ainda</h2>
          <p>Comece adicionando filmes aos seus favoritos!</p>
          <router-link to="/" class="explore-button">Explorar Filmes</router-link>
        </div>

        <div v-else class="favorites-content">
          <FilterTabs v-model="selectedGenre" :filters="genreFilters" />

          <div class="movies-grid">
            <MovieCard
              v-for="favorite in filteredFavorites"
              :key="favorite.id"
              :movie="convertToMovie(favorite)"
              @click="handleMovieClick"
            />
          </div>

          <div v-if="filteredFavorites.length === 0" class="no-results-inline">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="64"
              height="64"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.5"
            >
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="15" y1="9" x2="9" y2="15"></line>
              <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
            <h3>Nenhum favorito neste gênero</h3>
            <p>Tente selecionar outro gênero</p>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import AppHeader from '@/components/AppHeader.vue'
import MovieCard from '@/components/MovieCard.vue'
import FilterTabs from '@/components/FilterTabs.vue'
import { useFavorites } from '@/composables/useFavorites'
import { useGenres } from '@/composables/useMovies'
import type { Movie } from '@/types/movie'

interface Favorite {
  id: number
  user_id: number
  movie_id: number
  movie_title: string
  poster_path: string | null
  backdrop_path: string | null
  overview: string | null
  vote_average: number | null
  release_date: string | null
  genre_ids: number[] | null
  created_at: string
  updated_at: string
}

const { favorites, loading } = useFavorites()
const { genres, fetchGenres } = useGenres()

const selectedGenre = ref('Todos')
const isSearchActive = ref(false)
const currentSearchQuery = ref('')
const searchResults = ref<Favorite[]>([])
const searchLoading = ref(false)

onMounted(async () => {
  await fetchGenres()
})

const genreFilters = computed(() => {
  if (!genres.value || genres.value.length === 0) {
    return [{ id: 'Todos', label: 'Todos' }]
  }
  return [
    { id: 'Todos', label: 'Todos' },
    ...genres.value.map(genre => ({
      id: genre.id.toString(),
      label: genre.name
    }))
  ]
})

const filteredFavorites = computed(() => {
  if (selectedGenre.value === 'Todos') {
    return favorites.value
  }
  
  const genreId = parseInt(selectedGenre.value)
  return favorites.value.filter((favorite: Favorite) => 
    favorite.genre_ids && Array.isArray(favorite.genre_ids) && favorite.genre_ids.includes(genreId)
  )
})

const searchResultsCount = computed(() => searchResults.value.length)

const handleSearch = async (query: string) => {
  if (!query || query.trim() === '') {
    clearSearch()
    return
  }

  currentSearchQuery.value = query
  isSearchActive.value = true
  searchLoading.value = true

  try {
    const lowerQuery = query.toLowerCase()
    searchResults.value = favorites.value.filter((favorite: Favorite) => 
      favorite.movie_title?.toLowerCase().includes(lowerQuery) ||
      favorite.overview?.toLowerCase().includes(lowerQuery)
    )
  } catch (err) {
    searchResults.value = []
  } finally {
    searchLoading.value = false
  }
}

const clearSearch = () => {
  isSearchActive.value = false
  currentSearchQuery.value = ''
  searchResults.value = []
  searchLoading.value = false
}

const handleMovieClick = (movieId: number) => {
  // Movie clicked
}

const convertToMovie = (favorite: Favorite): Movie => {
  return {
    id: favorite.movie_id,
    title: favorite.movie_title,
    poster_path: favorite.poster_path,
    overview: favorite.overview || '',
    vote_average: favorite.vote_average || 0,
    release_date: favorite.release_date || '',
    backdrop_path: favorite.backdrop_path,
    genre_ids: favorite.genre_ids || [],
    original_language: '',
    original_title: favorite.movie_title,
    popularity: 0,
    video: false,
    adult: false,
    vote_count: 0,
  }
}
</script>

<style scoped>
.favorites-view {
  min-height: 100vh;
  background: linear-gradient(to bottom, #0a0a0a 0%, #1a1a1a 100%);
}

main {
  padding-bottom: 4rem;
}

.favorites-header {
  padding: 8rem 2rem 2rem;
  text-align: center;
}

.page-title {
  font-size: 3rem;
  font-weight: 700;
  color: #fff;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  margin-bottom: 0.5rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.page-subtitle {
  font-size: 1.125rem;
  color: rgba(255, 255, 255, 0.6);
  font-weight: 500;
}

.favorites-content {
  margin-top: 2rem;
}

.search-results-section {
  padding: 0 2rem;
  margin-top: 6rem;
}

.section-header {
  padding: 0;
  margin-bottom: 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.section-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #fff;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.clear-search-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: rgba(255, 255, 255, 0.9);
  padding: 0.5rem 1rem;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.3s ease;
}

.clear-search-button:hover {
  background: rgba(255, 255, 255, 0.15);
  border-color: rgba(255, 255, 255, 0.3);
}

.movies-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 1.5rem;
  padding: 0 2rem;
}

.loading-state,
.empty-state,
.no-results-inline {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
  text-align: center;
}

.loading-state {
  color: rgba(255, 255, 255, 0.7);
}

.empty-state svg {
  color: rgba(255, 255, 255, 0.2);
  margin-bottom: 1.5rem;
}

.empty-state h2 {
  color: #fff;
  font-size: 2rem;
  margin-bottom: 0.5rem;
  font-weight: 600;
}

.empty-state p {
  color: rgba(255, 255, 255, 0.6);
  margin-bottom: 2rem;
  font-size: 1.125rem;
}

.explore-button {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
  padding: 0.875rem 2rem;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s ease;
}

.explore-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
}

.no-results-inline svg {
  color: rgba(255, 255, 255, 0.2);
  margin-bottom: 1rem;
}

.no-results-inline h3 {
  color: #fff;
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
}

.no-results-inline p {
  color: rgba(255, 255, 255, 0.6);
}

.spinner {
  width: 48px;
  height: 48px;
  border: 4px solid rgba(255, 255, 255, 0.1);
  border-top-color: #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

@media (max-width: 1024px) {
  .movies-grid {
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  }
}

@media (max-width: 768px) {
  .favorites-header {
    padding: 6rem 1rem 1.5rem;
  }

  .page-title {
    font-size: 2rem;
  }

  .search-results-section {
    padding: 0 1rem;
    margin-top: 5rem;
  }

  .movies-grid {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
    padding: 0 1rem;
  }

  .clear-search-button {
    font-size: 0.75rem;
    padding: 0.4rem 0.8rem;
  }
}
</style>
