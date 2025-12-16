<template>
  <v-footer
    class="quiz-footer text-center d-flex flex-column ga-2 py-4"
    color="indigo-lighten-1"
  >
    <div>{{ new Date().getFullYear() }} — <strong>Quiz</strong></div>
  </v-footer>
</template>
<script setup>
</script>
<style scoped>
.quiz-footer {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
}
</style>
