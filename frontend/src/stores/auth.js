import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/api/axios'
import { useRouter } from 'vue-router'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(null)
  const isLoading = ref(false)
  const error = ref(null)

  // Getters
  const isAuthenticated = computed(() => !!token.value)
  const userRole = computed(() => user.value?.role ?? null)
  const userName = computed(() => user.value?.first_name ?? '')

  // Actions
  async function login(email, password) {
    isLoading.value = true
    error.value = null
    try {
      const response = await api.post('/auth/login', { email, password })
      token.value = response.data.token
      user.value = response.data.user
      localStorage.setItem('token', response.data.token)
      localStorage.setItem('user', JSON.stringify(response.data.user))
      return response.data
    } catch (err) {
      error.value = err.response?.data?.error || 'Login failed'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  async function register(data) {
    isLoading.value = true
    error.value = null
    try {
      const response = await api.post('/auth/register', data)
      return response.data
    } catch (err) {
      error.value = err.response?.data?.error || 'Registration failed'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  function logout() {
    token.value = null
    user.value = null
    localStorage.removeItem('token')
    localStorage.removeItem('user')
  }

  function restoreFromStorage() {
    const savedToken = localStorage.getItem('token')
    const savedUser = localStorage.getItem('user')

    if (savedToken && savedUser) {
      // Decode JWT to check expiry (base64 decode the payload)
      try {
        const payload = JSON.parse(atob(savedToken.split('.')[1]))
        if (payload.exp && payload.exp * 1000 < Date.now()) {
          // Token expired
          logout()
          return
        }
        token.value = savedToken
        user.value = JSON.parse(savedUser)
      } catch {
        logout()
      }
    }
  }

  return {
    user,
    token,
    isLoading,
    error,
    isAuthenticated,
    userRole,
    userName,
    login,
    register,
    logout,
    restoreFromStorage
  }
})
