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

        <div v-else class="no-results-inline">
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

        <MovieCarousel title="MOVIES YOU MUST WATCH" :show-navigation="true">
          <MovieCard
            v-for="movie in mustWatchMovies"
            :key="movie.id"
            :movie="movie"
            @click="handleMovieClick"
          />
        </MovieCarousel>

        <div class="recommended-section">
          <div class="section-header">
            <h2 class="section-title">RECOMMENDED FOR YOU</h2>
          </div>

          <FilterTabs v-model="selectedLanguage" :filters="languageFilters" />

          <div class="movies-grid">
            <MovieCard
              v-for="movie in recommendedMovies"
              :key="movie.id"
              :movie="movie"
              @click="handleMovieClick"
            />
          </div>
        </div>

        <div v-if="loading" class="loading-state">
          <div class="spinner"></div>
          <p>Carregando filmes...</p>
        </div>

        <div v-if="error" class="error-state">
          <p>{{ error }}</p>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import AppHeader from '@/components/AppHeader.vue'
import HeroSection from '@/components/HeroSection.vue'
import MovieCarousel from '@/components/MovieCarousel.vue'
import MovieCard from '@/components/MovieCard.vue'
import FilterTabs from '@/components/FilterTabs.vue'
import { useMovies } from '@/composables/useMovies'
import type { Movie } from '@/types/movie'

const router = useRouter()
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

const selectedLanguage = ref('all')
const popularMovies = ref<Movie[]>([])
const nowPlayingMovies = ref<Movie[]>([])
const topRatedMovies = ref<Movie[]>([])
const upcomingMovies = ref<Movie[]>([])
const isSearchActive = ref(false)
const currentSearchQuery = ref('')
const displayedSearchResults = ref<Movie[]>([])
const searchLoading = ref(false)

const languageFilters = [
  { id: 'all', label: 'All' },
  { id: 'en', label: 'English' },
  { id: 'pt', label: 'Português' },
  { id: 'es', label: 'Español' },
  { id: 'fr', label: 'Français' },
  { id: 'it', label: 'Italiano' },
  { id: 'de', label: 'Deutsch' },
  { id: 'ja', label: '日本語' },
  { id: 'ko', label: '한국어' },
]

const featuredMovie = computed(() => {
  return popularMovies.value.length > 0 ? popularMovies.value[0] : null
})

const mustWatchMovies = computed(() => {
  return nowPlayingMovies.value.slice(0, 10)
})

const recommendedMovies = computed(() => {
  if (selectedLanguage.value === 'all') {
    return topRatedMovies.value
  }
  return topRatedMovies.value.filter((movie) => movie.original_language === selectedLanguage.value)
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

  try {
    await searchMovies(query)
    displayedSearchResults.value = movies.value
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
    topRatedMovies.value = topRated
    upcomingMovies.value = upcoming
  } catch (err) {
    console.error('Erro ao carregar filmes:', err)
  }
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
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
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
</style>
