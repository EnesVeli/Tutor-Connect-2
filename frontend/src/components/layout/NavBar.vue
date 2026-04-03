<template>
  <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #1e1b4b, #312e81);">
    <div class="container">
      <RouterLink class="navbar-brand" to="/">
        <i class="bi bi-mortarboard-fill me-2"></i>Tutor Connect
      </RouterLink>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <!-- Public -->
          <li class="nav-item">
            <RouterLink class="nav-link" to="/tutors">
              <i class="bi bi-search me-1"></i>Find Tutors
            </RouterLink>
          </li>

          <!-- Student Links -->
          <template v-if="authStore.userRole === 'student'">
            <li class="nav-item">
              <RouterLink class="nav-link" to="/student/dashboard">
                <i class="bi bi-grid-fill me-1"></i>Dashboard
              </RouterLink>
            </li>
            <li class="nav-item">
              <RouterLink class="nav-link" to="/student/bookings">
                <i class="bi bi-calendar-check me-1"></i>My Bookings
              </RouterLink>
            </li>
            <li class="nav-item">
              <RouterLink class="nav-link" to="/student/profile">
                <i class="bi bi-person me-1"></i>Profile
              </RouterLink>
            </li>
          </template>

          <!-- Tutor Links -->
          <template v-if="authStore.userRole === 'tutor'">
            <li class="nav-item">
              <RouterLink class="nav-link" to="/tutor/dashboard">
                <i class="bi bi-grid-fill me-1"></i>Dashboard
              </RouterLink>
            </li>
            <li class="nav-item">
              <RouterLink class="nav-link" to="/tutor/profiles">
                <i class="bi bi-person-badge me-1"></i>My Profiles
              </RouterLink>
            </li>
            <li class="nav-item">
              <RouterLink class="nav-link" to="/tutor/bookings">
                <i class="bi bi-calendar-check me-1"></i>Bookings
              </RouterLink>
            </li>
          </template>

          <!-- Admin Links -->
          <template v-if="authStore.userRole === 'admin'">
            <li class="nav-item">
              <RouterLink class="nav-link" to="/admin/dashboard">
                <i class="bi bi-speedometer2 me-1"></i>Dashboard
              </RouterLink>
            </li>
            <li class="nav-item">
              <RouterLink class="nav-link" to="/admin/users">
                <i class="bi bi-people me-1"></i>Users
              </RouterLink>
            </li>
            <li class="nav-item">
              <RouterLink class="nav-link" to="/admin/reviews">
                <i class="bi bi-journal-text me-1"></i>Reviews
              </RouterLink>
            </li>
          </template>
        </ul>

        <ul class="navbar-nav">
          <template v-if="authStore.isAuthenticated">
            <li class="nav-item">
              <span class="nav-link text-light opacity-75">
                <i class="bi bi-person-circle me-1"></i>{{ authStore.userName }}
                <span class="badge ms-1" :class="roleBadgeClass">{{ authStore.userRole }}</span>
              </span>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#" @click.prevent="handleLogout">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
              </a>
            </li>
          </template>
          <template v-else>
            <li class="nav-item">
              <RouterLink class="nav-link" to="/login">
                <i class="bi bi-box-arrow-in-right me-1"></i>Login
              </RouterLink>
            </li>
            <li class="nav-item">
              <RouterLink class="nav-link" to="/register">
                <i class="bi bi-person-plus me-1"></i>Register
              </RouterLink>
            </li>
          </template>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const roleBadgeClass = computed(() => {
  switch (authStore.userRole) {
    case 'admin': return 'bg-danger'
    case 'tutor': return 'bg-info'
    case 'student': return 'bg-success'
    default: return 'bg-secondary'
  }
})

function handleLogout() {
  authStore.logout()
  router.push('/login')
}
</script>
