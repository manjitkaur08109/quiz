<template>
  <v-app-bar color="primary">

    <v-app-bar-nav-icon @click="drawer = !drawer" />
    <v-toolbar-title>Quiz</v-toolbar-title>
    <v-spacer></v-spacer>

    <v-menu location="bottom end">
      <template #activator="{ props }">
        <v-btn v-bind="props" icon variant="text">
          <v-icon>mdi-email-outline</v-icon>
        </v-btn>
      </template>

      <v-card width="300">
        <v-list density="compact">
          <v-list-item v-if="emails.length === 0">
            <v-list-item-title>No Emails</v-list-item-title>
          </v-list-item>

          <v-list-item v-for="email in emails.slice(0, 5)" :key="email.id">
            <v-list-item-title>{{ email.subject }}</v-list-item-title>
            <v-list-item-subtitle>{{ email.message }}</v-list-item-subtitle>
          </v-list-item>
        </v-list>

        <v-card-actions class="justify-end">
          <v-btn variant="text" color="primary" @click="goToEmails">
            View all
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-menu>
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
const emails = ref([]);
const unreadCount = ref(0);

const api = inject("api");
const router = useRouter();
const drawer = ref(true);
const menu = ref(false);

const permissions = JSON.parse(
  localStorage.getItem("permissions") || "[]"
);


const items = [
  { title: "Dashboard", path: "/", icon: "mdi-view-dashboard", permission: 'view dashboard' },
  { title: "Quiz", path: "/quiz", icon: "mdi-help-circle-outline", permission: 'create quiz' },
  { title: "Users", path: "/users", icon: "mdi-account-group", permission: 'view user' },
  { title: "Category", path: "/category", icon: "mdi-view-grid-outline", permission: 'create category' },
  { title: "Discover", path: "/discover", icon: "mdi-compass-outline", permission: 'view discover' },
  { title: "MyLearning", path: "/myLearning", icon: "mdi-school-outline", permission: 'view myLearning' },
  { title: "Payments", path: "/payments", icon: "mdi-cash-multiple", permission: 'view payment' },
  { title: "Notifications", path: "/notifications", icon: "mdi-bell-outline" },
  { title: "Role and Permission", path: "/rolepermission", icon: "mdi-account-group-outline", permission: 'view rolepermission' },
  { title: "Email Logs", path: "/email_logs", icon: "mdi-email-outline" },
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
  return items.filter(
    (item) =>
      !item.permission || permissions.includes(item.permission)
  );
});


const logout = async () => {
  try {
    await api.post(
      "/logout",
      {},

    );

    localStorage.removeItem("token");
    localStorage.removeItem("permissions");
    localStorage.removeItem("user");
    router.push("/login");
  } catch (error) {
    console.error("Logout failed:", error);
    localStorage.removeItem("token");
    localStorage.removeItem("permissions");
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

const goToEmails = () => {
  router.push("/email_logs");
};

const updateUnreadCount = () => {
  unreadCount.value = Number(localStorage.getItem("unreadCount") || 0);
};


const fetchHeaderNotifications = async () => {
  try {
    const res = await api.get("/notifications/index");

    notifications.value = res?.data?.data?.data ?? [];

    const unread = notifications.value.filter(n => !n.read_at).length;
    unreadCount.value = unread;

    localStorage.setItem("unreadCount", unread);
  } catch (e) {
    console.error("Header notification error", e);
  }
};


const fetchHeaderEmails = async () => {
  try {
    const res = await api.get("/email_logs/index");
    emails.value = res.data.data || [];
  } catch (e) {
    console.error("Header email error", e);
  }
}

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

onMounted(() => {
  fetchHeaderNotifications();
  fetchHeaderEmails();
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
