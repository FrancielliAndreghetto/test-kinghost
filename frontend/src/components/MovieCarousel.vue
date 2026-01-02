<template>
  <section class="movie-carousel">
    <div class="carousel-header">
      <h2 class="carousel-title">{{ title }}</h2>
      <button v-if="showFilter" class="filter-button" @click="$emit('toggleFilter')">
        FILTERS
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="16"
          height="16"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
        </svg>
      </button>
    </div>

    <div class="carousel-container" ref="carouselRef">
      <button
        v-if="showNavigation && canScrollLeft"
        class="nav-button nav-left"
        @click="scroll('left')"
        aria-label="Scroll left"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
      </button>

      <div class="carousel-track" ref="trackRef" @scroll="updateScrollState">
        <slot></slot>
      </div>

      <button
        v-if="showNavigation && canScrollRight"
        class="nav-button nav-right"
        @click="scroll('right')"
        aria-label="Scroll right"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
      </button>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

interface Props {
  title: string
  showFilter?: boolean
  showNavigation?: boolean
}

withDefaults(defineProps<Props>(), {
  showFilter: false,
  showNavigation: true,
})

defineEmits<{
  toggleFilter: []
}>()

const trackRef = ref<HTMLElement | null>(null)
const carouselRef = ref<HTMLElement | null>(null)
const canScrollLeft = ref(false)
const canScrollRight = ref(false)

const updateScrollState = () => {
  if (!trackRef.value) return

  const { scrollLeft, scrollWidth, clientWidth } = trackRef.value
  canScrollLeft.value = scrollLeft > 0
  canScrollRight.value = scrollLeft < scrollWidth - clientWidth - 10
}

const scroll = (direction: 'left' | 'right') => {
  if (!trackRef.value) return

  const scrollAmount = trackRef.value.clientWidth * 0.8
  const targetScroll =
    direction === 'left'
      ? trackRef.value.scrollLeft - scrollAmount
      : trackRef.value.scrollLeft + scrollAmount

  trackRef.value.scrollTo({
    left: targetScroll,
    behavior: 'smooth',
  })
}

let resizeObserver: ResizeObserver | null = null

onMounted(() => {
  updateScrollState()

  if (trackRef.value) {
    resizeObserver = new ResizeObserver(updateScrollState)
    resizeObserver.observe(trackRef.value)
  }
})

onUnmounted(() => {
  resizeObserver?.disconnect()
})
</script>

<style scoped>
.movie-carousel {
  padding: 2rem 0;
}

.carousel-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding: 0 2rem;
}

.carousel-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #fff;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.filter-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
  border: 1px solid rgba(255, 255, 255, 0.2);
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.filter-button:hover {
  background: rgba(255, 255, 255, 0.15);
  border-color: rgba(255, 255, 255, 0.3);
}

.carousel-container {
  position: relative;
}

.carousel-track {
  display: flex;
  gap: 1.5rem;
  overflow-x: auto;
  scroll-behavior: smooth;
  padding: 0 2rem;
  scrollbar-width: none;
  -ms-overflow-style: none;
}

.carousel-track::-webkit-scrollbar {
  display: none;
}

.carousel-track > :deep(*) {
  flex: 0 0 auto;
  width: 200px;
}

.nav-button {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(0, 0, 0, 0.8);
  backdrop-filter: blur(10px);
  color: #fff;
  border: none;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 10;
  transition: all 0.3s ease;
  opacity: 0;
}

.carousel-container:hover .nav-button {
  opacity: 1;
}

.nav-button:hover {
  background: rgba(0, 0, 0, 0.95);
  transform: translateY(-50%) scale(1.1);
}

.nav-left {
  left: 0.5rem;
}

.nav-right {
  right: 0.5rem;
}

@media (max-width: 768px) {
  .carousel-header {
    padding: 0 1rem;
  }

  .carousel-track {
    padding: 0 1rem;
    gap: 1rem;
  }

  .carousel-track > :deep(*) {
    width: 150px;
  }

  .nav-button {
    display: none;
  }
}
</style>
