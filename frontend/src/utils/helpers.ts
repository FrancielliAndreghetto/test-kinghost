export const isValidEmail = (email: string): boolean => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

export const isValidPassword = (password: string): boolean => {
  return password.length >= 6
}

export const isRequired = (value: string): boolean => {
  return value.trim().length > 0
}

export const minLength = (value: string, min: number): boolean => {
  return value.length >= min
}

export const maxLength = (value: string, max: number): boolean => {
  return value.length <= max
}

export const passwordsMatch = (password: string, confirmation: string): boolean => {
  return password === confirmation
}

export const formatDate = (date: string): string => {
  return new Date(date).toLocaleDateString('pt-BR')
}

export const formatYear = (date: string): number => {
  return new Date(date).getFullYear()
}

export const truncate = (text: string, maxLength: number): string => {
  if (text.length <= maxLength) return text
  return text.substring(0, maxLength) + '...'
}

export const formatRating = (rating: number): string => {
  return rating.toFixed(1)
}

export const clamp = (value: number, min: number, max: number): number => {
  return Math.min(Math.max(value, min), max)
}

export const unique = <T>(array: T[]): T[] => {
  return [...new Set(array)]
}

export const chunk = <T>(array: T[], size: number): T[][] => {
  const chunks: T[][] = []
  for (let i = 0; i < array.length; i += size) {
    chunks.push(array.slice(i, i + size))
  }
  return chunks
}

export const isEmpty = (obj: object): boolean => {
  return Object.keys(obj).length === 0
}

export const pick = <T extends object, K extends keyof T>(obj: T, keys: K[]): Pick<T, K> => {
  const result = {} as Pick<T, K>
  keys.forEach((key) => {
    if (key in obj) {
      result[key] = obj[key]
    }
  })
  return result
}

export const debounce = <T extends (...args: never[]) => unknown>(
  func: T,
  wait: number,
): ((...args: Parameters<T>) => void) => {
  let timeout: ReturnType<typeof setTimeout> | null = null

  return (...args: Parameters<T>) => {
    if (timeout) clearTimeout(timeout)
    timeout = setTimeout(() => func(...args), wait)
  }
}

export const throttle = <T extends (...args: never[]) => unknown>(
  func: T,
  limit: number,
): ((...args: Parameters<T>) => void) => {
  let inThrottle: boolean = false

  return (...args: Parameters<T>) => {
    if (!inThrottle) {
      func(...args)
      inThrottle = true
      setTimeout(() => (inThrottle = false), limit)
    }
  }
}
