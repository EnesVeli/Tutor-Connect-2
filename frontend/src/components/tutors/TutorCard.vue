<template>
  <div class="card card-hover-lift h-100">
    <div class="card-body d-flex flex-column">
      <div class="d-flex align-items-center mb-3">
        <img :src="avatarUrl" :alt="fullName" class="tutor-avatar me-3">
        <div>
          <h5 class="card-title mb-0">{{ fullName }}</h5>
          <span class="badge bg-primary-subtle text-primary">{{ tutor.subject }}</span>
        </div>
      </div>

      <p class="text-muted small flex-grow-1">{{ truncatedBio }}</p>

      <div class="d-flex justify-content-between align-items-center mb-2">
        <div v-if="hasRating" class="star-rating">
          <template v-for="n in 5" :key="n">
            <i :class="starClass(n)"></i>
          </template>
          <small class="text-muted ms-1">({{ rating }})</small>
        </div>
        <small v-else class="text-muted fst-italic">No reviews yet</small>
        <span class="fw-bold text-primary" style="font-size: 1.2rem;">€{{ tutor.hourly_rate }}/hr</span>
      </div>

      <div class="d-flex justify-content-between align-items-center small text-muted mb-3">
        <span><i class="bi bi-briefcase me-1"></i>{{ tutor.experience_years }} yrs exp</span>
        <span><i class="bi bi-calendar3 me-1"></i>{{ tutor.available_days }}</span>
      </div>

      <RouterLink :to="`/tutors/${tutor.id}`" class="btn btn-primary w-100">
        <i class="bi bi-eye me-1"></i>View Profile
      </RouterLink>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  tutor: { type: Object, required: true }
})

const fullName = computed(() => `${props.tutor.first_name} ${props.tutor.last_name}`)

const avatarUrl = computed(() =>
  `https://ui-avatars.com/api/?name=${props.tutor.first_name}+${props.tutor.last_name}&background=4f46e5&color=fff&size=64`
)

const truncatedBio = computed(() => {
  const bio = props.tutor.bio || 'No bio available'
  return bio.length > 80 ? bio.substring(0, 80) + '...' : bio
})

const rating = computed(() => parseFloat(props.tutor.avg_rating) || 0)
const hasRating = computed(() => rating.value > 0)

function starClass(n) {
  const rating = parseFloat(props.tutor.avg_rating) || 0
  if (n <= Math.floor(rating)) return 'bi bi-star-fill'
  if (n - 0.5 <= rating) return 'bi bi-star-half'
  return 'bi bi-star'
}
</script>
