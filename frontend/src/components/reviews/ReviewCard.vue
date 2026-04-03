<template>
  <div class="card mb-3">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="star-rating mb-1">
            <template v-for="n in 5" :key="n">
              <i :class="n <= review.rating ? 'bi bi-star-fill' : 'bi bi-star'"></i>
            </template>
          </div>
          <p class="mb-1">{{ review.comment || 'No comment' }}</p>
          <small class="text-muted">
            By {{ review.first_name }} {{ review.last_name }} — {{ formatDate(review.created_at) }}
          </small>
        </div>
        <slot name="actions"></slot>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  review: { type: Object, required: true }
})

function formatDate(dateStr) {
  return new Date(dateStr).toLocaleDateString('en-GB', {
    year: 'numeric', month: 'short', day: 'numeric'
  })
}
</script>
