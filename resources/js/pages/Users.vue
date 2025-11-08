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
      <template #item.image="{ item }">
        <v-avatar size="40">
          <v-img
            :src="
              item.image ||
              'https://cdn-icons-png.flaticon.com/512/3135/3135715.png'
            "
          />
        </v-avatar>
      </template>

      <template #item.actions="{ item }">
        <v-btn size="x-small" icon color="primary" @click="viewUser(item)">
          <v-icon>mdi-eye</v-icon>
        </v-btn>

        <v-btn
          size="x-small"
          class="ml-2"
          icon
          color="red"
          @click="deleteUser(item.id)"
        >
          <v-icon>mdi-delete</v-icon>
        </v-btn>

          <v-btn icon color="success" class="ml-2" size="x-small" @click="impersonateUser(item)">
            <v-icon left>mdi-account-switch</v-icon>
    </v-btn>
      </template>
    </v-data-table>
  </v-card>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { inject } from "vue";
import { useRouter, useRoute } from "vue-router";
const router = useRouter();
const route = useRoute();

const toast = inject("toast");
const search = ref("");
const users = ref([]);
const headers = [
  { title: "Name", key: "name" },
  { title: "Email", key: "email" },
  { title: "Created At", key: "created_at" },
  { title: "Actions", key: "actions", sortable: false },
];

const getUsers = async () => {
  try {
    const token = localStorage.getItem("token");
    const res = await axios.get("/api/users", {
      headers: { Authorization: `Bearer ${token}` },
    });
    users.value = res.data.data;
  } catch (error) {
    if (error?.response?.status === 401) {
      localStorage.removeItem("token");
      localStorage.removeItem("user");
      router.push("/login");
    }
    toast.value.showToast(
      error?.response?.data?.message || "Something went wrong!",
      "error"
    );
  }
};

const viewUser = (user) => {
  alert(`User: ${user.name}\nEmail: ${user.email}`);
};

const deleteUser = async (id) => {
  if (!confirm("Are you sure you want to delete this user?")) return;

  try {
    const token = localStorage.getItem("token");
    await axios.delete(`/api/users/${id}`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    users.value = users.value.filter((u) => u.id !== id);

    toast.value.showToast("User Deleted.", "success");
  } catch (error) {
    toast.value.showToast(
      error?.response?.data?.message || "Something went wrong!",
      "error"
    );
  }
};

const impersonateUser = async (userToImpersonate) => {
  try {
    const token = localStorage.getItem("token");


    const response = await axios.post(
        "api/impersonate",
        { user_id: userToImpersonate.id },
        { headers: { Authorization: `Bearer ${token}` } }
    );
    localStorage.setItem("adminBackupToken", token);

    const newToken = response.data.data.token;
    const newUser = response.data.data.user;

    localStorage.setItem("token", newToken);
    localStorage.setItem("user", JSON.stringify(newUser));

    toast.value.showToast(`Logged in as ${newUser.name}`, "success");
  setTimeout(() => {
      window.location.href = "/myLearning"; 
    }, 1000);

  } catch (error) {
    console.error(error);
    toast.value.showToast(
      error?.response?.data?.message || "Impersonation failed!",
      "error"
    );
  }
};


onMounted(() => {
  getUsers();
});
</script>

