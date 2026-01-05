export const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8080'

export const STORAGE_KEYS = {
  AUTH_TOKEN: 'auth_token',
  USER_PREFERENCES: 'user_preferences',
} as const

export const ROUTE_NAMES = {
  HOME: 'home',
  LOGIN: 'login',
  REGISTER: 'register',
  FAVORITES: 'favorites',
  MOVIES: 'movies',
  TV_SHOWS: 'tv-shows',
  NEW: 'new',
} as const

export const PAGINATION = {
  DEFAULT_PAGE: 1,
  DEFAULT_PAGE_SIZE: 20,
  MAX_PAGE_SIZE: 100,
} as const

export const IMAGE_SIZES = {
  POSTER_SMALL: 'w185',
  POSTER_MEDIUM: 'w342',
  POSTER_LARGE: 'w500',
  BACKDROP_SMALL: 'w300',
  BACKDROP_MEDIUM: 'w780',
  BACKDROP_LARGE: 'w1280',
} as const

export const TIMEOUTS = {
  API_REQUEST: 30000, // 30 seconds
  DEBOUNCE_SEARCH: 500, // 500ms
  TOAST_DISPLAY: 3000, // 3 seconds
} as const

export const ERROR_MESSAGES = {
  NETWORK_ERROR: 'Erro de conexão. Verifique sua internet.',
  UNAUTHORIZED: 'Você precisa estar logado para acessar esta página.',
  FORBIDDEN: 'Você não tem permissão para acessar este recurso.',
  NOT_FOUND: 'Recurso não encontrado.',
  SERVER_ERROR: 'Erro no servidor. Tente novamente mais tarde.',
  VALIDATION_ERROR: 'Dados inválidos. Verifique os campos.',
} as const

export const SUCCESS_MESSAGES = {
  LOGIN_SUCCESS: 'Login realizado com sucesso!',
  REGISTER_SUCCESS: 'Cadastro realizado com sucesso!',
  LOGOUT_SUCCESS: 'Logout realizado com sucesso!',
  FAVORITE_ADDED: 'Filme adicionado aos favoritos!',
  FAVORITE_REMOVED: 'Filme removido dos favoritos!',
} as const
