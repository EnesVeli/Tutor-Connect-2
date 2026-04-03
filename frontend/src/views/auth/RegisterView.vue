<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-6">
        <div class="card">
          <div class="card-body p-4">
            <div class="text-center mb-4">
              <i class="bi bi-person-plus-fill text-primary" style="font-size: 3rem;"></i>
              <h2 class="mt-2 fw-bold">Create Account</h2>
              <p class="text-muted">Join Tutor Connect today</p>
            </div>

            <AlertMessage v-if="error" type="danger" :message="error" @close="error = null" />
            <AlertMessage v-if="success" type="success" :message="success" @close="success = null" />

            <form @submit.prevent="handleRegister">
              <div class="row g-3">
                <div class="col-6">
                  <label for="first_name" class="form-label">First Name</label>
                  <input type="text" class="form-control" id="first_name" v-model="form.first_name" required>
                </div>
                <div class="col-6">
                  <label for="last_name" class="form-label">Last Name</label>
                  <input type="text" class="form-control" id="last_name" v-model="form.last_name" required>
                </div>
              </div>

              <div class="mb-3 mt-3">
                <label for="reg_email" class="form-label">Email Address</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                  <input type="email" class="form-control" id="reg_email" v-model="form.email" required>
                </div>
              </div>

              <div class="mb-3">
                <label for="reg_password" class="form-label">Password</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-lock"></i></span>
                  <input type="password" class="form-control" id="reg_password" v-model="form.password" minlength="8" required>
                </div>
                <div class="form-text">Minimum 8 characters</div>
              </div>

              <div class="mb-4">
                <label for="role" class="form-label">I am a...</label>
                <select class="form-select" id="role" v-model="form.role" required>
                  <option value="student">Student — I want to find tutors</option>
                  <option value="tutor">Tutor — I want to teach</option>
                </select>
              </div>

              <button type="submit" class="btn btn-primary w-100 mb-3" :disabled="isLoading">
                <span v-if="isLoading" class="spinner-border spinner-border-sm me-2"></span>
                <i v-else class="bi bi-person-plus me-2"></i>
                Create Account
              </button>

              <p class="text-center text-muted mb-0">
                Already have an account?
                <RouterLink to="/login" class="text-primary fw-semibold">Sign in</RouterLink>
              </p>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useAuthStore } from '@/stores/auth'
import AlertMessage from '@/components/ui/AlertMessage.vue'

const authStore = useAuthStore()
const isLoading = ref(false)
const error = ref(null)
const success = ref(null)

const form = reactive({
  first_name: '',
  last_name: '',
  email: '',
  password: '',
  role: 'student'
})

async function handleRegister() {
  isLoading.value = true
  error.value = null
  try {
    await authStore.register(form)
    success.value = 'Account created successfully! Please sign in.'
    form.first_name = ''
    form.last_name = ''
    form.email = ''
    form.password = ''
    form.role = 'student'
  } catch (err) {
    error.value = err.response?.data?.error || 'Registration failed'
  } finally {
    isLoading.value = false
  }
}
</script>
