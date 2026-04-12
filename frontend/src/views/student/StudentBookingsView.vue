<template>
  <div class="container mt-5">
    <div class="page-header">
      <h1><i class="bi bi-calendar-check me-2"></i>My Bookings</h1>
      <p>View your upcoming and past bookings.</p>
    </div>

    <LoadingSpinner v-if="loading" />

    <template v-else>
      <AlertMessage v-if="error" type="danger" :message="error" @close="error = null" />
      <AlertMessage v-if="success" type="success" :message="success" @close="success = null" />

      <template v-if="bookings.length > 0">
        <div v-for="booking in bookings" :key="booking.id">
          <BookingCard :booking="booking" user-role="student" />

          <!-- Review section for completed bookings -->
          <div v-if="booking.status === 'completed'" class="card mb-3 border-primary">
            <div class="card-body">

              <!-- EXISTING REVIEW: display mode -->
              <template v-if="booking.review_id && !editingReview[booking.id]">
                <h6 class="fw-bold mb-2">
                  <i class="bi bi-chat-left-quote me-2"></i>Your Review for {{ booking.subject }}
                </h6>
                <div class="star-rating mb-1">
                  <i v-for="n in 5" :key="n"
                    :class="n <= booking.review_rating ? 'bi bi-star-fill' : 'bi bi-star'"
                    style="font-size: 1.2rem;"></i>
                  <small class="text-muted ms-1">({{ booking.review_rating }}/5)</small>
                </div>
                <p class="mb-2 fst-italic text-muted">"{{ booking.review_comment || 'No comment' }}"</p>
                <div class="d-flex gap-2">
                  <button class="btn btn-sm btn-outline-primary" @click="startEdit(booking)">
                    <i class="bi bi-pencil me-1"></i>Edit
                  </button>
                  <button class="btn btn-sm btn-outline-danger" @click="deleteReview(booking)" :disabled="isSubmitting === booking.id">
                    <span v-if="isSubmitting === booking.id" class="spinner-border spinner-border-sm me-1" role="status"></span>
                    {{ isSubmitting === booking.id ? 'Please wait...' : 'Delete' }}
                  </button>
                </div>
              </template>

              <!-- EXISTING REVIEW: edit mode -->
              <template v-else-if="booking.review_id && editingReview[booking.id]">
                <h6 class="fw-bold mb-2">
                  <i class="bi bi-pencil-square me-2"></i>Edit Review for {{ booking.subject }}
                </h6>
                <div class="star-rating mb-2">
                  <i v-for="n in 5" :key="n"
                    :class="n <= reviewForms[booking.id]?.rating ? 'bi bi-star-fill' : 'bi bi-star'"
                    style="cursor: pointer; font-size: 1.5rem;"
                    @click="setRating(booking.id, n)"></i>
                </div>
                <textarea class="form-control mb-2" v-model="reviewForms[booking.id].comment"
                  rows="2" placeholder="Update your review..."></textarea>
                <div class="d-flex gap-2">
                  <button class="btn btn-sm btn-primary" @click="saveEdit(booking)"
                    :disabled="reviewForms[booking.id]?.submitting">
                    <span v-if="reviewForms[booking.id]?.submitting" class="spinner-border spinner-border-sm me-1"></span>
                    <i v-else class="bi bi-check2 me-1"></i>Save
                  </button>
                  <button class="btn btn-sm btn-outline-secondary" @click="cancelEdit(booking.id)">
                    Cancel
                  </button>
                </div>
              </template>

              <!-- NO REVIEW YET: create form -->
              <template v-else-if="!booking.review_id">
                <h6 class="fw-bold">
                  <i class="bi bi-chat-left-quote me-2"></i>Leave a Review for {{ booking.subject }}
                </h6>
                <div class="star-rating mb-2">
                  <i v-for="n in 5" :key="n"
                    :class="n <= reviewForms[booking.id]?.rating ? 'bi bi-star-fill' : 'bi bi-star'"
                    style="cursor: pointer; font-size: 1.5rem;"
                    @click="setRating(booking.id, n)"></i>
                </div>
                <textarea class="form-control mb-2" v-model="reviewForms[booking.id].comment"
                  rows="2" placeholder="Share your experience..."></textarea>
                <button class="btn btn-sm btn-primary" @click="submitReview(booking)"
                  :disabled="reviewForms[booking.id]?.submitting">
                  <span v-if="reviewForms[booking.id]?.submitting" class="spinner-border spinner-border-sm me-1"></span>
                  <i v-else class="bi bi-send me-1"></i>Submit Review
                </button>
              </template>

            </div>
          </div>
        </div>
      </template>

      <div v-else class="empty-state">
        <i class="bi bi-calendar-x"></i>
        <h5>No Bookings Yet</h5>
        <p>Start by finding a tutor and booking a lesson!</p>
        <RouterLink to="/tutors" class="btn btn-primary mt-2">Find a Tutor</RouterLink>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import api from '@/api/axios'
