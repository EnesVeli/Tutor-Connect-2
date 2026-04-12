<template>
  <div class="container mt-5">
    <div class="page-header">
      <h1><i class="bi bi-chat-left-quote me-2"></i>Review Moderation</h1>
      <p>View and moderate all student reviews on the platform.</p>
    </div>

    <LoadingSpinner v-if="loading" />

    <template v-else>
      <AlertMessage v-if="error" type="danger" :message="error" @close="error = null" />
      <AlertMessage v-if="success" type="success" :message="success" @close="success = null" />

      <div v-if="reviews.length > 0" class="card">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>Student</th>
                  <th>Tutor</th>
                  <th>Subject</th>
                  <th>Rating</th>
                  <th>Comment</th>
                  <th>Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="review in reviews" :key="review.id">
                  <td>{{ review.student_first_name }} {{ review.student_last_name }}</td>
                  <td>{{ review.tutor_first_name }} {{ review.tutor_last_name }}</td>
                  <td><span class="badge bg-primary-subtle text-primary">{{ review.subject }}</span></td>
                  <td>
                    <div class="star-rating">
                      <i v-for="n in 5" :key="n"
                        :class="n <= review.rating ? 'bi bi-star-fill' : 'bi bi-star'"></i>
                    </div>
                  </td>
                  <td class="text-truncate" style="max-width: 200px;">{{ review.comment || '-' }}</td>
                  <td class="text-nowrap">{{ formatDate(review.created_at) }}</td>
                  <td>
                    <button class="btn btn-sm btn-outline-danger" @click="deleteReview(review)" :disabled="isSubmitting === review.id">
                      <span v-if="isSubmitting === review.id" class="spinner-border spinner-border-sm me-1" role="status"></span>
                      {{ isSubmitting === review.id ? 'Please wait...' : 'Delete' }}
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div v-else class="empty-state">
        <i class="bi bi-chat-left"></i>
        <h5>No Reviews Yet</h5>
        <p>No reviews have been submitted on the platform.</p>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api/axios'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'
import AlertMessage from '@/components/ui/AlertMessage.vue'

const reviews = ref([])
const loading = ref(true)
const error = ref(null)
const success = ref(null)
const isSubmitting = ref(null)

onMounted(async () => {
  try {
    const res = await api.get('/admin/reviews')
    reviews.value = res.data
  } catch {
    error.value = 'Failed to load reviews'
  } finally {
    loading.value = false
  }
})

async function deleteReview(review) {
  error.value = null
  isSubmitting.value = review.id
  try {
    await api.delete(`/reviews/${review.id}`)
    reviews.value = reviews.value.filter(r => r.id !== review.id)
    success.value = 'Review deleted successfully'
  } catch (err) {
    error.value = err.response?.data?.error || 'Failed to delete review'
  } finally {
    isSubmitting.value = null
  }
}

function formatDate(dateStr) {
  return new Date(dateStr).toLocaleDateString('en-GB', {
    year: 'numeric', month: 'short', day: 'numeric'
  })
}
</script>
