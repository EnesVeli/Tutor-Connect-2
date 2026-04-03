<template>
  <span class="badge rounded-pill" :class="badgeClass">
    <i :class="iconClass" class="me-1"></i>{{ status }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: { type: String, required: true }
})

const badgeClass = computed(() => `badge-${props.status}`)

const iconClass = computed(() => {
  switch (props.status) {
    case 'pending': return 'bi bi-clock'
    case 'confirmed': return 'bi bi-check-circle'
    case 'completed': return 'bi bi-trophy'
    case 'cancelled': return 'bi bi-x-circle'
    default: return 'bi bi-question-circle'
  }
})
</script>
