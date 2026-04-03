<template>
  <div class="container mt-5">
    <div class="page-header">
      <h1><i class="bi bi-calendar-check me-2"></i>Student Bookings</h1>
      <p>Manage bookings from your students.</p>
    </div>

    <LoadingSpinner v-if="loading" />

    <template v-else>
      <AlertMessage v-if="error" type="danger" :message="error" @close="error = null" />
      <AlertMessage v-if="success" type="success" :message="success" @close="success = null" />

      <template v-if="bookings.length > 0">
        <BookingCard v-for="booking in bookings" :key="booking.id" :booking="booking" user-role="tutor"
          @status-update="handleStatusUpdate" />
      </template>

      <div v-else class="empty-state">
        <i class="bi bi-calendar-x"></i>
        <h5>No Bookings Yet</h5>
        <p>Once students book your services, their bookings will appear here.</p>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api/axios'
import BookingCard from '@/components/bookings/BookingCard.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'
import AlertMessage from '@/components/ui/AlertMessage.vue'

const bookings = ref([])
const loading = ref(true)
const error = ref(null)
const success = ref(null)

onMounted(async () => {
  try {
    const res = await api.get('/bookings')
    bookings.value = res.data.data
  } catch {
    error.value = 'Failed to load bookings'
  } finally {
    loading.value = false
  }
})

async function handleStatusUpdate({ bookingId, status }) {
  try {
    const res = await api.put(`/bookings/${bookingId}`, { status })
    const index = bookings.value.findIndex(b => b.id === bookingId)
    if (index !== -1) {
      bookings.value[index] = { ...bookings.value[index], status: res.data.status }
    }
    success.value = `Booking ${status} successfully!`
  } catch (err) {
    error.value = err.response?.data?.error || 'Failed to update booking'
  }
}
</script>
