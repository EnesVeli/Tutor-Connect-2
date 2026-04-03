<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-6">
        <div class="page-header">
          <h1><i class="bi bi-person-circle me-2"></i>My Profile</h1>
        </div>

        <LoadingSpinner v-if="loading" />

        <template v-else>
          <AlertMessage v-if="error" type="danger" :message="error" @close="error = null" />
          <AlertMessage v-if="success" type="success" :message="success" @close="success = null" />

          <div class="card">
            <div class="card-body p-4">
              <form @submit.prevent="handleSave">
                <div class="row g-3">
                  <div class="col-6">
                    <label class="form-label">First Name</label>
                    <input type="text" class="form-control" v-model="form.first_name" required>
                  </div>
                  <div class="col-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" class="form-control" v-model="form.last_name" required>
                  </div>
                </div>

                <div class="mb-3 mt-3">
                  <label class="form-label">Email</label>
                  <input type="email" class="form-control" :value="form.email" disabled>
                  <div class="form-text">Email cannot be changed</div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Date of Birth</label>
                  <input type="date" class="form-control" v-model="form.date_of_birth">
                </div>

                <div class="mb-4">
                  <label class="form-label">Bio</label>
                  <textarea class="form-control" v-model="form.bio" rows="3" placeholder="Tell us about yourself..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100" :disabled="saving">
                  <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
                  <i v-else class="bi bi-check2 me-2"></i>Save Profile
                </button>
              </form>
            </div>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import api from '@/api/axios'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'
import AlertMessage from '@/components/ui/AlertMessage.vue'

const loading = ref(true)
const saving = ref(false)
const error = ref(null)
const success = ref(null)

const form = reactive({
  first_name: '',
  last_name: '',
  email: '',
  date_of_birth: '',
  bio: ''
})

onMounted(async () => {
  try {
    const res = await api.get('/student/profile')
    Object.assign(form, res.data)
  } catch {
    error.value = 'Failed to load profile'
  } finally {
    loading.value = false
  }
})

async function handleSave() {
  saving.value = true
  error.value = null
  try {
    await api.put('/student/profile', {
      first_name: form.first_name,
      last_name: form.last_name,
      date_of_birth: form.date_of_birth,
      bio: form.bio
    })
    success.value = 'Profile updated successfully!'
  } catch (err) {
    error.value = err.response?.data?.error || 'Failed to save profile'
  } finally {
    saving.value = false
  }
}
</script>
