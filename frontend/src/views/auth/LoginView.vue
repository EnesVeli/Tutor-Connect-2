<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-5">
        <div class="card">
          <div class="card-body p-4">
            <div class="text-center mb-4">
              <i class="bi bi-mortarboard-fill text-primary" style="font-size: 3rem;"></i>
              <h2 class="mt-2 fw-bold">Welcome Back</h2>
              <p class="text-muted">Sign in to your Tutor Connect account</p>
            </div>

            <AlertMessage v-if="authStore.error" type="danger" :message="authStore.error" @close="authStore.error = null" />

            <form @submit.prevent="handleLogin">
              <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                  <input type="email" class="form-control" id="email" v-model="email" placeholder="your@email.com" required>
                </div>
              </div>

              <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-lock"></i></span>
                  <input type="password" class="form-control" id="password" v-model="password" placeholder="Enter your password" required>
                </div>
              </div>

              <button type="submit" class="btn btn-primary w-100 mb-3" :disabled="authStore.isLoading">
                <span v-if="authStore.isLoading" class="spinner-border spinner-border-sm me-2"></span>
                <i v-else class="bi bi-box-arrow-in-right me-2"></i>
                Sign In
              </button>

              <p class="text-center text-muted mb-0">
                Don't have an account?
                <RouterLink to="/register" class="text-primary fw-semibold">Register here</RouterLink>
              </p>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AlertMessage from '@/components/ui/AlertMessage.vue'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')

async function handleLogin() {
  try {
    await authStore.login(email.value, password.value)
    router.push('/')
  } catch {
    // Error already set in auth store
  }
}
</script>
