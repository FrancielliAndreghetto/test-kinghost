import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '@/views/HomeView.vue'
import LoginView from '@/views/LoginView.vue'
import RegisterView from '@/views/RegisterView.vue'
import { useAuth } from '@/composables/useAuth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
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
      component: () => import('@/views/HomeView.vue'),
    },
    {
      path: '/movies',
      name: 'movies',
      component: () => import('@/views/HomeView.vue'),
    },
    {
      path: '/new',
      name: 'new',
      component: () => import('@/views/HomeView.vue'),
    },
  ],
})

// Navigation guards
router.beforeEach((to, from, next) => {
  const { isAuthenticated } = useAuth()
  
  if (to.meta.requiresGuest && isAuthenticated.value) {
    next('/')
    return
  }
  
  next()
})

export default router
