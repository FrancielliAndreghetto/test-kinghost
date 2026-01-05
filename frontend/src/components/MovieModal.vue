<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="isOpen" class="modal-overlay" @click="handleClose">
        <div class="modal-container" @click.stop>
          <button class="modal-close" @click="handleClose" aria-label="Close modal">
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

          <div class="modal-backdrop">
            <div v-if="imageLoading" class="backdrop-skeleton"></div>
            <img
              v-if="movie.backdrop_path"
              :src="backdropUrl"
              :alt="movie.title"
              class="backdrop-image"
              @load="imageLoading = false"
              @error="imageLoading = false"
            />
            <div v-if="!movie.backdrop_path || !imageLoaded" class="backdrop-gradient-fallback"></div>
            <div class="backdrop-gradient"></div>
          </div>

          <div class="modal-content">
            <div class="modal-header">
              <h2 class="modal-title">{{ movie.title }}</h2>
            </div>
            <div class="modal-meta">
                <span class="rating">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                >
                    <path
                    d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"
                    />
                </svg>
                {{ movie.vote_average.toFixed(1) }}
                </span>
                <span v-if="movie.release_date" class="release-date">
                {{ formatYear(movie.release_date) }}
                </span>
            </div>

            <div class="modal-body">
              <h3 class="section-title">Sinopse</h3>
              <p class="overview">{{ movie.overview || 'Sinopse não disponível.' }}</p>

              <div class="modal-actions">

                <button class="action-button secondary" @click="handleToggleFavorite">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="20"
                    height="20"
                    viewBox="0 0 24 24"
                    :fill="isFavorite(movie.id) ? 'currentColor' : 'none'"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <path
                      d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"
                    ></path>
                  </svg>
                  {{ isFavorite(movie.id) ? 'Remover dos Favoritos' : 'Adicionar aos Favoritos' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { useFavorites } from '@/composables/useFavorites'
import type { Movie } from '@/types/movie'
import { getImageUrl, IMAGE_SIZES } from '@/config/api'

interface Props {
  movie: Movie
  isOpen: boolean
}

const props = defineProps<Props>()

const emit = defineEmits<{
  close: []
  watch: [movieId: number]
}>()

const { isFavorite, toggleFavorite } = useFavorites()
const imageLoading = ref(true)

// Reset imageLoading quando o modal abre
watch(() => props.isOpen, (newValue) => {
  if (newValue) {
    imageLoading.value = true
    console.log('Modal opened - Movie:', props.movie.title)
    console.log('Backdrop path:', props.movie.backdrop_path)
    console.log('Backdrop URL:', backdropUrl.value)
  }
})

const backdropUrl = computed(() => {
  if (!props.movie.backdrop_path) return ''
  return getImageUrl(props.movie.backdrop_path, IMAGE_SIZES.BACKDROP_LARGE)
})

const imageLoaded = computed(() => !imageLoading.value)

const formatYear = (date: string) => {
  return new Date(date).getFullYear()
}

const handleClose = () => {
  emit('close')
}

const handleWatch = () => {
  emit('watch', props.movie.id)
}

const handleToggleFavorite = async () => {
  try {
    await toggleFavorite(props.movie)
  } catch (error) {
    console.error('Error toggling favorite:', error)
  }
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 2rem;
  overflow-y: auto;
}

.modal-container {
  position: relative;
  width: 100%;
  max-width: 900px;
  background: linear-gradient(to bottom, #1a1a1a 0%, #0a0a0a 100%);
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
}

.modal-close {
  position: absolute;
  top: 1rem;
  right: 1rem;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: rgba(0, 0, 0, 0.8);
  border: none;
  color: #fff;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
  transition: all 0.3s ease;
}

.modal-close:hover {
  background: rgba(0, 0, 0, 0.9);
  transform: rotate(90deg);
}

.modal-backdrop {
  position: relative;
  width: 100%;
  height: 400px;
  overflow: hidden;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.backdrop-skeleton {
  position: absolute;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0.1) 25%,
    rgba(255, 255, 255, 0.2) 50%,
    rgba(255, 255, 255, 0.1) 75%
  );
  background-size: 200% 100%;
  animation: skeleton-loading 2s infinite;
  z-index: 1;
}

@keyframes skeleton-loading {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}

.backdrop-image {
  position: relative;
  width: 100%;
  height: 100%;
  object-fit: cover;
  z-index: 2;
}

.backdrop-gradient-fallback {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  z-index: 1;
}

.backdrop-gradient {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 200px;
  background: linear-gradient(to top, #1a1a1a 0%, transparent 100%);
}

.modal-content {
  padding: 2rem;
  position: relative;
  margin-top: -100px;
}

.modal-header {
  margin-bottom: 2rem;
}

.modal-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #fff;
  margin-bottom: 0.75rem;
  line-height: 1.2;
}

.modal-meta {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  font-size: 1rem;
}

.rating {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #fbbf24;
  font-weight: 600;
}

.release-date {
  color: rgba(255, 255, 255, 0.7);
  font-weight: 500;
}

.modal-body {
  margin-top: 2rem;
}

.section-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #fff;
  margin-bottom: 1rem;
}

.overview {
  color: rgba(255, 255, 255, 0.8);
  line-height: 1.8;
  font-size: 1rem;
  margin-bottom: 2rem;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.action-button {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.875rem 1.5rem;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  border: none;
}

.action-button.primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
}

.action-button.primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
}

.action-button.secondary {
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.action-button.secondary:hover {
  background: rgba(255, 255, 255, 0.15);
  border-color: rgba(255, 255, 255, 0.3);
}

/* Transitions */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-active .modal-container,
.modal-leave-active .modal-container {
  transition: all 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from .modal-container,
.modal-leave-to .modal-container {
  transform: scale(0.95) translateY(20px);
  opacity: 0;
}

@media (max-width: 768px) {
  .modal-overlay {
    padding: 0;
    align-items: flex-end;
  }

  .modal-container {
    max-width: 100%;
    border-radius: 16px 16px 0 0;
    max-height: 90vh;
    overflow-y: auto;
  }

  .modal-backdrop {
    height: 250px;
  }

  .modal-content {
    padding: 1.5rem;
    margin-top: -60px;
  }

  .modal-title {
    font-size: 1.75rem;
  }

  .modal-actions {
    flex-direction: column;
  }

  .action-button {
    width: 100%;
    justify-content: center;
  }
}
</style>
