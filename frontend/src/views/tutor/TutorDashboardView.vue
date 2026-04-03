<template>
  <div class="container mt-5">
    <div class="page-header">
      <h1><i class="bi bi-grid-fill me-2"></i>Welcome, {{ authStore.userName }}!</h1>
      <p>Your tutor dashboard — manage your profiles and bookings.</p>
    </div>

    <div class="row g-4">
      <div class="col-12 col-md-4">
        <RouterLink to="/tutor/profiles" class="text-decoration-none">
          <div class="card action-card h-100">
            <div class="card-body text-center p-4">
              <i class="bi bi-person-badge action-icon d-block mb-3"></i>
              <h5 class="fw-bold">My Profiles</h5>
              <p class="text-muted small mb-0">Create and manage your subject profiles</p>
            </div>
          </div>
        </RouterLink>
      </div>
      <div class="col-12 col-md-4">
        <RouterLink to="/tutor/bookings" class="text-decoration-none">
          <div class="card action-card h-100">
            <div class="card-body text-center p-4">
              <i class="bi bi-calendar-check action-icon d-block mb-3"></i>
              <h5 class="fw-bold">My Bookings</h5>
              <p class="text-muted small mb-0">View and manage student bookings</p>
            </div>
          </div>
        </RouterLink>
      </div>
      <div class="col-12 col-md-4">
        <RouterLink to="/tutor/profiles/new" class="text-decoration-none">
          <div class="card action-card h-100">
            <div class="card-body text-center p-4">
              <i class="bi bi-plus-circle action-icon d-block mb-3"></i>
              <h5 class="fw-bold">New Profile</h5>
              <p class="text-muted small mb-0">Add a new subject you can teach</p>
            </div>
          </div>
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
const authStore = useAuthStore()
</script>
