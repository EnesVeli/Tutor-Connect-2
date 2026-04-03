<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-6">
        <div class="page-header">
          <h1><i class="bi bi-person-gear me-2"></i>Edit User</h1>
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
                  <input type="email" class="form-control" v-model="form.email" required>
                </div>

                <div class="mb-3">
                  <label class="form-label">Role</label>
                  <input type="text" class="form-control" :value="form.role" disabled>
                </div>

                <div class="mb-4">
                  <label class="form-label">Bio</label>
                  <textarea class="form-control" v-model="form.bio" rows="3"></textarea>
                </div>

                <div class="d-flex gap-2">
                  <button type="submit" class="btn btn-primary flex-grow-1" :disabled="saving">
                    <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
                    <i v-else class="bi bi-check2 me-2"></i>Save Changes
                  </button>
                  <RouterLink to="/admin/users" class="btn btn-outline-secondary">Cancel</RouterLink>
                </div>
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
import { useRoute, useRouter } from 'vue-router'
import api from '@/api/axios'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'
import AlertMessage from '@/components/ui/AlertMessage.vue'

const route = useRoute()
const router = useRouter()

const loading = ref(true)
const saving = ref(false)
const error = ref(null)
const success = ref(null)

const form = reactive({
  first_name: '',
  last_name: '',
  email: '',
  role: '',
  bio: ''
})

onMounted(async () => {
  try {
    const res = await api.get(`/admin/users/${route.params.id}`)
    Object.assign(form, res.data)
  } catch {
    error.value = 'Failed to load user'
  } finally {
    loading.value = false
  }
})

async function handleSave() {
  saving.value = true
  error.value = null
  try {
    await api.put(`/admin/users/${route.params.id}`, {
      first_name: form.first_name,
      last_name: form.last_name,
      email: form.email,
      bio: form.bio
    })
    success.value = 'User updated successfully!'
    setTimeout(() => router.push('/admin/users'), 1500)
  } catch (err) {
    error.value = err.response?.data?.error || 'Failed to update user'
  } finally {
    saving.value = false
  }
}
</script>
