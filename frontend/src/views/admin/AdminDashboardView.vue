<template>
  <div class="container mt-5">
    <div class="page-header">
      <h1><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</h1>
      <p>Platform overview and statistics.</p>
    </div>

    <LoadingSpinner v-if="loading" />

    <template v-else>
      <!-- Stat Cards -->
      <div class="row g-4 mb-5">
        <div class="col-6 col-lg-3">
          <div class="stat-card stat-card-purple">
            <i class="bi bi-people-fill d-block mb-2"></i>
            <div class="stat-number">{{ stats.total_users }}</div>
            <div class="stat-label">Total Users</div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="stat-card stat-card-blue">
            <i class="bi bi-person-workspace d-block mb-2"></i>
            <div class="stat-number">{{ stats.total_tutors }}</div>
            <div class="stat-label">Active Tutors</div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="stat-card stat-card-green">
            <i class="bi bi-calendar-check-fill d-block mb-2"></i>
            <div class="stat-number">{{ stats.total_bookings }}</div>
            <div class="stat-label">Total Bookings</div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="stat-card stat-card-amber">
            <i class="bi bi-currency-euro d-block mb-2"></i>
            <div class="stat-number">€{{ stats.total_earnings }}</div>
            <div class="stat-label">Total Earnings</div>
          </div>
        </div>
      </div>

      <!-- Popular Tutors Table -->
      <div class="card">
        <div class="card-header bg-white">
          <h5 class="mb-0 fw-bold"><i class="bi bi-trophy me-2"></i>Most Popular Tutors</h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Tutor</th>
                  <th>Bookings</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(tutor, index) in stats.tutors_list" :key="index">
                  <td>
                    <span v-if="index < 3" class="badge" :class="index === 0 ? 'bg-warning' : index === 1 ? 'bg-secondary' : 'bg-danger'">
                      {{ index + 1 }}
                    </span>
                    <span v-else>{{ index + 1 }}</span>
                  </td>
                  <td>{{ tutor.first_name }} {{ tutor.last_name }}</td>
                  <td><span class="badge bg-primary">{{ tutor.booking_count }}</span></td>
                </tr>
                <tr v-if="!stats.tutors_list?.length">
                  <td colspan="3" class="text-center text-muted py-3">No bookings data yet</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api/axios'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'

const stats = ref({
  total_users: 0, total_tutors: 0, total_students: 0,
  total_bookings: 0, total_earnings: 0, tutors_list: []
})
const loading = ref(true)

onMounted(async () => {
  try {
    const res = await api.get('/admin/stats')
    stats.value = res.data
  } catch { /* handled */ }
  finally { loading.value = false }
})
</script>