import BookingCard from '@/components/bookings/BookingCard.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'
import AlertMessage from '@/components/ui/AlertMessage.vue'

const bookings = ref([])
const loading = ref(true)
const error = ref(null)
const success = ref(null)
const reviewForms = reactive({})
const editingReview = reactive({})
const isSubmitting = ref(null)

onMounted(async () => {
  try {
    const res = await api.get('/bookings')
    bookings.value = res.data.data

    // Initialize review forms for all completed bookings
    bookings.value.filter(b => b.status === 'completed').forEach(b => {
      reviewForms[b.id] = {
        rating: b.review_rating || 0,
        comment: b.review_comment || '',
        submitting: false
      }
    })
  } catch {
    error.value = 'Failed to load bookings'
  } finally {
    loading.value = false
  }
})

function setRating(bookingId, rating) {
  if (reviewForms[bookingId]) {
    reviewForms[bookingId].rating = rating
  }
}

// ── Create new review ──────────────────────────────────────────────
async function submitReview(booking) {
  const form = reviewForms[booking.id]
  if (!form || form.rating < 1) {
    error.value = 'Please select a rating (1-5 stars)'
    return
  }

  form.submitting = true
  error.value = null

  try {
    const res = await api.post(`/tutors/${booking.profile_id}/reviews`, {
      booking_id: booking.id,
      rating: form.rating,
      comment: form.comment
    })
    // Update local state so display switches instantly
    booking.review_id = res.data.id
    booking.review_rating = res.data.rating
    booking.review_comment = res.data.comment
    booking.review_created_at = res.data.created_at
    success.value = 'Review submitted successfully!'
  } catch (err) {
    error.value = err.response?.data?.error || 'Failed to submit review'
  } finally {
    form.submitting = false
  }
}

// ── Edit existing review ───────────────────────────────────────────
function startEdit(booking) {
  reviewForms[booking.id] = {
    rating: booking.review_rating,
    comment: booking.review_comment || '',
    submitting: false
  }
  editingReview[booking.id] = true
}

function cancelEdit(bookingId) {
  editingReview[bookingId] = false
}

async function saveEdit(booking) {
  const form = reviewForms[booking.id]
  if (!form || form.rating < 1) {
    error.value = 'Please select a rating (1-5 stars)'
    return
  }

  form.submitting = true
  error.value = null

  try {
    const res = await api.put(`/reviews/${booking.review_id}`, {
      rating: form.rating,
      comment: form.comment
    })
    booking.review_rating = res.data.rating
    booking.review_comment = res.data.comment
    editingReview[booking.id] = false
    success.value = 'Review updated successfully!'
  } catch (err) {
    error.value = err.response?.data?.error || 'Failed to update review'
  } finally {
    form.submitting = false
  }
}

// ── Delete review ──────────────────────────────────────────────────
async function deleteReview(booking) {
  error.value = null
  isSubmitting.value = booking.id
  try {
    await api.delete(`/reviews/${booking.review_id}`)
    booking.review_id = null
    booking.review_rating = null
    booking.review_comment = null
    booking.review_created_at = null
    // Re-initialize the create form
    reviewForms[booking.id] = { rating: 0, comment: '', submitting: false }
    success.value = 'Review deleted.'
  } catch (err) {
    error.value = err.response?.data?.error || 'Failed to delete review'
  } finally {
    isSubmitting.value = null
  }
}
</script>
