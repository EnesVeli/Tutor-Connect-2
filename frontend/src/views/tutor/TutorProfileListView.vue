<template>
  <div class="container mt-5">
    <div class="page-header d-flex justify-content-between align-items-center">
      <div>
        <h1><i class="bi bi-person-badge me-2"></i>My Profiles</h1>
        <p>Manage the subjects you teach.</p>
      </div>
      <RouterLink to="/tutor/profiles/new" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Add Profile
      </RouterLink>
    </div>

    <div v-if="error" class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ error }}
      <button type="button" class="btn-close" @click="error = null" aria-label="Close"></button>
    </div>

    <LoadingSpinner v-if="loading" />

    <template v-else>
      <div v-if="profiles.length > 0" class="row g-4">
        <div v-for="profile in profiles" :key="profile.id" class="col-12 col-md-6">
          <div class="card card-hover-lift h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <h5 class="fw-bold mb-0">
                  <span class="badge bg-primary-subtle text-primary me-2">{{ profile.subject }}</span>
                </h5>
                <span class="fw-bold text-primary">EUR {{ profile.hourly_rate }}/hr</span>
              </div>
              <p class="text-muted small mb-2">{{ profile.bio || 'No bio' }}</p>
              <p class="small mb-0">
                <i class="bi bi-briefcase me-1"></i>{{ profile.experience_years }} yrs |
                <i class="bi bi-clock me-1"></i>{{ profile.availability_start }}-{{ profile.availability_end }} |
                <i class="bi bi-calendar3 me-1"></i>{{ profile.available_days }}
              </p>
            </div>
            <div class="card-footer bg-transparent border-0 p-3">
              <RouterLink :to="`/tutor/profiles/${profile.id}/edit`" class="btn btn-outline-primary btn-sm me-2">
                <i class="bi bi-pencil me-1"></i>Edit
              </RouterLink>
              <button class="btn btn-outline-danger btn-sm" @click="confirmDelete(profile)" :disabled="isSubmitting === profile.id">
                <span v-if="isSubmitting === profile.id" class="spinner-border spinner-border-sm me-1" role="status"></span>
                {{ isSubmitting === profile.id ? 'Please wait...' : 'Delete' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="empty-state">
        <i class="bi bi-person-badge"></i>
        <h5>No Profiles Yet</h5>
        <p>Create your first tutoring profile to start receiving bookings!</p>
        <RouterLink to="/tutor/profiles/new" class="btn btn-primary mt-2">Create Profile</RouterLink>
      </div>
    </template>

    <ConfirmModal :visible="showConfirm" message="Are you sure you want to delete this profile? All associated bookings will also be removed."
      @confirm="handleDelete" @cancel="showConfirm = false" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api/axios'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'
import ConfirmModal from '@/components/ui/ConfirmModal.vue'

const profiles = ref([])
const loading = ref(true)
const showConfirm = ref(false)
const profileToDelete = ref(null)
const error = ref(null)
const isSubmitting = ref(null)

onMounted(async () => {
  error.value = null
  try {
    const res = await api.get('/tutor/profiles')
    profiles.value = res.data
  } catch (err) {
    error.value = err.response?.data?.error || 'Something went wrong. Please try again.'
  } finally {
    loading.value = false
  }
})

function confirmDelete(profile) {
  profileToDelete.value = profile
  showConfirm.value = true
}

async function handleDelete() {
  showConfirm.value = false
  if (!profileToDelete.value) return

  error.value = null
  isSubmitting.value = profileToDelete.value.id
  try {
    await api.delete(`/tutor/profiles/${profileToDelete.value.id}`)
    profiles.value = profiles.value.filter(p => p.id !== profileToDelete.value.id)
  } catch (err) {
    error.value = err.response?.data?.error || 'Something went wrong. Please try again.'
  } finally {
    isSubmitting.value = null
  }
}
</script>
