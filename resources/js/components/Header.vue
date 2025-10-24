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
        <v-btn v-bind="props"  icon="mdi-account" variant="text"></v-btn>
      </template>
      <v-list>
        <v-list-item @click="goToProfile">
          <v-list-item-title>My Profile</v-list-item-title>
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
        v-for="item in items"
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
import { ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();
const drawer = ref(true);
const menu = ref(false);

const items = [
  { title: "Dashboard", path: "/", icon: "mdi-view-dashboard" },
  { title: "Quiz", path: "/quiz", icon: "mdi-help-circle-outline" },
  { title: "Users", path: "/users", icon: "mdi-account-group" },
  {title:"Category",path:"/category",icon:"mdi-view-grid-outline"},
  { title: "Registration", path: "/register", icon: "mdi-account-plus" },
];

const logout = () => {
  localStorage.removeItem("token");
  localStorage.removeItem("user");
  delete api.defaults.headers.common["Authorization"];
  router.push({ path: "/login", replace: true });
};
const goToProfile = () => {
  const token = localStorage.getItem("token");
  if (token) {
    router.push("/profile");
  } else {
    router.push("/login");
  }
};
// const goToProfile = () => {router.push('/profile')};
</script>
