<template>
  <v-app>
    <!-- Header content -->
    <Header v-if="isLoggedIn" />
    <!-- Main content -->
    <v-main>
      <v-container fluid>
        <router-view />
         <Toast ref="toastRef" />
      </v-container>
    </v-main>
    <!-- Footer content -->
    <Footer v-if="isLoggedIn"/>
  </v-app>
</template>

<script setup>
import { useRoute } from "vue-router";
import { ref, provide, watch } from "vue";
import Toast from "@/components/Toast.vue";

import Header from "@/components/Header.vue";
import Footer from "@/components/Footer.vue";
const isLoggedIn = ref(localStorage.getItem("token"));
const route = useRoute();
watch(route, (to) => {
  isLoggedIn.value = localStorage.getItem("token");
});

const toastRef = ref(null);
provide("toast", toastRef);
</script>
