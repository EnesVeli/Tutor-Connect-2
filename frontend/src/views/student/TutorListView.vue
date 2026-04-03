<template>
  <div class="container mt-5">
    <div class="page-header">
      <h1><i class="bi bi-search me-2"></i>Find a Tutor</h1>
      <p>Browse our directory of qualified tutors. Filter by subject and price to find your perfect match.</p>
    </div>

    <TutorFilter :current-filters="tutorStore.filters" @filter-change="tutorStore.setFilters" />

    <LoadingSpinner v-if="tutorStore.isLoading" />

    <template v-else>
      <AlertMessage v-if="tutorStore.error" type="danger" :message="tutorStore.error" @close="tutorStore.error = null" />

      <p v-if="tutorStore.hasResults" class="text-muted mb-3">
        Showing {{ showingRange }} of {{ tutorStore.totalTutors }} tutors
      </p>

      <div v-if="tutorStore.hasResults" class="row g-4">
        <div v-for="tutor in tutorStore.tutors" :key="tutor.id" class="col-12 col-sm-6 col-lg-4">
          <TutorCard :tutor="tutor" />
        </div>
      </div>

      <div v-else class="empty-state">
        <i class="bi bi-person-x"></i>
        <h5>No Tutors Found</h5>
        <p>Try adjusting your filters or check back later.</p>
      </div>

      <PaginationControl
        :current-page="tutorStore.currentPage"
        :total-pages="tutorStore.totalPages"
        @page-change="tutorStore.setPage"
      />
    </template>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useTutorStore } from '@/stores/tutors'
import TutorCard from '@/components/tutors/TutorCard.vue'
import TutorFilter from '@/components/tutors/TutorFilter.vue'
import PaginationControl from '@/components/tutors/PaginationControl.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'
import AlertMessage from '@/components/ui/AlertMessage.vue'

const tutorStore = useTutorStore()

const showingRange = computed(() => {
  const start = (tutorStore.currentPage - 1) * 9 + 1
  const end = Math.min(tutorStore.currentPage * 9, tutorStore.totalTutors)
  return `${start}–${end}`
})

onMounted(() => {
  tutorStore.fetchTutors()
})
</script>
