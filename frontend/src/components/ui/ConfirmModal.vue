<template>
  <Teleport to="body">
    <div v-if="visible" class="modal-backdrop fade show"></div>
    <div v-if="visible" class="modal fade show d-block" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header border-0">
            <h5 class="modal-title">
              <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Confirm Action
            </h5>
            <button type="button" class="btn-close" @click="$emit('cancel')"></button>
          </div>
          <div class="modal-body">
            <p class="mb-0">{{ message }}</p>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary" @click="$emit('cancel')">Cancel</button>
            <button type="button" class="btn btn-danger" @click="$emit('confirm')">
              <i class="bi bi-trash me-1"></i>Confirm
            </button>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
defineProps({
  message: { type: String, default: 'Are you sure?' },
  visible: { type: Boolean, default: false }
})

defineEmits(['confirm', 'cancel'])
</script>
