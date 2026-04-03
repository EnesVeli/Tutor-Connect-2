<template>
  <div></div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

onMounted(() => {
  if (!authStore.isAuthenticated) {
    router.replace('/login')
    return
  }

  switch (authStore.userRole) {
    case 'student':
      router.replace('/student/dashboard')
      break
    case 'tutor':
      router.replace('/tutor/dashboard')
      break
    case 'admin':
      router.replace('/admin/dashboard')
      break
    default:
      router.replace('/login')
  }
})
</script>
