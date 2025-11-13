<template>
  <v-snackbar
    v-model="visible"
    :color="color"
    :timeout="timeout"
    location="top right"
    multi-line
    elevation="5"
  >
    <v-icon start>{{ icon }}</v-icon>
    {{ message }}

    <template #actions>
      <v-btn icon="mdi-close" @click="visible = false"></v-btn>
    </template>
  </v-snackbar>
</template>

<script setup>
import { ref } from "vue";

const visible = ref(false);
const message = ref("");
const color = ref("success");
const icon = ref("mdi-check-circle");
const timeout = ref(3000);

function showToast(text, type = "success") {
    alert(text);
  message.value = text;
  color.value = type === "error" ? "error" : type === "warning" ? "warning" : "success";
  icon.value =
    type === "error"
      ? "mdi-alert-circle"
      : type === "warning"
      ? "mdi-alert"
      : "mdi-check-circle";
  visible.value = true;
}

// expose the showToast method
defineExpose({ showToast });
</script>
