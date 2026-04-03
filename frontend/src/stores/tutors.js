import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/api/axios'

export const useTutorStore = defineStore('tutors', () => {
  const tutors = ref([])
  const totalTutors = ref(0)
  const currentPage = ref(1)
  const totalPages = ref(1)
  const filters = ref({ subject: '', minPrice: null, maxPrice: null })
  const isLoading = ref(false)
  const error = ref(null)

  // Getters
  const hasResults = computed(() => tutors.value.length > 0)

  // Actions
  async function fetchTutors(newFilters = null, page = null) {
    if (newFilters !== null) {
      filters.value = { ...filters.value, ...newFilters }
    }
    if (page !== null) {
      currentPage.value = page
    }

    isLoading.value = true
    error.value = null

    try {
      const params = {
        page: currentPage.value,
        limit: 9
      }
      if (filters.value.subject) params.subject = filters.value.subject
      if (filters.value.minPrice) params.min_price = filters.value.minPrice
      if (filters.value.maxPrice) params.max_price = filters.value.maxPrice

      const response = await api.get('/tutors', { params })
      tutors.value = response.data.data
      totalTutors.value = response.data.total
      totalPages.value = response.data.totalPages
      currentPage.value = response.data.page
    } catch (err) {
      error.value = err.response?.data?.error || 'Failed to load tutors'
    } finally {
      isLoading.value = false
    }
  }

  function setFilters(newFilters) {
    fetchTutors(newFilters, 1) // Reset to page 1 on filter change
  }

  function setPage(page) {
    fetchTutors(null, page)
  }

  return {
    tutors,
    totalTutors,
    currentPage,
    totalPages,
    filters,
    isLoading,
    error,
    hasResults,
    fetchTutors,
    setFilters,
    setPage
  }
})
