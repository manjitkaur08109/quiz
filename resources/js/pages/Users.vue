<template>
  <v-card flat>
    <v-card-title class="d-flex align-center pe-2">
      Users
      <v-spacer></v-spacer>

      <v-text-field
        v-model="search"
        density="compact"
        label="Search"
        prepend-inner-icon="mdi-magnify"
        variant="solo-filled"
        flat
        hide-details
        single-line
      ></v-text-field>
    </v-card-title>

    <v-divider></v-divider>

    <v-data-table
      v-model:search="search"
      :filter-keys="['name', 'email']"
      :items="users"
      :headers="headers"
      class="elevation-2"
    >
      <!-- 👤 Profile image (optional) -->
      <template #item.image="{ item }">
        <v-avatar size="40">
          <v-img :src="item.image || 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png'" />
        </v-avatar>
      </template>

      <!-- 👁️ Action buttons -->
      <template #item.actions="{ item }">
        <v-btn size="x-small" icon color="primary" @click="viewUser(item)">
          <v-icon>mdi-eye</v-icon>
        </v-btn>

        <v-btn size="x-small" icon color="error" @click="deleteUser(item.id)">
          <v-icon>mdi-delete</v-icon>
        </v-btn>
      </template>
    </v-data-table>
  </v-card>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const search = ref("");
const users = ref([]);
const headers = [
  { title: "Name", key: "name" },
  { title: "Email", key: "email" },
  { title: "Created At", key: "created_at" },
  { title: "Actions", key: "actions", sortable: false },
];

// ✅ Fetch users from Laravel API
const getUsers = async () => {
  try {
    const token = localStorage.getItem("token");
    const res = await axios.get("/api/users", {
      headers: { Authorization: `Bearer ${token}` },
    });
    users.value = res.data.data; // assuming {data: users[]}
  } catch (err) {
    console.error("Error fetching users:", err);
  }
};

// 👁️ View user (you can open dialog or redirect)
const viewUser = (user) => {
  alert(`User: ${user.name}\nEmail: ${user.email}`);
};

// 🗑️ Delete user
const deleteUser = async (id) => {
  if (!confirm("Are you sure you want to delete this user?")) return;

  try {
    const token = localStorage.getItem("token");
    await axios.delete(`/api/users/${id}`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    users.value = users.value.filter((u) => u.id !== id);
    alert("User deleted successfully!");
  } catch (err) {
    console.error("Delete error:", err);
    alert("Failed to delete user!");
  }
};

onMounted(() => {
  getUsers();
});

</script>

