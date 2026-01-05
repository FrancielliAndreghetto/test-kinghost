<template>
  <div class="home-view">
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
          <p>Buscando...</p>
        </div>

        <div v-else-if="displayedSearchResults.length > 0" class="movies-grid">
          <MovieCard
            v-for="movie in displayedSearchResults"
            :key="movie.id"
            :movie="movie"
            @click="handleMovieClick"
          />
        </div>

        <div v-if="isLoadingMore" class="loading-more">
          <div class="spinner"></div>
          <p>Carregando mais filmes...</p>
        </div>

        <div v-else-if="displayedSearchResults.length > 0 && currentPage >= totalPages" class="no-more-results">
          <p>✓ Todos os resultados foram carregados</p>
        </div>

        <div v-else-if="!searchLoading && displayedSearchResults.length === 0" class="no-results-inline">
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
          <h3>Nenhum resultado encontrado</h3>
          <p>Tente buscar com outros termos</p>
        </div>
      </div>

      <div v-else>
        <HeroSection
          v-if="featuredMovie"
          :movie="featuredMovie"
          :genres="['Romance', 'Drama']"
          @watch="handleWatch"
          @addToList="handleAddToList"
        />

        <MovieCarousel 
          v-if="isAuthenticated && favorites.length > 0"
          title="MEUS FAVORITOS" 
          :show-navigation="true"
        >
          <template #action>
            <router-link to="/favorites" class="view-all-button">
              Ver Todos
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <polyline points="9 18 15 12 9 6"></polyline>
              </svg>
            </router-link>
          </template>
          <MovieCard
            v-for="favorite in favorites.slice(0, 20)"
            :key="favorite.id"
            :movie="mapFavoriteToMovie(favorite)"
            @click="handleMovieClick"
          />
        </MovieCarousel>

        <div class="recommended-section">
          <div class="section-header">
            <h2 class="section-title">RECOMENDADOS PARA VOCÊ</h2>
          </div>

          <FilterTabs v-model="selectedLanguage" :filters="genreFilters" />

          <div class="movies-grid">
            <MovieCard
              v-for="movie in recommendedMovies"
              :key="movie.id"
              :movie="movie"
              @click="handleMovieClick"
            />
          </div>

          <div v-if="isLoadingMoreRecommended" class="loading-more">
            <div class="spinner"></div>
            <p>Carregando mais filmes...</p>
          </div>

          <div v-else-if="recommendedPage >= recommendedTotalPages && recommendedMovies.length > 0" class="no-more-results">
            <p>✓ Todos os filmes foram carregados</p>
          </div>
        </div>

        <div v-if="error" class="error-state">
          <p>{{ error }}</p>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, onUnmounted } from 'vue'
import { useAuth } from '@/composables/useAuth'
import { useFavorites } from '@/composables/useFavorites'
import AppHeader from '@/components/AppHeader.vue'
import HeroSection from '@/components/HeroSection.vue'
import MovieCarousel from '@/components/MovieCarousel.vue'
import MovieCard from '@/components/MovieCard.vue'
import FilterTabs from '@/components/FilterTabs.vue'
import { useMovies, useGenres } from '@/composables/useMovies'
import type { Movie } from '@/types/movie'

interface Favorite {
  id: number
  user_id: number
  movie_id: number
  movie_title: string
  poster_path: string | null
  overview: string | null
  vote_average: number | null
  release_date: string | null
  created_at: string
  updated_at: string
}

const {
  movies,
  loading,
  error,
  searchMovies,
  getPopularMovies,
  getNowPlayingMovies,
  getTopRatedMovies,
  getUpcomingMovies,
} = useMovies()

const { favorites, fetchFavorites } = useFavorites()
const { isAuthenticated } = useAuth()
const { genres, fetchGenres } = useGenres()

onMounted(async () => {
  if (isAuthenticated.value) {
    await fetchFavorites()
  }
  await fetchGenres()
})

const mapFavoriteToMovie = (favorite: Favorite): Movie => ({
  id: favorite.movie_id,
  title: favorite.movie_title,
  poster_path: favorite.poster_path,
  overview: favorite.overview || '',
  vote_average: favorite.vote_average || 0,
  release_date: favorite.release_date || '',
  backdrop_path: null,
  vote_count: 0,
  popularity: 0,
  genre_ids: [],
  original_language: '',
  original_title: favorite.movie_title,
  adult: false,
  video: false,
})

