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

export class AuthService {
  private readonly baseUrl = '/auth'

  async login(credentials: LoginCredentials): Promise<AuthResponse> {
    return await apiClient.post<AuthResponse>(`${this.baseUrl}/login`, credentials)
  }

  async register(data: RegisterData): Promise<AuthResponse> {
    return await apiClient.post<AuthResponse>(`${this.baseUrl}/register`, data)
  }

  async logout(): Promise<void> {
    await apiClient.post<void>(`${this.baseUrl}/logout`)
  }

  async getCurrentUser(): Promise<User> {
    return await apiClient.get<User>(`${this.baseUrl}/me`)
  }

  storeToken(token: string): void {
    localStorage.setItem('auth_token', token)
  }

  getToken(): string | null {
    return localStorage.getItem('auth_token')
  }

  removeToken(): void {
    localStorage.removeItem('auth_token')
  }

  isAuthenticated(): boolean {
    return this.getToken() !== null
  }
}

export const authService = new AuthService()
