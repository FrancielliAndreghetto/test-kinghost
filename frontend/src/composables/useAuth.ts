import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { authService, type User, type LoginCredentials, type RegisterData } from '@/services/authService'
import { useFavorites } from './useFavorites'
import { eventBus, AppEvents } from '@/utils/eventBus'
import { errorHandler } from '@/utils/errorHandler'

const user = ref<User | null>(null)
const token = ref<string | null>(null)
const loading = ref(false)
const error = ref<string | null>(null)
const isInitialized = ref(false)
const { fetchFavorites } = useFavorites()

export function useAuth() {
  const isAuthenticated = computed(() => !!user.value && !!token.value)
  const router = useRouter()

  const setAuthToken = (authToken: string): void => {
    token.value = authToken
    authService.storeToken(authToken)
  }

  const clearAuth = (): void => {
    user.value = null
    token.value = null
    authService.removeToken()
    eventBus.emit(AppEvents.USER_LOGGED_OUT)
  }

  const initializeAuth = async (): Promise<void> => {
    const savedToken = authService.getToken()
    
    if (savedToken) {
      token.value = savedToken
      await getCurrentUser()
    }
    
    isInitialized.value = true
  }

  const getCurrentUser = async (): Promise<void> => {
    if (!token.value) return

    try {
      user.value = await authService.getCurrentUser()
      await fetchFavorites()
    } catch (err) {
      clearAuth()
      errorHandler.handle(err as Error, 'getCurrentUser')
    }
  }

  const login = async (credentials: LoginCredentials): Promise<void> => {
    loading.value = true
    error.value = null

    try {
      const response = await authService.login(credentials)
      
      user.value = response.user
      setAuthToken(response.token)
      
      await fetchFavorites()
      
      eventBus.emit(AppEvents.USER_LOGGED_IN, response.user)
    } catch (err: unknown) {
      const apiError = err as { response?: { data?: { message?: string; errors?: Record<string, string[]> } } }
      const errorMessage = apiError.response?.data?.message || 
                          (apiError.response?.data?.errors ? 
                          Object.values(apiError.response.data.errors).flat().join(' ') : 
                          'Login failed')
      
      error.value = errorMessage
      errorHandler.handle(errorMessage, 'login')
      throw new Error(errorMessage)
    } finally {
      loading.value = false
    }
  }

  const register = async (credentials: RegisterData): Promise<void> => {
    loading.value = true
    error.value = null

    try {
      const response = await authService.register(credentials)
      
      user.value = response.user
      setAuthToken(response.token)
      
      eventBus.emit(AppEvents.USER_LOGGED_IN, response.user)
    } catch (err: unknown) {
      const apiError = err as { response?: { data?: { message?: string; errors?: Record<string, string[]> } } }
      const errorMessage = apiError.response?.data?.message || 
                          (apiError.response?.data?.errors ? 
                          Object.values(apiError.response.data.errors).flat().join(' ') : 
                          'Registration failed')
      
      error.value = errorMessage
      errorHandler.handle(errorMessage, 'register')
      throw new Error(errorMessage)
    } finally {
      loading.value = false
    }
  }

  const logout = async (): Promise<void> => {
    loading.value = true
    error.value = null

    try {
      await authService.logout()
    } catch (err) {
      errorHandler.handle(err as Error, 'logout')
    } finally {
      clearAuth()
      loading.value = false
      router.push('/login')
    }
  }

  return {
    user: computed(() => user.value),
    token: computed(() => token.value),
    loading: computed(() => loading.value),
    error: computed(() => error.value),
    isAuthenticated,
    isInitialized: computed(() => isInitialized.value),
    login,
    register,
    logout,
    getCurrentUser,
    initializeAuth,
    clearAuth,
  }
}
