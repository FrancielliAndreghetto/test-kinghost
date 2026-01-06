<template>
  <article class="movie-card">
    <div class="card-image" @click="$emit('click', movie.id)">
      <img :src="posterUrl" :alt="movie.title" loading="lazy" />
      <div class="card-overlay">
        <button class="info-button" @click.stop="openModal" aria-label="Info movie">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="32"
            height="32"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="16" x2="12" y2="12"></line>
            <line x1="12" y1="8" x2="12.01" y2="8"></line>
          </svg>
        </button>
      </div>
    </div>

    <div class="card-content">
      <h3 class="card-title">{{ movie.title }}</h3>

      <div class="card-meta">
        <span class="rating" v-if="movie.vote_average">
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

        <button class="favorite-button" @click.stop="handleToggleFavorite" :aria-label="favoriteLabel">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="16"
            height="16"
            viewBox="0 0 24 24"
            :fill="isFavorite(movie.id) ? 'currentColor' : 'none'"
            stroke="currentColor"
            stroke-width="2"
          >
            <path
              d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"
            ></path>
          </svg>
        </button>
      </div>
    </div>

    <MovieModal 
      :movie="movieWithFavoriteData" 
      :is-open="isModalOpen" 
      @close="closeModal"
      @watch="handleWatch"
    />
  </article>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useFavorites } from '@/composables/useFavorites'
import type { Movie } from '@/types/movie'
import { getImageUrl, IMAGE_SIZES } from '@/config/api'
import MovieModal from './MovieModal.vue'

interface Props {
  movie: Movie
}

const props = defineProps<Props>()

defineEmits<{
  click: [movieId: number]
}>()

const { isFavorite, toggleFavorite, favorites } = useFavorites()

const isModalOpen = ref(false)

const posterUrl = computed(() => getImageUrl(props.movie.poster_path, IMAGE_SIZES.POSTER_MEDIUM))

const favoriteLabel = computed(() =>
  isFavorite(props.movie.id) ? 'Remove from favorites' : 'Add to favorites',
)

// Merge movie with favorite data if exists
const movieWithFavoriteData = computed(() => {
  const favorite = favorites.value.find(f => f.movie_id === props.movie.id)
  if (favorite && favorite.backdrop_path) {
    return {
      ...props.movie,
      backdrop_path: favorite.backdrop_path
    }
  }
  return props.movie
})

const openModal = () => {
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
}

const handleWatch = (movieId: number) => {
  console.log('Watch movie:', movieId)
  closeModal()
}

const handleToggleFavorite = async () => {
  try {
    await toggleFavorite(props.movie)
  } catch (err) {
    console.error('Failed to toggle favorite:', err)
  }
}
</script>

<style scoped>
.movie-card {
  cursor: pointer;
  border-radius: 12px;
  overflow: hidden;
  background: rgba(255, 255, 255, 0.05);
  transition: all 0.3s ease;
}

.movie-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
}

.card-image {
  position: relative;
  aspect-ratio: 2/3;
  overflow: hidden;
}

.card-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.movie-card:hover .card-image img {
  transform: scale(1.05);
}

.card-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.movie-card:hover .card-overlay {
  opacity: 1;
}

.info-button {
  background: rgba(255, 255, 255, 0.95);
  color: #000;
  border: none;
  width: 56px;
  height: 56px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
}

.info-button:hover {
  transform: scale(1.1);
  background: #fff;
}

.card-content {
  padding: 1rem;
}

.card-title {
  font-size: 1rem;
  font-weight: 600;
  color: #fff;
  margin-bottom: 0.5rem;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.card-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.rating {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  color: #f5c518;
  font-size: 0.875rem;
  font-weight: 600;
}

.favorite-button {
  background: none;
  border: none;
  color: #ff4458;
  cursor: pointer;
  padding: 0.25rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.3s ease;
}

.favorite-button:hover {
  transform: scale(1.2);
}

@media (max-width: 768px) {
  .card-title {
    font-size: 0.875rem;
  }

  .rating {
    font-size: 0.75rem;
  }
}
</style>
