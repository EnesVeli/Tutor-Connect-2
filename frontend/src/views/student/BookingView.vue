<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-8 col-lg-6">
        <div class="page-header">
          <h1><i class="bi bi-calendar-plus me-2"></i>Book a Lesson</h1>
        </div>

        <LoadingSpinner v-if="loading" />

        <template v-else-if="tutor">
          <!-- Tutor Summary -->
          <div class="card mb-4">
            <div class="card-body d-flex align-items-center">
              <img :src="avatarUrl" class="tutor-avatar me-3" :alt="tutorName">
              <div>
                <h5 class="mb-0 fw-bold">{{ tutorName }}</h5>
                <span class="badge bg-primary-subtle text-primary">{{ tutor.subject }}</span>
                <span class="ms-2 fw-bold text-primary">€{{ tutor.hourly_rate }}/hr</span>
              </div>
            </div>
          </div>

          <AlertMessage v-if="error" type="danger" :message="error" @close="error = null" />
          <AlertMessage v-if="success" type="success" :message="success" @close="success = null" />

          <div class="card">
            <div class="card-body p-4">
              <form @submit.prevent="handleBooking">
                <div class="mb-3">
                  <label class="form-label">Select Date</label>
                  <input type="date" class="form-control" v-model="form.date" :min="minDate" required>
                  <div class="form-text">
                    Available days: <strong>{{ tutor.available_days }}</strong>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Select Time</label>
                  <select class="form-select" v-model="form.time" required>
                    <option value="" disabled>Choose a time slot</option>
                    <option v-for="slot in timeSlots" :key="slot" :value="slot">{{ slot }}</option>
                  </select>
                  <div class="form-text">
                    Available hours: <strong>{{ tutor.availability_start }} – {{ tutor.availability_end }}</strong>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Message to Tutor (optional)</label>
                  <textarea class="form-control" v-model="form.comment" rows="3" placeholder="Tell the tutor what you'd like to focus on..."></textarea>
                </div>

                <!-- Payment -->
                <div class="card bg-light mb-4">
                  <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-credit-card me-2"></i>Payment Details</h6>
                    <div class="mb-2">
                      <input type="text" class="form-control" placeholder="4242 4242 4242 4242" maxlength="19">
                    </div>
                    <div class="row g-2">
                      <div class="col-6">
                        <input type="text" class="form-control" placeholder="MM/YY" maxlength="5">
                      </div>
                      <div class="col-6">
                        <input type="text" class="form-control" placeholder="CVC" maxlength="3">
                      </div>
                    </div>
                  </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100" :disabled="submitting">
                  <span v-if="submitting" class="spinner-border spinner-border-sm me-2"></span>
                  <i v-else class="bi bi-check-circle me-2"></i>
                  Confirm Booking — €{{ tutor.hourly_rate }}
                </button>
              </form>
            </div>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/api/axios'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'
import AlertMessage from '@/components/ui/AlertMessage.vue'

const route = useRoute()
const router = useRouter()

const tutor = ref(null)
const loading = ref(true)
const submitting = ref(false)
const error = ref(null)
const success = ref(null)

const form = ref({ date: '', time: '', comment: '' })

const tutorName = computed(() => tutor.value ? `${tutor.value.first_name} ${tutor.value.last_name}` : '')
const avatarUrl = computed(() =>
  tutor.value ? `https://ui-avatars.com/api/?name=${tutor.value.first_name}+${tutor.value.last_name}&background=4f46e5&color=fff&size=64` : ''
)

const minDate = computed(() => {
  const d = new Date()
  d.setDate(d.getDate() + 1)
  return d.toISOString().split('T')[0]
})

const timeSlots = computed(() => {
  if (!tutor.value) return []
  const slots = []
  const [startH, startM] = (tutor.value.availability_start || '09:00').split(':').map(Number)
  const [endH, endM] = (tutor.value.availability_end || '17:00').split(':').map(Number)
  let h = startH, m = startM

  while (h < endH || (h === endH && m < endM)) {
    slots.push(`${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`)
    m += 30
    if (m >= 60) { h++; m = 0 }
  }
  return slots
})

onMounted(async () => {
  try {
    const res = await api.get(`/tutors/${route.params.tutorProfileId}`)
    tutor.value = res.data
  } catch {
    error.value = 'Failed to load tutor information'
  } finally {
    loading.value = false
  }
})

async function handleBooking() {
  if (!form.value.date || !form.value.time) {
    error.value = 'Please select both a date and time'
    return
  }

  // Client-side day check
  const dayMap = { 0: 'Sun', 1: 'Mon', 2: 'Tue', 3: 'Wed', 4: 'Thu', 5: 'Fri', 6: 'Sat' }
  const selectedDay = dayMap[new Date(form.value.date).getDay()]
  const availableDays = (tutor.value.available_days || '').split(',').map(d => d.trim())

  if (!availableDays.includes(selectedDay)) {
    error.value = `This tutor is not available on ${selectedDay}. Available days: ${availableDays.join(', ')}`
    return
  }

  submitting.value = true
  error.value = null

  try {
    await api.post('/bookings', {
      tutor_profile_id: parseInt(route.params.tutorProfileId),
      scheduled_at: `${form.value.date} ${form.value.time}:00`,
      student_comment: form.value.comment
    })
    success.value = 'Booking confirmed! Redirecting to your bookings...'
    setTimeout(() => router.push('/student/bookings'), 2000)
  } catch (err) {
    error.value = err.response?.data?.details?.scheduled_at || err.response?.data?.error || 'Booking failed'
  } finally {
    submitting.value = false
  }
}
</script>