const selectedLanguage = ref('Todos')
const popularMovies = ref<Movie[]>([])
const nowPlayingMovies = ref<Movie[]>([])
const topRatedMovies = ref<Movie[]>([])
const allTopRatedMovies = ref<Movie[]>([])
const upcomingMovies = ref<Movie[]>([])
const isSearchActive = ref(false)
const currentSearchQuery = ref('')
const displayedSearchResults = ref<Movie[]>([])
const searchLoading = ref(false)
const currentPage = ref(1)
const totalPages = ref(1)
const isLoadingMore = ref(false)
const recommendedPage = ref(1)
const recommendedTotalPages = ref(1)
const isLoadingMoreRecommended = ref(false)

const genreFilters = computed(() => {
  if (!genres.value || genres.value.length === 0) {
    return [{ id: 'todos', label: 'Todos' }]
  }
  return [
    { id: 'todos', label: 'Todos' },
    ...genres.value.map(genre => ({
      id: genre.id.toString(),
      label: genre.name
    }))
  ]
})

const featuredMovie = computed(() => {
  return popularMovies.value.length > 0 ? popularMovies.value[0] : null
})

const mustWatchMovies = computed(() => {
  return nowPlayingMovies.value.slice(0, 10)
})

const recommendedMovies = computed(() => {
  if (selectedLanguage.value === 'Todos') {
    return allTopRatedMovies.value
  }
  const genreId = parseInt(selectedLanguage.value)
  return allTopRatedMovies.value.filter((movie) => movie.genre_ids.includes(genreId))
})

const searchResultsCount = computed(() => displayedSearchResults.value.length)

const handleSearch = async (query: string) => {
  if (!query || query.trim() === '') {
    clearSearch()
    return
  }

  currentSearchQuery.value = query
  isSearchActive.value = true
  searchLoading.value = true
  currentPage.value = 1
  displayedSearchResults.value = []

  try {
    const result = await searchMovies(query)
    displayedSearchResults.value = result.results || movies.value
    totalPages.value = result.total_pages || 1
    currentPage.value = 1
  } catch (err) {
    console.error('Erro ao buscar:', err)
    displayedSearchResults.value = []
  } finally {
    searchLoading.value = false
  }
}

const clearSearch = () => {
  isSearchActive.value = false
  currentSearchQuery.value = ''
  displayedSearchResults.value = []
  searchLoading.value = false
  currentPage.value = 1
  totalPages.value = 1
}

const loadMoreSearchResults = async () => {
  if (isLoadingMore.value || currentPage.value >= totalPages.value) {
    return
  }

  isLoadingMore.value = true
  currentPage.value++

  try {
    const result = await searchMovies(currentSearchQuery.value, currentPage.value)
    displayedSearchResults.value = [...displayedSearchResults.value, ...(result.results || [])]
  } catch (err) {
    console.error('Erro ao carregar mais resultados:', err)
  } finally {
    isLoadingMore.value = false
  }
}

const handleScroll = () => {
  const scrollPosition = window.innerHeight + window.scrollY
  const threshold = document.documentElement.scrollHeight - 500

  if (scrollPosition >= threshold) {
    if (isSearchActive.value) {
      loadMoreSearchResults()
    } else {
      loadMoreRecommended()
    }
  }
}

const loadMoreRecommended = async () => {
  if (isLoadingMoreRecommended.value || recommendedPage.value >= recommendedTotalPages.value) {
    return
  }

  isLoadingMoreRecommended.value = true
  recommendedPage.value++

  try {
    const result = await getTopRatedMovies(recommendedPage.value)
    allTopRatedMovies.value = [...allTopRatedMovies.value, ...(result.results || [])]
    topRatedMovies.value = [...topRatedMovies.value, ...(result.results || [])]
  } catch (err) {
    console.error('Erro ao carregar mais filmes recomendados:', err)
  } finally {
    isLoadingMoreRecommended.value = false
  }
}

