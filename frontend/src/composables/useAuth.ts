import { ref, computed } from 'vue'
import axios from 'axios'

interface User {
  id: number
  name: string
  email: string
  email_verified_at: string | null
  created_at: string
  updated_at: string
}

interface LoginCredentials {
  email: string
  password: string
}

interface RegisterCredentials {
  name: string
  email: string
  password: string
  password_confirmation: string
}

interface AuthResponse {
  message: string
  user: User
  token: string
}

const user = ref<User | null>(null)
const token = ref<string | null>(null)
const loading = ref(false)
const error = ref<string | null>(null)

// Configure axios defaults
axios.defaults.baseURL = 'http://localhost:8080'
axios.defaults.headers.common['Accept'] = 'application/json'
axios.defaults.headers.common['Content-Type'] = 'application/json'

export function useAuth() {
  const isAuthenticated = computed(() => !!user.value && !!token.value)

  const setAuthToken = (authToken: string) => {
    token.value = authToken
    localStorage.setItem('auth_token', authToken)
    axios.defaults.headers.common['Authorization'] = `Bearer ${authToken}`
  }

  const clearAuth = () => {
    user.value = null
    token.value = null
    localStorage.removeItem('auth_token')
    delete axios.defaults.headers.common['Authorization']
  }

  const initializeAuth = () => {
    const savedToken = localStorage.getItem('auth_token')
    if (savedToken) {
      token.value = savedToken
      axios.defaults.headers.common['Authorization'] = `Bearer ${savedToken}`
      // Try to get user data
      getCurrentUser()
    }
  }

  const getCurrentUser = async () => {
    if (!token.value) return

    try {
      const response = await axios.get('/api/auth/me')
      user.value = response.data.user
    } catch (err) {
      clearAuth()
      console.error('Failed to get current user:', err)
    }
  }

  const login = async (credentials: LoginCredentials): Promise<void> => {
    loading.value = true
    error.value = null

    try {
      const response = await axios.post<AuthResponse>('/api/auth/login', credentials)
      const { user: userData, token: authToken } = response.data

      user.value = userData
      setAuthToken(authToken)
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Login failed'
      error.value = errorMessage
      throw new Error(errorMessage)
    } finally {
      loading.value = false
    }
  }

  const register = async (credentials: RegisterCredentials): Promise<void> => {
    loading.value = true
    error.value = null

    try {
      const response = await axios.post<AuthResponse>('/api/auth/register', credentials)
      const { user: userData, token: authToken } = response.data

      user.value = userData
      setAuthToken(authToken)
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || 'Registration failed'
      error.value = errorMessage
      throw new Error(errorMessage)
    } finally {
      loading.value = false
    }
  }

  const logout = async (): Promise<void> => {
    loading.value = true
    error.value = null

    try {
      await axios.post('/api/auth/logout')
    } catch (err) {
      console.error('Logout error:', err)
    } finally {
      clearAuth()
      loading.value = false
    }
  }

  // Initialize auth on composable creation
  initializeAuth()

  return {
    user: computed(() => user.value),
    token: computed(() => token.value),
    loading: computed(() => loading.value),
    error: computed(() => error.value),
    isAuthenticated,
    login,
    register,
    logout,
    getCurrentUser,
    clearAuth,
  }
}