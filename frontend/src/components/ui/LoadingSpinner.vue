<template>
  <div v-if="visible" class="loading-overlay">
    <div class="text-center">
      <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-3 text-muted">Loading...</p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  visible: { type: Boolean, default: true }
})
</script>
