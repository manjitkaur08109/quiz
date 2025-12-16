<template>
  <v-app>
    <Header v-if="isLoggedIn" />
    <v-main>
      <v-container fluid>
            <v-alert
    v-if="isImpersonating"
    type="error"
    variant="flat"
    class="rounded-0 text-white"
    style="background-color: #d32f2f"
    @click="revertToAdmin"
    >
    Back to Admin Account
</v-alert>
        <router-view />
         <Toast ref="toastRef" />
      </v-container>
    </v-main>
    <!-- <Footer v-if="isLoggedIn"/> -->
  </v-app>
</template>

<script setup>
import { useRoute } from "vue-router";
import { ref, provide, watch ,computed ,inject} from "vue";
import Toast from "@/components/Toast.vue";
const toast = inject("toast");
const api = inject("api");

import Header from "@/components/Header.vue";
import Footer from "@/components/Footer.vue";
const isLoggedIn = ref(localStorage.getItem("token"));
const route = useRoute();
watch(route, (to) => {
  isLoggedIn.value = localStorage.getItem("token");

});
// Check impersonation mode
const isImpersonating = computed(() => {
  return !!localStorage.getItem("adminBackupToken");
});

const revertToAdmin = async () => {

     await api.post(
      "/logout",
      {},

    );
  const adminToken = localStorage.getItem("adminBackupToken");
  if (!adminToken)
    return toast.value.showToast("No admin session found", "error");

  // 🔹 Admin token wapas set karo
  localStorage.setItem("token", adminToken);
  localStorage.removeItem("adminBackupToken");

  // 🔹 Admin user data dobara fetch karo
  try {
    const { data } = await api.get("/profile");
    localStorage.setItem("user", JSON.stringify(data.data));
    user.value = data.data;
  } catch (e) {
    console.error("Admin user reload failed", e);
  }

  // 🔹 Page reload karo taaki naya admin data apply ho jaaye
  setTimeout(() => {
    window.location.href = "/"; // ya "/dashboard"
  }, 1000);
};
const toastRef = ref(null);
provide("toast", toastRef);
</script>
