export const API_CONFIG = {
  BASE_URL: 'http://localhost:8080/api',
  IMAGE_BASE_URL: 'https://image.tmdb.org/t/p',
  PLACEHOLDER_IMAGE: 'https://via.placeholder.com/500x750?text=Sem+Imagem',
} as const

export const IMAGE_SIZES = {
  POSTER_SMALL: '/w154',
  POSTER_MEDIUM: '/w342',
  POSTER_LARGE: '/w500',
  BACKDROP_SMALL: '/w300',
  BACKDROP_MEDIUM: '/w780',
  BACKDROP_LARGE: '/w1280',
  BACKDROP_ORIGINAL: '/original',
} as const

export const getImageUrl = (
  path: string | null,
  size: string = IMAGE_SIZES.POSTER_LARGE,
): string => {
  if (!path) return API_CONFIG.PLACEHOLDER_IMAGE
  return `${API_CONFIG.IMAGE_BASE_URL}${size}${path}`
}

export const getBackdropUrl = (
  path: string | null,
  size: string = IMAGE_SIZES.BACKDROP_LARGE,
): string => {
  if (!path) return API_CONFIG.PLACEHOLDER_IMAGE
  return `${API_CONFIG.IMAGE_BASE_URL}${size}${path}`
}
