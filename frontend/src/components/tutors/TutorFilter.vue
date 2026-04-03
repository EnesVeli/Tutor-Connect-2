<template>
  <div class="card mb-4">
    <div class="card-body">
      <div class="row g-3 align-items-end">
        <div class="col-12 col-md-4">
          <label class="form-label">Subject</label>
          <select class="form-select" v-model="localFilters.subject" @change="emitChange">
            <option value="">All Subjects</option>
            <option v-for="s in subjects" :key="s" :value="s">{{ s }}</option>
          </select>
        </div>
        <div class="col-6 col-md-3">
          <label class="form-label">Min Price (€)</label>
          <input type="number" class="form-control" v-model.number="localFilters.minPrice" @change="emitChange" min="0" placeholder="0">
        </div>
        <div class="col-6 col-md-3">
          <label class="form-label">Max Price (€)</label>
          <input type="number" class="form-control" v-model.number="localFilters.maxPrice" @change="emitChange" min="0" placeholder="Any">
        </div>
        <div class="col-12 col-md-2">
          <button class="btn btn-outline-secondary w-100" @click="clearFilters">
            <i class="bi bi-x-circle me-1"></i>Clear
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  currentFilters: { type: Object, default: () => ({ subject: '', minPrice: null, maxPrice: null }) }
})

const emit = defineEmits(['filter-change'])

const subjects = [
  'Mathematics', 'English', 'Science', 'Physics', 'Chemistry', 'Biology',
  'History', 'Geography', 'Computer Science', 'Music', 'Art',
  'Physical Education', 'Economics', 'French', 'Spanish', 'German'
]
const localFilters = ref({ ...props.currentFilters })

function emitChange() {
  emit('filter-change', { ...localFilters.value })
}

function clearFilters() {
  localFilters.value = { subject: '', minPrice: null, maxPrice: null }
  emitChange()
}
</script>
