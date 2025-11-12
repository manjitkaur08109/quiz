<template>
  <!-- App Bar -->
  <v-app-bar color="primary">
    <v-app-bar-nav-icon @click="drawer = !drawer" />
    <v-toolbar-title>Quiz</v-toolbar-title>
    <v-spacer></v-spacer>
    <v-menu
      v-model="menu"
      :close-on-content-click="false"
      location="bottom end"
    >
      <template #activator="{ props }">
        <v-btn v-bind="props" icon="mdi-account" variant="text"></v-btn>
      </template>
      <v-list>
        <v-list-item @click="goToProfile">
          <v-list-item-title>My Profile</v-list-item-title>
        </v-list-item>
        <v-divider></v-divider>

        <v-list-item v-if="isImpersonating" @click="revertToAdmin">
          <v-list-item-title>Return to Admin</v-list-item-title>
        </v-list-item>
        <v-divider></v-divider>
        <v-list-item @click="logout">
          <v-list-item-title>Logout</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
  </v-app-bar>

  <!-- Navigation Drawer -->
  <v-navigation-drawer v-model="drawer" app permanent>
    <v-list>
      <v-list-item
        v-for="item in filteredItems"
        :key="item.title"
        :title="item.title"
        :prepend-icon="item.icon"
        :to="item.path"
        link
      />
    </v-list>
  </v-navigation-drawer>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { inject } from "vue";

const toast = inject("toast");
const router = useRouter();
const drawer = ref(true);
const menu = ref(false);

const items = [
  { title: "Dashboard", path: "/", icon: "mdi-view-dashboard" },
  { title: "Quiz", path: "/quiz", icon: "mdi-help-circle-outline" },
  { title: "Users", path: "/users", icon: "mdi-account-group" },
  { title: "Category", path: "/category", icon: "mdi-view-grid-outline" },
  { title: "Discover", path: "/discover", icon: "mdi-compass-outline" },
  { title: "MyLearning", path: "/myLearning", icon: "mdi-school-outline" },
  { title: "Products", path: "/products", icon: "mdi-school-outline" },
];

// const user = JSON.parse(localStorage.getItem("user") || "{}");

let user = {};
try {
  const storedUser = localStorage.getItem("user");
  user = storedUser ? JSON.parse(storedUser) : {};
} catch (e) {
  console.error("Invalid user JSON:", e);
  user = {};
}

const filteredItems = computed(() => {
  if (user?.account_type === "admin") {
    return items;
  } else {
    return items.filter(
      (item) => item.title === "Discover" || item.title === "MyLearning"
    );
  }
});
// Check impersonation mode
const isImpersonating = computed(() => {
  return !!localStorage.getItem("adminBackupToken");
});

const logout = async () => {
  try {
    const token = localStorage.getItem("token");
    await axios.post(
      "/api/logout",
      {},
      {
        headers: { Authorization: `Bearer ${token}` },
      }
    );

    // Token delete from localStorage
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    router.push("/login");
  } catch (error) {
    console.error("Logout failed:", error);
    // toast.value.showToast("Something went wrong during logout!", "error");
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    router.push("/login");
  }
};

const revertToAdmin = async () => {
  const adminToken = localStorage.getItem("adminBackupToken");
  if (!adminToken)
    return toast.value.showToast("No admin session found", "error");

  // 🔹 Admin token wapas set karo
  localStorage.setItem("token", adminToken);
  localStorage.removeItem("adminBackupToken");

  // 🔹 Admin user data dobara fetch karo
  try {
    const { data } = await axios.get("/api/profile", {
      headers: { Authorization: `Bearer ${adminToken}` },
    });
    localStorage.setItem("user", JSON.stringify(data.data));
    user.value = data.data;
  } catch (e) {
    console.error("Admin user reload failed", e);
  }

  // 🔹 Success message dikhao
  toast.value.showToast("Returned to Admin account", "success");

  // 🔹 Page reload karo taaki naya admin data apply ho jaaye
  setTimeout(() => {
    window.location.href = "/"; // ya "/dashboard"
  }, 1000);
};

const goToProfile = () => {
  router.push("/profile");
};
</script>
