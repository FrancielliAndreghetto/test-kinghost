import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '@/views/HomeView.vue'
import LoginView from '@/views/LoginView.vue'
import RegisterView from '@/views/RegisterView.vue'
import FavoritesView from '@/views/FavoritesView.vue'
import { useAuth } from '@/composables/useAuth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
      meta: { requiresAuth: true },
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView,
      meta: { requiresGuest: true },
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView,
      meta: { requiresGuest: true },
    },
    {
      path: '/tv-shows',
      name: 'tv-shows',
      component: HomeView,
      meta: { requiresAuth: true },
    },
    {
      path: '/movies',
      name: 'movies',
      component: HomeView,
      meta: { requiresAuth: true },
    },
    {
      path: '/new',
      name: 'new',
      component: HomeView,
      meta: { requiresAuth: true },
    },
    {
      path: '/favorites',
      name: 'favorites',
      component: FavoritesView,
      meta: { requiresAuth: true }
    }
  ],
})

// Navigation guards
router.beforeEach(async (to, from, next) => {
  const { isAuthenticated, isInitialized, initializeAuth } = useAuth()
  
  // Aguarda a inicialização da autenticação na primeira navegação
  if (!isInitialized.value) {
    await initializeAuth()
  }
  
  // Redireciona usuários autenticados tentando acessar login/register
  if (to.meta.requiresGuest && isAuthenticated.value) {
    next('/')
    return
  }
  
  // Redireciona usuários não autenticados tentando acessar rotas protegidas
  if (to.meta.requiresAuth && !isAuthenticated.value) {
    next('/login')
    return
  }
  
  next()
})

export default router
