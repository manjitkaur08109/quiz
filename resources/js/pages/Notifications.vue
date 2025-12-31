<template>
  <v-card flat>
    <v-card-title class="d-flex align-center pe-2">
      Notifications
      <v-spacer></v-spacer>

      <v-text-field v-model="search" density="compact" label="Search" prepend-inner-icon="mdi-magnify"
        variant="solo-filled" @update:modelValue="onSearch" :flat="true" :hide-details="true" :single-line="true" />
      <v-spacer></v-spacer>
      <v-btn color="red" size="small" variant="outlined" class="ml-2" @click="deleteAllNotifications">
        <v-icon start>mdi-delete-sweep</v-icon>
        Delete All
      </v-btn>


    </v-card-title>

    <v-divider></v-divider>
    <v-data-table v-model:search="search" :headers="headers" :items="notificationItems"
      :filter-keys="['data.title', 'data.description']" 
      v-model:page="pagination.current_page"
      :items-per-page-options="[5, 10, 25, 50]"
      v-model:items-per-page="pagination.per_page"
      :items-length="notificationItems.length"
      @update:page="onPageChange"
      @update:items-per-page="onPerPageChange">
      <template #item.sn="{ index }">
        {{ index + 1 }}
      </template>
      <!-- Title -->
      <template #item.title="{ item }">
        {{ item.data.title }}
      </template>

      <!-- Message -->
      <template #item.description="{ item }">
        {{ item.data?.description }}
      </template>

      <!-- Status -->
      <template #item.status="{ item }">
        <v-chip size="small" :color="item.read_at ? 'green' : 'orange'">
          {{ item.read_at ? 'read' : 'unread' }}
        </v-chip>
      </template>

      <template #item.actions="{ item }">
        <v-btn class="ml-2" size="x-small" icon color="primary" :disabled="item.read_at" @click="markAsRead(item.id)">
          <v-icon>mdi-check</v-icon>
        </v-btn>


        <v-btn class="ml-2" icon="mdi-delete" size="x-small" color="red" @click.stop="deleteNotification(item.id)" />


      </template>

    </v-data-table>
  </v-card>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import { inject } from "vue";
const api = inject("api");
const toast = inject("toast");
const router = useRouter();
const search = ref("");
const loading = ref(false);

const notifications = ref([]);
// const unreadCount = ref(0);

const notificationItems = ref([]);
const headers = [
  { title: "S.No", value: "sn" },
  { title: "Title", value: "title" },
  { title: "Description", value: "description" },
  { title: "Status", value: "status" },
  { title: "Actions", value: "actions", sortable: false },
];

const pagination = ref({
  current_page: 1,
  per_page: 5,
  total: 0,
});

const onPageChange = (page) => {
  pagination.value.current_page = page;
  fetchNotifications();
};

const onPerPageChange = (perPage) => {
  pagination.value.per_page = perPage;
  pagination.value.current_page = 1;
  fetchNotifications();
};

const onSearch = () => {
  pagination.value.current_page = 1;
  fetchNotifications();
};
const unreadCount = ref(
  Number(localStorage.getItem("unreadCount") || 0)
);

// listen for updates
const updateUnreadCount = () => {
  unreadCount.value = Number(localStorage.getItem("unreadCount") || 0);
};

onMounted(() => {
  window.addEventListener("notifications-updated", updateUnreadCount);
});

onUnmounted(() => {
  window.removeEventListener("notifications-updated", updateUnreadCount);
});

const fetchNotifications = async () => {
  loading.value = true;
  try {
    const res = await api.get(`/notifications/index?page=${pagination.value.current_page}&per_page=${pagination.value.per_page}&search=${search.value}`);
    notificationItems.value = res.data.data.data;
    pagination.value.current_page = res.data.data.current_page;
    pagination.value.per_page = res.data.data.per_page;
    pagination.value.total = res.data.data.total;
    const unread = notificationItems.value.filter(
      n => n.read_at === null
    ).length;

    unreadCount.value = unread;

    // 🔔 store for header
    localStorage.setItem("unreadCount", unread);

    // 🔁 notify header
    window.dispatchEvent(new Event("notifications-updated"));

  } catch (error) {
    toast.value.showToast(
      error?.response?.data?.message || "Something went wrong!",
      "error"
    );
  } finally {
    loading.value = false;
  }
};

const markAsRead = async (id) => {
  try {
    await api.post(`/notifications/markAsRead/${id}`);
    toast.value.showToast("Notification marked as read", "success");
    fetchNotifications();
  } catch (error) {
    toast.value.showToast("Failed to update notification", "error");
  }
};

const deleteNotification = async (id) => {
  console.log(id);
  if (!confirm("Delete this notification?")) return;

  try {
    await api.delete(`/notifications/destroy/${id}`);
    toast.value.showToast("Notification deleted", "success");
    fetchNotifications();
  } catch (error) {
    toast.value.showToast("Failed to delete notification", "error");
  }
};

const deleteAllNotifications = async () => {
  if (!confirm("Delete all notifications?")) return;

  try {
    await api.delete("/notifications/destroyAll");
    toast.value.showToast("All notifications deleted", "success");

    // instant UI clear
    notificationItems.value = [];
    unreadCount.value = 0;

    localStorage.setItem("unreadCount", 0);
    window.dispatchEvent(new Event("notifications-updated"));
  } catch (error) {
    toast.value.showToast("Failed to delete notifications", "error");
  }
};

onMounted(() => {
  fetchNotifications();
});

</script>
