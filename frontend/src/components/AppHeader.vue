<template>
  <header class="app-header">
    <div class="header-container">
      <div class="header-left">
        <router-link to="/" class="logo">
          <span class="logo-text">KINGHOST</span>
        </router-link>

        <nav class="main-nav" aria-label="Main navigation">
          <router-link to="/home" class="nav-link" active-class="active">Início</router-link>
          <router-link to="/favorites" class="nav-link" active-class="active">Favoritos </router-link>
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

        <div class="auth-section">
          <div v-if="isAuthenticated" class="user-section">
            <span class="user-name">Olá, {{ user?.name }}</span>
            <div class="user-menu">
              <button class="user-avatar" @click.stop="toggleUserMenu" aria-label="Menu do usuário" :title="`Clique para abrir menu (${user?.name})`">
                <img
                  src="https://i.pravatar.cc/100"
                  alt="Avatar do usuário"
                  width="32"
                  height="32"
                  loading="lazy"
                />
              </button>
              <div v-if="showUserMenu" class="user-dropdown" @click.stop>
                <button @click="handleLogout" class="dropdown-item logout-item">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16,17 21,12 16,7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                  </svg>
                  Sair da conta
                </button>
              </div>
            </div>
          </div>
          <div v-else class="auth-buttons">
            <router-link to="/login" class="auth-btn login-btn">Entrar</router-link>
            <router-link to="/register" class="auth-btn register-btn">Criar Conta</router-link>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { useAuth } from '@/composables/useAuth'

const { user, isAuthenticated, logout } = useAuth()

const emit = defineEmits<{
  search: [query: string]
}>()

const searchQuery = ref('')
const searchActive = ref(false)
const searchInputRef = ref<HTMLInputElement | null>(null)
const showUserMenu = ref(false)
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

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
}

const handleLogout = async () => {
  showUserMenu.value = false
  await logout()
}

// Fechar dropdown quando clicar fora
const closeDropdown = () => {
  showUserMenu.value = false
}

onMounted(() => {
  // Adicionar listener para fechar o dropdown ao clicar fora
  document.addEventListener('click', closeDropdown)
})

onUnmounted(() => {
  document.removeEventListener('click', closeDropdown)
})

// Expor método para limpar busca externamente
defineExpose({
  clearSearch
})
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

.auth-section {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-section {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-name {
  color: rgba(255, 255, 255, 0.9);
  font-size: 0.875rem;
  font-weight: 500;
}

.user-menu {
  position: relative;
}

.user-avatar {
  border-radius: 50%;
  overflow: hidden;
  transition: all 0.3s ease;
  background: none;
  border: 2px solid rgba(255, 255, 255, 0.2);
  cursor: pointer;
  padding: 0;
}

.user-avatar:hover {
  border-color: rgba(255, 255, 255, 0.4);
  transform: scale(1.05);
}

.user-dropdown {
  position: absolute;
  top: calc(100% + 0.5rem);
  right: 0;
  background: rgba(0, 0, 0, 0.95);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 8px;
  padding: 0.5rem;
  min-width: 140px;
  z-index: 9999;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
  animation: dropdownFade 0.2s ease-out;
}

@keyframes dropdownFade {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  width: 100%;
  padding: 0.75rem 1rem;
  background: none;
  border: none;
  color: rgba(255, 255, 255, 0.9);
  font-size: 0.875rem;
  cursor: pointer;
  border-radius: 6px;
  transition: background-color 0.2s ease;
}

.dropdown-item:hover {
  background: rgba(255, 255, 255, 0.1);
}

.logout-item:hover {
  background: rgba(239, 68, 68, 0.2);
  color: #fca5a5;
}

.auth-buttons {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.auth-btn {
  padding: 0.5rem 1rem;
  border-radius: 6px;
  text-decoration: none;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.3s ease;
  border: 1px solid transparent;
}

.login-btn {
  color: rgba(255, 255, 255, 0.9);
  border-color: rgba(255, 255, 255, 0.2);
}

.login-btn:hover {
  color: #fff;
  border-color: rgba(255, 255, 255, 0.4);
  background: rgba(255, 255, 255, 0.1);
}

.register-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
}

.register-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.user-avatar {
  background: none;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  padding: 0;
  cursor: pointer;
  overflow: hidden;
  transition: all 0.3s ease;
}

.user-avatar:hover {
  border-color: #667eea;
  transform: scale(1.05);
  box-shadow: 0 0 10px rgba(102, 126, 234, 0.4);
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
