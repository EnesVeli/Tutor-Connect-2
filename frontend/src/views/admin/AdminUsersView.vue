<template>
  <div class="container mt-5">
    <div class="page-header">
      <h1><i class="bi bi-people me-2"></i>User Management</h1>
      <p>View, edit, and manage all platform users.</p>
    </div>

    <LoadingSpinner v-if="loading" />

    <template v-else>
      <AlertMessage v-if="error" type="danger" :message="error" @close="error = null" />
      <AlertMessage v-if="success" type="success" :message="success" @close="success = null" />

      <div class="card">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Subject</th>
                  <th>Bio</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <UserRow v-for="user in users" :key="user.id + '-' + (user.profile_id || 'no')" :user="user"
                  @edit-user="editUser" @delete-user="confirmDeleteUser" @delete-profile="confirmDeleteProfile" />
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div v-if="users.length === 0" class="empty-state">
        <i class="bi bi-people"></i>
        <p>No users found.</p>
      </div>
    </template>

    <ConfirmModal :visible="showConfirm" :message="confirmMessage" @confirm="executeConfirmedAction" @cancel="showConfirm = false" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api/axios'
import UserRow from '@/components/admin/UserRow.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'
import AlertMessage from '@/components/ui/AlertMessage.vue'
import ConfirmModal from '@/components/ui/ConfirmModal.vue'

const router = useRouter()
const users = ref([])
const loading = ref(true)
const error = ref(null)
const success = ref(null)
const showConfirm = ref(false)
const confirmMessage = ref('')
const pendingAction = ref(null)

onMounted(async () => {
  await fetchUsers()
})

async function fetchUsers() {
  loading.value = true
  try {
    const res = await api.get('/admin/users')
    users.value = res.data
  } catch {
    error.value = 'Failed to load users'
  } finally {
    loading.value = false
  }
}

function editUser(user) {
  router.push(`/admin/users/${user.id}/edit`)
}

function confirmDeleteUser(user) {
  confirmMessage.value = `Are you sure you want to delete ${user.first_name} ${user.last_name}? This will cascade to all their data.`
  pendingAction.value = async () => {
    try {
      await api.delete(`/admin/users/${user.id}`)
      success.value = 'User deleted successfully'
      await fetchUsers()
    } catch (err) {
      error.value = err.response?.data?.error || 'Failed to delete user'
    }
  }
  showConfirm.value = true
}

function confirmDeleteProfile(user) {
  confirmMessage.value = `Delete the "${user.subject}" profile for ${user.first_name} ${user.last_name}? The user account will remain.`
  pendingAction.value = async () => {
    try {
      await api.delete(`/admin/tutor-profiles/${user.profile_id}`)
      success.value = 'Profile deleted successfully'
      await fetchUsers()
    } catch (err) {
      error.value = err.response?.data?.error || 'Failed to delete profile'
    }
  }
  showConfirm.value = true
}

function executeConfirmedAction() {
  showConfirm.value = false
  if (pendingAction.value) pendingAction.value()
}
</script>
