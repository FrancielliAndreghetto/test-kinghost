import { apiClient } from './apiClient'

export interface User {
  id: number
  name: string
  email: string
  created_at: string
  updated_at: string
}

export interface LoginCredentials {
  email: string
  password: string
}

export interface RegisterData {
  name: string
  email: string
  password: string
  password_confirmation: string
}

export interface AuthResponse {
  success: boolean
  user: User
  token: string
  token_type: string
}

/**
 * Authentication Service
 * Handles all authentication-related API calls
 * Following Service Pattern and Single Responsibility Principle
 */
export class AuthService {
  private readonly baseUrl = '/auth'

  /**
   * Login user
   */
  async login(credentials: LoginCredentials): Promise<AuthResponse> {
    return await apiClient.post<AuthResponse>(`${this.baseUrl}/login`, credentials)
  }

  /**
   * Register new user
   */
  async register(data: RegisterData): Promise<AuthResponse> {
    return await apiClient.post<AuthResponse>(`${this.baseUrl}/register`, data)
  }

  /**
   * Logout user
   */
  async logout(): Promise<void> {
    await apiClient.post<void>(`${this.baseUrl}/logout`)
  }

  /**
   * Get current user
   */
  async getCurrentUser(): Promise<User> {
    return await apiClient.get<User>(`${this.baseUrl}/me`)
  }

  /**
   * Store authentication token
   */
  storeToken(token: string): void {
    localStorage.setItem('auth_token', token)
  }

  /**
   * Get stored token
   */
  getToken(): string | null {
    return localStorage.getItem('auth_token')
  }

  /**
   * Remove stored token
   */
  removeToken(): void {
    localStorage.removeItem('auth_token')
  }

  /**
   * Check if user is authenticated
   */
  isAuthenticated(): boolean {
    return this.getToken() !== null
  }
}

// Singleton instance
export const authService = new AuthService()
