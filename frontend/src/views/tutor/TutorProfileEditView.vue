<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-8 col-lg-6">
        <div class="page-header">
          <h1><i class="bi bi-pencil-square me-2"></i>{{ isEditing ? 'Edit Profile' : 'Create Profile' }}</h1>
        </div>

        <LoadingSpinner v-if="loading" />

        <template v-else>
          <AlertMessage v-if="error" type="danger" :message="error" @close="error = null" />

          <div class="card">
            <div class="card-body p-4">
              <form @submit.prevent="handleSave">
                <div class="mb-3">
                  <label class="form-label">Subject</label>
                  <select class="form-select" v-model="form.subject" required>
                    <option value="" disabled>Select a subject</option>
                    <option v-for="s in subjectOptions" :key="s" :value="s">{{ s }}</option>
                  </select>
                </div>

                <div class="row g-3 mb-3">
                  <div class="col-6">
                    <label class="form-label">Hourly Rate (€)</label>
                    <input type="number" class="form-control" v-model.number="form.hourly_rate" required min="1" step="0.50">
                  </div>
                  <div class="col-6">
                    <label class="form-label">Experience (years)</label>
                    <input type="number" class="form-control" v-model.number="form.experience_years" required min="0">
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Bio / Description</label>
                  <textarea class="form-control" v-model="form.bio" rows="3" placeholder="Describe your teaching style and qualifications..."></textarea>
                </div>

                <div class="mb-3">
                  <label class="form-label">Available Days</label>
                  <div class="d-flex flex-wrap gap-2">
                    <div v-for="day in allDays" :key="day" class="form-check">
                      <input type="checkbox" class="form-check-input" :id="day" :value="day" v-model="selectedDays">
                      <label class="form-check-label" :for="day">{{ day }}</label>
                    </div>
                  </div>
                </div>

                <div class="row g-3 mb-4">
                  <div class="col-6">
                    <label class="form-label">Start Time</label>
                    <input type="time" class="form-control" v-model="form.availability_start" required>
                  </div>
                  <div class="col-6">
                    <label class="form-label">End Time</label>
                    <input type="time" class="form-control" v-model="form.availability_end" required>
                  </div>
                </div>

                <button type="submit" class="btn btn-primary w-100" :disabled="saving">
                  <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
                  <i v-else class="bi bi-check2 me-2"></i>
                  {{ isEditing ? 'Update Profile' : 'Create Profile' }}
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
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/api/axios'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'
import AlertMessage from '@/components/ui/AlertMessage.vue'

const route = useRoute()
const router = useRouter()

const loading = ref(false)
const saving = ref(false)
const error = ref(null)

const allDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
const subjectOptions = [
  'Mathematics', 'English', 'Science', 'Physics', 'Chemistry', 'Biology',
  'History', 'Geography', 'Computer Science', 'Music', 'Art',
  'Physical Education', 'Economics', 'French', 'Spanish', 'German'
]
const selectedDays = ref([])

const isEditing = computed(() => !!route.params.id)

const form = reactive({
  subject: '',
  hourly_rate: 20,
  experience_years: 0,
  bio: '',
  availability_start: '09:00',
  availability_end: '17:00'
})

onMounted(async () => {
  if (isEditing.value) {
    loading.value = true
    try {
      const res = await api.get('/tutor/profiles')
      const profile = res.data.find(p => p.id == route.params.id)
      if (profile) {
        Object.assign(form, profile)
        selectedDays.value = (profile.available_days || '').split(',').map(d => d.trim()).filter(Boolean)
      }
    } catch {
      error.value = 'Failed to load profile'
    } finally {
      loading.value = false
    }
  }
})

async function handleSave() {
  if (selectedDays.value.length === 0) {
    error.value = 'Please select at least one available day'
    return
  }

  saving.value = true
  error.value = null

  const payload = {
    ...form,
    available_days: selectedDays.value.join(',')
  }

  try {
    if (isEditing.value) {
      await api.put(`/tutor/profiles/${route.params.id}`, payload)
    } else {
      await api.post('/tutor/profiles', payload)
    }
    router.push('/tutor/profiles')
  } catch (err) {
    error.value = err.response?.data?.error || 'Failed to save profile'
  } finally {
    saving.value = false
  }
}
</script>
