<template>
  <footer class="footer text-center">
    <div class="container">
      <p class="mb-0">
        <i class="bi bi-mortarboard-fill me-1"></i>
        &copy; {{ currentYear }} Tutor Connect — All rights reserved.
      </p>
    </div>
  </footer>
</template>

<script setup>
const currentYear = new Date().getFullYear()
</script>
