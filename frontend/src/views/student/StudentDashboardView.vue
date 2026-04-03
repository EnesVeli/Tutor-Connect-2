<template>
  <div class="container mt-5">
    <div class="page-header">
      <h1><i class="bi bi-grid-fill me-2"></i>Welcome, {{ authStore.userName }}!</h1>
      <p>Your student dashboard — find tutors, manage bookings, and more.</p>
    </div>

    <div class="row g-4">
      <div class="col-12 col-md-4">
        <RouterLink to="/tutors" class="text-decoration-none">
          <div class="card action-card h-100">
            <div class="card-body text-center p-4">
              <i class="bi bi-search action-icon d-block mb-3"></i>
              <h5 class="fw-bold">Find a Tutor</h5>
              <p class="text-muted small mb-0">Browse our directory of qualified tutors</p>
            </div>
          </div>
        </RouterLink>
      </div>
      <div class="col-12 col-md-4">
        <RouterLink to="/student/bookings" class="text-decoration-none">
          <div class="card action-card h-100">
            <div class="card-body text-center p-4">
              <i class="bi bi-calendar-check action-icon d-block mb-3"></i>
              <h5 class="fw-bold">My Bookings</h5>
              <p class="text-muted small mb-0">View your upcoming and past bookings</p>
            </div>
          </div>
        </RouterLink>
      </div>
      <div class="col-12 col-md-4">
        <RouterLink to="/student/profile" class="text-decoration-none">
          <div class="card action-card h-100">
            <div class="card-body text-center p-4">
              <i class="bi bi-person-circle action-icon d-block mb-3"></i>
              <h5 class="fw-bold">My Profile</h5>
              <p class="text-muted small mb-0">Edit your personal information</p>
            </div>
          </div>
        </RouterLink>
      </div>
    </div>

    <!-- Recent Bookings Preview -->
    <div class="mt-5">
      <h4 class="fw-bold mb-3"><i class="bi bi-clock-history me-2"></i>Recent Bookings</h4>
      <LoadingSpinner v-if="loading" />
      <template v-else-if="recentBookings.length > 0">
        <BookingCard v-for="booking in recentBookings" :key="booking.id" :booking="booking" user-role="student" />
      </template>
      <div v-else class="empty-state">
        <i class="bi bi-calendar-x"></i>
        <p>No bookings yet. Start by finding a tutor!</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/api/axios'
import BookingCard from '@/components/bookings/BookingCard.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'

const authStore = useAuthStore()
const recentBookings = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    const response = await api.get('/bookings')
    recentBookings.value = response.data.data.slice(0, 3)
  } catch {
    // Silently fail — dashboard still usable
  } finally {
    loading.value = false
  }
})
</script>
