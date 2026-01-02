<template>
  <header class="app-header">
    <div class="header-container">
      <div class="header-left">
        <router-link to="/" class="logo">
          <span class="logo-text">DRAMATIC</span>
        </router-link>

        <nav class="main-nav" aria-label="Main navigation">
          <router-link to="/" class="nav-link" active-class="active">HOME</router-link>
          <router-link to="/tv-shows" class="nav-link" active-class="active"> TV SHOW </router-link>
          <router-link to="/movies" class="nav-link" active-class="active">MOVIES</router-link>
          <router-link to="/new" class="nav-link" active-class="active">NEW</router-link>
        </nav>
      </div>

      <div class="header-right">
        <div class="search-container" :class="{ active: searchActive }">
          <svg
            class="search-icon"
            xmlns="http://www.w3.org/2000/svg"
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.35-4.35"></path>
          </svg>
          <input
            ref="searchInputRef"
            v-model="searchQuery"
            type="text"
            class="search-input"
            placeholder="Buscar filmes..."
            @focus="searchActive = true"
            @blur="handleSearchBlur"
            @input="handleSearchInput"
          />
          <button
            v-if="searchQuery"
            class="clear-button"
            @click="clearSearch"
            aria-label="Limpar busca"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="16"
              height="16"
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

        <button class="icon-button" aria-label="Grid view">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <rect x="3" y="3" width="7" height="7"></rect>
            <rect x="14" y="3" width="7" height="7"></rect>
            <rect x="14" y="14" width="7" height="7"></rect>
            <rect x="3" y="14" width="7" height="7"></rect>
          </svg>
        </button>

        <button class="icon-button" aria-label="Notifications">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
          </svg>
        </button>

        <button class="user-avatar" aria-label="User menu">
          <img
            src="https://i.pravatar.cc/100"
            alt="User avatar"
            width="32"
            height="32"
            loading="lazy"
          />
        </button>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const emit = defineEmits<{
  search: [query: string]
}>()

const searchQuery = ref('')
const searchActive = ref(false)
const searchInputRef = ref<HTMLInputElement | null>(null)
let searchTimeout: ReturnType<typeof setTimeout> | null = null

const handleSearchInput = () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }

  searchTimeout = setTimeout(() => {
    emit('search', searchQuery.value)
  }, 500)
}

const clearSearch = () => {
  searchQuery.value = ''
  emit('search', '')
  searchInputRef.value?.focus()
}

const handleSearchBlur = () => {
  setTimeout(() => {
    if (!searchQuery.value) {
      searchActive.value = false
    }
  }, 200)
}
</script>

<style scoped>
.app-header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  background: linear-gradient(to bottom, rgba(0, 0, 0, 0.9), rgba(0, 0, 0, 0.7));
  backdrop-filter: blur(10px);
  padding: 1rem 2rem;
  transition: background-color 0.3s ease;
}

.header-container {
  max-width: 1400px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 2rem;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 3rem;
}

.logo {
  text-decoration: none;
}

.logo-text {
  font-size: 1.5rem;
  font-weight: 700;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  letter-spacing: 0.05em;
}

.main-nav {
  display: flex;
  gap: 2rem;
}

.nav-link {
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  font-size: 0.875rem;
  font-weight: 500;
  letter-spacing: 0.1em;
  transition: color 0.3s ease;
  position: relative;
}

.nav-link:hover,
.nav-link.active {
  color: #fff;
}

.nav-link.active::after {
  content: '';
  position: absolute;
  bottom: -0.5rem;
  left: 0;
  right: 0;
  height: 2px;
  background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
}

.header-right {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.search-container {
  position: relative;
  display: flex;
  align-items: center;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 20px;
  padding: 0.5rem 1rem;
  transition: all 0.3s ease;
  width: 180px;
}

.search-container:hover,
.search-container.active {
  background: rgba(255, 255, 255, 0.15);
  border-color: rgba(255, 255, 255, 0.3);
  width: 300px;
}

.search-icon {
  color: rgba(255, 255, 255, 0.7);
  flex-shrink: 0;
}

.search-input {
  flex: 1;
  background: none;
  border: none;
  outline: none;
  color: #fff;
  font-size: 0.875rem;
  padding: 0 0.5rem;
  width: 100%;
}

.search-input::placeholder {
  color: rgba(255, 255, 255, 0.5);
}

.clear-button {
  background: none;
  border: none;
  color: rgba(255, 255, 255, 0.7);
  cursor: pointer;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color 0.2s ease;
  flex-shrink: 0;
}

.clear-button:hover {
  color: #fff;
}

.icon-button {
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color 0.3s ease;
  border-radius: 8px;
  width: 40px;
  height: 40px;
  background: none;
  border: none;
  color: rgba(255, 255, 255, 0.7);
  cursor: pointer;
}

.icon-button:hover {
  color: #fff;
  background: rgba(255, 255, 255, 0.1);
}

.user-avatar {
  background: none;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  padding: 0;
  cursor: pointer;
  overflow: hidden;
  transition: border-color 0.3s ease;
}

.user-avatar:hover {
  border-color: #fff;
}

.user-avatar img {
  display: block;
  width: 32px;
  height: 32px;
  object-fit: cover;
}

@media (max-width: 768px) {
  .app-header {
    padding: 1rem;
  }

  .main-nav {
    display: none;
  }

  .header-left {
    gap: 1rem;
  }

  .header-right {
    gap: 0.5rem;
  }

  .search-container {
    width: 150px;
  }

  .search-container:hover,
  .search-container.active {
    width: 200px;
  }
}
</style>
