<template>
  <div class="container mt-5">
    <LoadingSpinner v-if="loading" />

    <template v-else-if="tutor">
      <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
          <div class="card">
            <div class="card-body p-4">
              <!-- Header -->
              <div class="d-flex align-items-center mb-4">
                <img :src="avatarUrl" class="tutor-avatar me-3" style="width: 80px; height: 80px;" :alt="fullName">
                <div>
                  <h2 class="fw-bold mb-1">{{ fullName }}</h2>
                  <span class="badge bg-primary-subtle text-primary fs-6">{{ tutor.subject }}</span>
                </div>
              </div>

              <!-- Stats Row -->
              <div class="row g-3 mb-4">
                <div class="col-4">
                  <div class="text-center p-3 rounded-3" style="background: var(--tc-gray-light);">
                    <div class="fw-bold text-primary fs-4">€{{ tutor.hourly_rate }}</div>
                    <small class="text-muted">Per Hour</small>
                  </div>
                </div>
                <div class="col-4">
                  <div class="text-center p-3 rounded-3" style="background: var(--tc-gray-light);">
                    <div class="fw-bold text-primary fs-4">{{ tutor.experience_years }}</div>
                    <small class="text-muted">Years Exp.</small>
                  </div>
                </div>
                <div class="col-4">
                  <div class="text-center p-3 rounded-3" style="background: var(--tc-gray-light);">
                    <div class="fw-bold text-primary fs-4 star-rating">
                      <template v-if="avgRating">
                        <template v-for="n in 5" :key="n">
                          <i :class="detailStarClass(n)"></i>
                        </template>
                        <span class="ms-1">{{ avgRating }}</span>
                      </template>
                      <template v-else>
                        <i class="bi bi-star"></i> N/A
                      </template>
                    </div>
                    <small class="text-muted">Rating</small>
                  </div>
                </div>
              </div>

              <!-- Bio -->
              <h5 class="fw-bold"><i class="bi bi-person-lines-fill me-2"></i>About</h5>
              <p class="text-muted mb-4">{{ tutor.bio || 'No bio available' }}</p>

              <!-- Availability -->
              <h5 class="fw-bold"><i class="bi bi-calendar3 me-2"></i>Availability</h5>
              <div class="mb-4">
                <p class="mb-1"><strong>Days:</strong>
                  <span v-for="day in availableDays" :key="day" class="badge bg-light text-dark me-1">{{ day }}</span>
                </p>
                <p class="mb-0"><strong>Hours:</strong> {{ tutor.availability_start }} – {{ tutor.availability_end }}</p>
              </div>

              <!-- Book Button -->
              <RouterLink v-if="authStore.isAuthenticated && authStore.userRole === 'student'"
                :to="`/book/${tutor.id}`" class="btn btn-primary btn-lg w-100">
                <i class="bi bi-calendar-plus me-2"></i>Book This Tutor
              </RouterLink>
              <RouterLink v-else-if="!authStore.isAuthenticated" to="/login" class="btn btn-primary btn-lg w-100">
                <i class="bi bi-box-arrow-in-right me-2"></i>Login to Book
              </RouterLink>
            </div>
          </div>

          <!-- Reviews Section -->
          <div class="mt-4">
            <h4 class="fw-bold mb-3"><i class="bi bi-chat-left-quote me-2"></i>Reviews ({{ reviews.length }})</h4>
            <template v-if="reviews.length > 0">
              <ReviewCard v-for="review in reviews" :key="review.id" :review="review" />
            </template>
            <div v-else class="empty-state">
              <i class="bi bi-chat-left"></i>
              <p>No reviews yet for this tutor.</p>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div v-else class="empty-state">
      <i class="bi bi-person-x"></i>
      <h5>Tutor Not Found</h5>
      <RouterLink to="/tutors" class="btn btn-primary mt-3">Back to Tutor List</RouterLink>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/api/axios'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'
import ReviewCard from '@/components/reviews/ReviewCard.vue'

const route = useRoute()
const authStore = useAuthStore()

const tutor = ref(null)
const reviews = ref([])
const loading = ref(true)
const avgRating = ref(null)

const fullName = computed(() => tutor.value ? `${tutor.value.first_name} ${tutor.value.last_name}` : '')
const avatarUrl = computed(() =>
  tutor.value ? `https://ui-avatars.com/api/?name=${tutor.value.first_name}+${tutor.value.last_name}&background=4f46e5&color=fff&size=80` : ''
)
const availableDays = computed(() => (tutor.value?.available_days || '').split(',').map(d => d.trim()).filter(Boolean))

onMounted(async () => {
  const id = route.params.id
  try {
    const [tutorRes, reviewsRes] = await Promise.all([
      api.get(`/tutors/${id}`),
      api.get(`/tutors/${id}/reviews`)
    ])
    tutor.value = tutorRes.data
    reviews.value = reviewsRes.data

    if (reviews.value.length > 0) {
      const sum = reviews.value.reduce((acc, r) => acc + r.rating, 0)
      avgRating.value = (sum / reviews.value.length).toFixed(1)
    }
  } catch {
    tutor.value = null
  } finally {
    loading.value = false
  }
})

function detailStarClass(n) {
  const r = parseFloat(avgRating.value) || 0
  if (n <= Math.floor(r)) return 'bi bi-star-fill'
  if (n - 0.5 <= r) return 'bi bi-star-half'
  return 'bi bi-star'
}
</script>
