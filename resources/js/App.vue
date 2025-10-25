<template>
  <v-app>
    <!-- Header content -->
    <Header v-if="isLoggedIn"  />
    <!-- Main content -->
    <v-main>
      <v-container fluid>
        <router-view />
      </v-container>
    </v-main>
    <!-- Footer content -->
    <Footer />
  </v-app>
</template>

<script setup>
import { ref, computed } from "vue";
import Header from "@/components/Header.vue";
import Footer from "@/components/Footer.vue";

const isLoggedIn = computed(() => !!localStorage.getItem("token"));
</script>
