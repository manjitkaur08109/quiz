<template>
  <v-app-bar color="primary">

    <v-app-bar-nav-icon @click="drawer = !drawer" />
    <v-toolbar-title>Quiz</v-toolbar-title>
    <v-spacer></v-spacer>

    <v-menu location="bottom end">
      <template #activator="{ props }">
        <v-btn v-bind="props" icon variant="text">
          <v-badge v-if="unreadCount > 0" :content="unreadCount" color="red">
            <v-icon>mdi-bell-outline</v-icon>
          </v-badge>

          <v-icon v-else>mdi-bell-outline</v-icon>
        </v-btn>
      </template>

      <v-card width="320">
        <v-list density="compact">
          <v-list-item v-for="notification in notifications.slice(0, 5)" :key="notification.id">
            <v-list-item-title>
              {{ notification.data.title }}
            </v-list-item-title>
            <v-list-item-subtitle>
              {{ notification.data.description }}
            </v-list-item-subtitle>
          </v-list-item>

          <v-list-item v-if="notifications.length === 0">
            <v-list-item-title>No notifications</v-list-item-title>
          </v-list-item>
        </v-list>
        <v-card-actions class="justify-end">
          <v-btn variant="text" color="primary" @click="goToNotifications">
            View all
          </v-btn>
        </v-card-actions>

      </v-card>
    </v-menu>

    <v-menu v-model="menu" :close-on-content-click="false" location="bottom end">

      <template #activator="{ props }">
        <v-btn v-bind="props" icon="mdi-account" variant="text"></v-btn>
      </template>
      <v-list>
        <v-list-item @click="goToProfile">
          <v-list-item-title>My Profile</v-list-item-title>
        </v-list-item>
        <v-divider></v-divider>

        <v-divider></v-divider>
        <v-list-item @click="logout">
          <v-list-item-title>Logout</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
  </v-app-bar>

  <v-navigation-drawer v-model="drawer" app permanent>
    <v-list>
      <v-list-item v-for="item in filteredItems" :key="item.title" :title="item.title" :prepend-icon="item.icon"
        :to="item.path" link />
    </v-list>
  </v-navigation-drawer>

</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import { inject } from "vue";


const notifications = ref([]);
const unreadCount = ref(0);

const api = inject("api");
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
  { title: "Payments", path: "/payments", icon: "mdi-cash-multiple", },
  { title: "Notifications", path: "/notifications", icon: "mdi-bell-outline", }
];

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
    // Admin ke liye Discover aur MyLearning hata do
    return items.filter(
      (item) => item.title !== "Discover" && item.title !== "MyLearning"
    );
  } else {
    // Normal user ke liye sirf Discover aur MyLearning
    return items.filter(
      (item) => item.title === "Discover" || item.title === "MyLearning"
    );
  }
});

const logout = async () => {
  try {
    await api.post(
      "/logout",
      {},

    );

    localStorage.removeItem("token");
    localStorage.removeItem("user");
    router.push("/login");
  } catch (error) {
    console.error("Logout failed:", error);
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    router.push("/login");
  }
};

const goToProfile = () => {
  router.push("/profile");
};

const goToNotifications = () => {
  router.push("/notifications");
};

const updateUnreadCount = () => {
  unreadCount.value = Number(localStorage.getItem("unreadCount") || 0);
};

const fetchHeaderNotifications = async () => {
  try {
    const res = await api.get("/notifications/index");
    notifications.value = res.data.data || [];

    const unread = notifications.value.filter(n => n.read_at === null).length;
    unreadCount.value = unread;
    localStorage.setItem("unreadCount", unread);
  } catch (e) {
    console.error("Header notification error", e);
  }
};

// ---------------- PUBLIC ECHO LISTENER ----------------
const setupEchoListener = () => {
  if (!window.Echo) {
    console.warn("Echo not available");
    return;
  }

  try {
    window.Echo.leave("public-notifications");
  } catch (e) { }

  window.Echo
    .channel("public-notifications")
    .listen(".notification.created", (e) => {
      console.log("Public notification received:", e);

      fetchHeaderNotifications();
      updateUnreadCount();

      window.dispatchEvent(new Event("notifications-updated"));
    });
};

// ---------------- LIFECYCLE ----------------
onMounted(() => {
  fetchHeaderNotifications();
  setupEchoListener();

  window.addEventListener("notifications-updated", updateUnreadCount);
});

onUnmounted(() => {
  window.removeEventListener("notifications-updated", updateUnreadCount);

  if (window.Echo) {
    window.Echo.leave("public-notifications");
  }
});

</script>
