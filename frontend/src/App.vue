<template>
  <router-view></router-view>
</template>

<script setup>
// Main entry point
</script>

<style>
/* Reset and Global transitions only */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: 'Inter', sans-serif;
  background-color: var(--surface);
  color: var(--on-surface);
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
