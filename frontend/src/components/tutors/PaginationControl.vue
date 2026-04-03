<template>
  <nav v-if="totalPages > 1" aria-label="Page navigation">
    <ul class="pagination justify-content-center">
      <li class="page-item" :class="{ disabled: currentPage <= 1 }">
        <a class="page-link" href="#" @click.prevent="$emit('page-change', currentPage - 1)">
          <i class="bi bi-chevron-left"></i>
        </a>
      </li>
      <li v-for="page in visiblePages" :key="page" class="page-item" :class="{ active: page === currentPage }">
        <a class="page-link" href="#" @click.prevent="$emit('page-change', page)">{{ page }}</a>
      </li>
      <li class="page-item" :class="{ disabled: currentPage >= totalPages }">
        <a class="page-link" href="#" @click.prevent="$emit('page-change', currentPage + 1)">
          <i class="bi bi-chevron-right"></i>
        </a>
      </li>
    </ul>
  </nav>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  currentPage: { type: Number, required: true },
  totalPages: { type: Number, required: true }
})

defineEmits(['page-change'])

const visiblePages = computed(() => {
  const pages = []
  const start = Math.max(1, props.currentPage - 2)
  const end = Math.min(props.totalPages, props.currentPage + 2)
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  return pages
})
</script>
