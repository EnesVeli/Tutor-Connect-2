<template>
  <div v-if="message" class="alert alert-dismissible fade show" :class="`alert-${type}`" role="alert">
    <i :class="iconClass" class="me-2"></i>
    {{ message }}
    <button type="button" class="btn-close" @click="$emit('close')" aria-label="Close"></button>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  type: { type: String, default: 'danger' },
  message: { type: String, default: '' }
})

defineEmits(['close'])

const iconClass = computed(() => {
  switch (props.type) {
    case 'success': return 'bi bi-check-circle-fill'
    case 'warning': return 'bi bi-exclamation-triangle-fill'
    case 'danger': return 'bi bi-x-circle-fill'
    default: return 'bi bi-info-circle-fill'
  }
})
</script>
