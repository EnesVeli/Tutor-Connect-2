<template>
  <div class="card mb-3">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-start">
        <div class="flex-grow-1">
          <div class="d-flex align-items-center mb-2">
            <h6 class="mb-0 me-2">{{ booking.subject || 'Unknown Subject' }}</h6>
            <BookingStatusBadge :status="booking.status" />
          </div>
          <p class="text-muted small mb-1">
            <i class="bi bi-person me-1"></i>
            <strong>{{ userRole === 'student' ? 'Tutor' : 'Student' }}:</strong>
            {{ booking.first_name }} {{ booking.last_name }}
            <template v-if="userRole === 'tutor' && booking.date_of_birth">
              (Age: {{ calculateAge(booking.date_of_birth) }})
            </template>
          </p>
          <p class="text-muted small mb-1">
            <i class="bi bi-envelope me-1"></i>{{ booking.email }}
          </p>
          <p class="text-muted small mb-1">
            <i class="bi bi-calendar-event me-1"></i>{{ formatDate(booking.scheduled_at) }}
          </p>
          <p v-if="booking.student_comment" class="small mb-0 fst-italic">
            <i class="bi bi-chat-left-text me-1"></i>"{{ booking.student_comment }}"
          </p>
        </div>

        <div v-if="userRole === 'tutor' && booking.status === 'pending'" class="ms-3 d-flex flex-column gap-2">
          <button class="btn btn-sm btn-success" @click="$emit('status-update', { bookingId: booking.id, status: 'confirmed' })">
            <i class="bi bi-check-lg"></i> Accept
          </button>
          <button class="btn btn-sm btn-danger" @click="$emit('status-update', { bookingId: booking.id, status: 'cancelled' })">
            <i class="bi bi-x-lg"></i> Reject
          </button>
        </div>

        <div v-if="userRole === 'tutor' && booking.status === 'confirmed'" class="ms-3">
          <button class="btn btn-sm btn-outline-primary" @click="$emit('status-update', { bookingId: booking.id, status: 'completed' })">
            <i class="bi bi-check2-all"></i> Complete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import BookingStatusBadge from './BookingStatusBadge.vue'

defineProps({
  booking: { type: Object, required: true },
  userRole: { type: String, required: true }
})

defineEmits(['status-update'])

function formatDate(dateStr) {
  return new Date(dateStr).toLocaleString('en-GB', {
    weekday: 'short', year: 'numeric', month: 'short', day: 'numeric',
    hour: '2-digit', minute: '2-digit'
  })
}

function calculateAge(dob) {
  const today = new Date()
  const birth = new Date(dob)
  let age = today.getFullYear() - birth.getFullYear()
  const m = today.getMonth() - birth.getMonth()
  if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--
  return age
}
</script>
