<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="show" class="modal-overlay" @click="handleOverlayClick">
        <div class="modal-container" @click.stop>
          <div class="modal-header">
            <div class="search-wrapper">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                class="search-icon"
              >
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
              </svg>
              <input
                ref="searchInputRef"
                v-model="searchQuery"
                type="text"
                placeholder="Buscar filmes, séries, pessoas..."
                class="search-input"
                @input="handleSearchInput"
                @keyup.esc="$emit('close')"
              />
              <button
                v-if="searchQuery"
                class="clear-button"
                @click="clearSearch"
                aria-label="Limpar busca"
              >
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
              </button>
            </div>
            <button class="close-button" @click="$emit('close')" aria-label="Fechar">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
            </button>
          </div>

          <div class="modal-body">
            <div v-if="loading" class="loading-state">
              <div class="spinner"></div>
              <p>Buscando...</p>
            </div>

            <div v-else-if="error" class="error-state">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="48"
                height="48"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
              </svg>
              <p>{{ error }}</p>
              <button class="retry-button" @click="handleSearchInput">Tentar novamente</button>
            </div>

            <div v-else-if="!searchQuery" class="empty-state">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="64"
                height="64"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
              >
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
              </svg>
              <h3>Buscar conteúdo</h3>
              <p>Digite o nome do filme, série ou pessoa que você procura</p>
            </div>

            <div v-else-if="searchQuery && results.length === 0 && !loading" class="no-results">
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
              <p>Não encontramos nada para "{{ searchQuery }}"</p>
              <p class="suggestion">Tente buscar por outro termo</p>
            </div>

            <div v-else class="results-grid">
              <div
                v-for="movie in results"
                :key="movie.id"
                class="result-item"
                @click="handleMovieClick(movie.id)"
              >
                <div class="result-poster">
                  <img :src="getPosterUrl(movie.poster_path)" :alt="movie.title" loading="lazy" />
                  <div class="result-overlay">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="32"
                      height="32"
                      viewBox="0 0 24 24"
                      fill="currentColor"
                    >
                      <polygon points="5 3 19 12 5 21 5 3"></polygon>
                    </svg>
                  </div>
                </div>
                <div class="result-info">
                  <h4>{{ movie.title }}</h4>
                  <div class="result-meta">
                    <span v-if="movie.release_date" class="year">
                      {{ new Date(movie.release_date).getFullYear() }}
                    </span>
                    <span class="rating">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="14"
                        height="14"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                      >
                        <path
                          d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"
                        />
                      </svg>
                      {{ movie.vote_average.toFixed(1) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, watch, nextTick } from 'vue'
import type { Movie } from '@/types/movie'
import { getImageUrl, IMAGE_SIZES } from '@/config/api'

interface Props {
  show: boolean
  results: Movie[]
  loading: boolean
  error: string | null
}

const props = defineProps<Props>()

const emit = defineEmits<{
  close: []
  search: [query: string]
  selectMovie: [movieId: number]
}>()

const searchQuery = ref('')
const searchInputRef = ref<HTMLInputElement | null>(null)
let searchTimeout: ReturnType<typeof setTimeout> | null = null

watch(
  () => props.show,
  (newVal) => {
    if (newVal) {
      nextTick(() => {
        searchInputRef.value?.focus()
      })
    } else {
      searchQuery.value = ''
    }
  },
)

const handleSearchInput = () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }

  searchTimeout = setTimeout(() => {
    if (searchQuery.value.trim()) {
      emit('search', searchQuery.value.trim())
    }
  }, 500) // Debounce de 500ms
}

const clearSearch = () => {
  searchQuery.value = ''
  searchInputRef.value?.focus()
}

const handleOverlayClick = () => {
  emit('close')
}

const handleMovieClick = (movieId: number) => {
  emit('selectMovie', movieId)
  emit('close')
}

const getPosterUrl = (path: string | null) => {
  return getImageUrl(path, IMAGE_SIZES.POSTER_MEDIUM)
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.85);
  backdrop-filter: blur(8px);
  display: flex;
  align-items: flex-start;
  justify-content: center;
  z-index: 9999;
  padding: 2rem;
  overflow-y: auto;
}

.modal-container {
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
  border-radius: 16px;
  width: 100%;
  max-width: 900px;
  margin-top: 2rem;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
  animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.modal-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.5rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.search-wrapper {
  flex: 1;
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 1rem;
  color: rgba(255, 255, 255, 0.5);
  pointer-events: none;
}

.search-input {
  width: 100%;
  padding: 1rem 3rem 1rem 3rem;
  background: rgba(255, 255, 255, 0.05);
  border: 2px solid rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  color: #fff;
  font-size: 1rem;
  outline: none;
  transition: all 0.3s ease;
}

.search-input:focus {
  background: rgba(255, 255, 255, 0.08);
  border-color: rgba(102, 126, 234, 0.5);
}

.search-input::placeholder {
  color: rgba(255, 255, 255, 0.4);
}

.clear-button,
.close-button {
  background: rgba(255, 255, 255, 0.05);
  border: none;
  color: rgba(255, 255, 255, 0.7);
  padding: 0.5rem;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.clear-button {
  position: absolute;
  right: 0.75rem;
}

.clear-button:hover,
.close-button:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
}

.modal-body {
  padding: 1.5rem;
  min-height: 400px;
  max-height: 600px;
  overflow-y: auto;
}

.loading-state,
.error-state,
.empty-state,
.no-results {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 400px;
  color: rgba(255, 255, 255, 0.7);
  text-align: center;
  padding: 2rem;
}

.spinner {
  width: 48px;
  height: 48px;
  border: 4px solid rgba(255, 255, 255, 0.1);
  border-top-color: #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.error-state svg {
  color: #ff4458;
  margin-bottom: 1rem;
}

.retry-button {
  margin-top: 1rem;
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.3s ease;
}

.retry-button:hover {
  transform: translateY(-2px);
}

.empty-state svg,
.no-results svg {
  margin-bottom: 1rem;
  opacity: 0.3;
}

.empty-state h3,
.no-results h3 {
  color: #fff;
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
}

.suggestion {
  font-size: 0.875rem;
  margin-top: 0.5rem;
  opacity: 0.6;
}

.results-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 1.5rem;
}

.result-item {
  cursor: pointer;
  transition: transform 0.3s ease;
}

.result-item:hover {
  transform: translateY(-4px);
}

.result-poster {
  position: relative;
  aspect-ratio: 2/3;
  border-radius: 12px;
  overflow: hidden;
  margin-bottom: 0.75rem;
}

.result-poster img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.result-item:hover .result-poster img {
  transform: scale(1.05);
}

.result-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.result-item:hover .result-overlay {
  opacity: 1;
}

.result-overlay svg {
  color: #fff;
}

.result-info h4 {
  color: #fff;
  font-size: 0.875rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.3;
}

.result-meta {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 0.75rem;
}

.year {
  color: rgba(255, 255, 255, 0.6);
}

.rating {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  color: #f5c518;
  font-weight: 600;
}

.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

@media (max-width: 768px) {
  .modal-overlay {
    padding: 0;
  }

  .modal-container {
    margin-top: 0;
    border-radius: 0;
    min-height: 100vh;
  }

  .results-grid {
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 1rem;
  }

  .modal-body {
    max-height: calc(100vh - 100px);
  }
}
</style>
