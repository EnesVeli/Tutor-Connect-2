<template>
  <div id="app" class="d-flex flex-column min-vh-100">
    <NavBar />
    <main class="flex-grow-1">
      <RouterView />
    </main>
    <Footer />
  </div>
</template>

<script setup>
import NavBar from '@/components/layout/NavBar.vue'
import Footer from '@/components/layout/Footer.vue'
</script>
