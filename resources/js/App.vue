<template>
  <v-app>
    <!-- Header content -->
    <Header />
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
import Header from "@/components/Header.vue";
import Footer from "@/components/Footer.vue";
</script>
