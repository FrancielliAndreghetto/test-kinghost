<template>
  <section class="hero-section" :style="{ backgroundImage: `url(${backdropUrl})` }">
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <div class="hero-info">
        <h1 class="hero-title">{{ movie.title || movie.original_title }}</h1>

        <div class="hero-meta">
          <span class="meta-item rating">
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
          <span class="meta-item">{{ movie.original_language.toUpperCase() }}</span>
          <span class="meta-item">{{ movie.adult ? '18+' : 'PG' }}</span>
          <span class="meta-item">{{ releaseYear }}</span>
        </div>

        <p class="hero-description">{{ movie.overview }}</p>

        <div class="hero-genres">
          <span v-for="genre in displayGenres" :key="genre" class="genre-tag">
            {{ genre }}
          </span>
        </div>

        <div class="hero-actions">
          <button class="btn btn-primary" @click="$emit('watch', movie.id)">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="20"
              height="20"
              viewBox="0 0 24 24"
              fill="currentColor"
            >
              <polygon points="5 3 19 12 5 21 5 3"></polygon>
            </svg>
            WATCH
          </button>
          <button class="btn btn-secondary" @click="$emit('addToList', movie.id)">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="20"
              height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            MY LIST
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { Movie } from '@/types/movie'
import { getBackdropUrl, IMAGE_SIZES } from '@/config/api'

interface Props {
  movie: Movie
  genres?: string[]
}

const props = withDefaults(defineProps<Props>(), {
  genres: () => ['Romance', 'Drama'],
})

defineEmits<{
  watch: [movieId: number]
  addToList: [movieId: number]
}>()

const backdropUrl = computed(() =>
  getBackdropUrl(props.movie.backdrop_path, IMAGE_SIZES.BACKDROP_ORIGINAL),
)

const releaseYear = computed(() => {
  if (!props.movie.release_date) return 'N/A'
  return new Date(props.movie.release_date).getFullYear()
})

const displayGenres = computed(() => props.genres.slice(0, 2))
</script>

<style scoped>
.hero-section {
  position: relative;
  min-height: 600px;
  display: flex;
  align-items: flex-end;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  margin-top: 80px;
  overflow: hidden;
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to right,
    rgba(0, 0, 0, 0.95) 0%,
    rgba(0, 0, 0, 0.7) 50%,
    rgba(0, 0, 0, 0.3) 100%
  );
}

.hero-content {
  position: relative;
  z-index: 10;
  padding: 3rem 2rem;
  max-width: 1400px;
  margin: 0 auto;
  width: 100%;
}

.hero-info {
  max-width: 550px;
}

.hero-title {
  font-size: 3rem;
  font-weight: 700;
  color: #fff;
  margin-bottom: 1rem;
  line-height: 1.2;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.hero-meta {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  color: rgba(255, 255, 255, 0.9);
  font-size: 0.875rem;
  font-weight: 500;
  padding: 0.25rem 0.75rem;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 4px;
  backdrop-filter: blur(10px);
}

.meta-item.rating {
  background: #f5c518;
  color: #000;
  font-weight: 600;
}

.hero-description {
  color: rgba(255, 255, 255, 0.9);
  font-size: 1rem;
  line-height: 1.6;
  margin-bottom: 1.5rem;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.hero-genres {
  display: flex;
  gap: 0.75rem;
  margin-bottom: 2rem;
}

.genre-tag {
  color: rgba(255, 255, 255, 0.8);
  font-size: 0.875rem;
  font-weight: 500;
}

.hero-actions {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.875rem 2rem;
  font-size: 0.875rem;
  font-weight: 600;
  letter-spacing: 0.05em;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
}

.btn-secondary {
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
  border: 2px solid rgba(255, 255, 255, 0.3);
  backdrop-filter: blur(10px);
}

.btn-secondary:hover {
  background: rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.5);
  transform: translateY(-2px);
}

@media (max-width: 768px) {
  .hero-section {
    min-height: 500px;
    margin-top: 70px;
  }

  .hero-content {
    padding: 2rem 1rem;
  }

  .hero-title {
    font-size: 2rem;
  }

  .hero-meta {
    flex-wrap: wrap;
  }

  .btn {
    padding: 0.75rem 1.5rem;
    font-size: 0.8rem;
  }
}
</style>