const handleMovieClick = (movieId: number) => {
  console.log('Movie clicked:', movieId)
}

const handleWatch = (movieId: number) => {
  console.log('Watch movie:', movieId)
}

const handleAddToList = (movieId: number) => {
  console.log('Add to list:', movieId)
}

onMounted(async () => {
  try {
    const [popular, nowPlaying, topRated, upcoming] = await Promise.all([
      getPopularMovies(),
      getNowPlayingMovies(),
      getTopRatedMovies(),
      getUpcomingMovies(),
    ])

    popularMovies.value = popular
    nowPlayingMovies.value = nowPlaying
    topRatedMovies.value = topRated.results || []
    allTopRatedMovies.value = topRated.results || []
    recommendedTotalPages.value = topRated.total_pages || 1
    recommendedPage.value = 1
    upcomingMovies.value = upcoming

    // Adicionar event listener para scroll infinito
    window.addEventListener('scroll', handleScroll)
  } catch (err) {
    console.error('Erro ao carregar filmes:', err)
  }
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
.home-view {
  min-height: 100vh;
  background: linear-gradient(to bottom, #0a0a0a 0%, #1a1a1a 100%);
}

main {
  padding-bottom: 4rem;
}

.search-results-section {
  padding: 0 2rem;
  margin-top: 6rem;
}

.section-header {
  padding: 0 2rem;
  margin-bottom: 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.search-results-section .section-header {
  padding: 0;
  margin-bottom: 2rem;
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
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1.5rem;
  padding: 0 2rem;
}

.search-results-section .movies-grid {
  padding: 0;
}

.recommended-section {
  margin-top: 3rem;
}

.no-results-inline {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
  text-align: center;
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

.loading-state,
.error-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
  color: rgba(255, 255, 255, 0.7);
}

.loading-more {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  color: rgba(255, 255, 255, 0.7);
}

.loading-more .spinner {
  width: 32px;
  height: 32px;
  margin-bottom: 0.5rem;
}

.no-more-results {
  text-align: center;
  padding: 2rem;
  color: rgba(255, 255, 255, 0.5);
  font-size: 0.875rem;
}

.no-more-results p {
  margin: 0;
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

.error-state p {
  color: #ff4458;
  font-weight: 500;
}

@media (max-width: 1024px) {
  .movies-grid {
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  }
}

@media (max-width: 768px) {
  .search-results-section {
    padding: 0 1rem;
    margin-top: 5rem;
  }

  .section-header {
    padding: 0 1rem;
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

.favorites-section {
  margin-bottom: 3rem;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.section-header h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #fff;
}

.view-all {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  font-size: 0.875rem;
  transition: color 0.2s;
}

.view-all:hover {
  color: #fff;
}

.carousel-container {
  position: relative;
}

.carousel {
  display: flex;
  gap: 1rem;
  overflow-x: auto;
  scroll-behavior: smooth;
  padding: 0.5rem 0;
  
  /* Esconder scrollbar */
  scrollbar-width: none;
  -ms-overflow-style: none;
}

.carousel::-webkit-scrollbar {
  display: none;
}

.carousel-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(0, 0, 0, 0.7);
  border: none;
  border-radius: 50%;
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  cursor: pointer;
  z-index: 10;
  transition: all 0.2s;
  backdrop-filter: blur(10px);
}

.carousel-arrow:hover {
  background: rgba(0, 0, 0, 0.9);
  transform: translateY(-50%) scale(1.1);
}

.carousel-arrow.left {
  left: -24px;
}

.carousel-arrow.right {
  right: -24px;
}

.view-all-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  font-size: 0.875rem;
  font-weight: 500;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  transition: all 0.3s ease;
}

.view-all-button:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
  border-color: rgba(255, 255, 255, 0.2);
  transform: translateX(4px);
}

.view-all-button svg {
  transition: transform 0.3s ease;
}

.view-all-button:hover svg {
  transform: translateX(4px);
}

@media (max-width: 768px) {
  .carousel-arrow {
    display: none; /* Esconder setas no mobile, usar scroll touch */
  }

  .clear-search-button {
    font-size: 0.75rem;
    padding: 0.4rem 0.8rem;
  }
}

</style>
