<template>
  <tr>
    <td>{{ user.id }}</td>
    <td>
      <div class="d-flex align-items-center">
        <img :src="avatarUrl" class="rounded-circle me-2" width="32" height="32" :alt="fullName">
        {{ fullName }}
      </div>
    </td>
    <td>{{ user.email }}</td>
    <td>
      <span class="badge" :class="roleBadge">{{ user.role }}</span>
    </td>
    <td>{{ user.subject || '—' }}</td>
    <td class="text-truncate" style="max-width: 150px;">{{ user.bio || '—' }}</td>
    <td>
      <div class="btn-group btn-group-sm">
        <button class="btn btn-outline-primary" @click="$emit('edit-user', user)" title="Edit">
          <i class="bi bi-pencil"></i>
        </button>
        <button class="btn btn-outline-danger" @click="$emit('delete-user', user)" title="Delete User">
          <i class="bi bi-trash"></i>
        </button>
        <button v-if="user.profile_id" class="btn btn-outline-warning" @click="$emit('delete-profile', user)" title="Delete Profile">
          <i class="bi bi-person-x"></i>
        </button>
      </div>
    </td>
  </tr>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  user: { type: Object, required: true }
})

defineEmits(['edit-user', 'delete-user', 'delete-profile'])

const fullName = computed(() => `${props.user.first_name} ${props.user.last_name}`)
const avatarUrl = computed(() =>
  `https://ui-avatars.com/api/?name=${props.user.first_name}+${props.user.last_name}&background=4f46e5&color=fff&size=32`
)
const roleBadge = computed(() => {
  switch (props.user.role) {
    case 'admin': return 'bg-danger'
    case 'tutor': return 'bg-info'
    case 'student': return 'bg-success'
    default: return 'bg-secondary'
  }
})
</script>
