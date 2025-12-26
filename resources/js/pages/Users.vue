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
      <v-btn v-if="can('create user')"
        class="ml-5"
        color="primary"
        @click="goToAdduser"
        prepend-icon="mdi-plus">
         Add New user
      </v-btn>
    </v-card-title>

    <v-divider></v-divider>

    <v-data-table
      v-model:search="search"
      :filter-keys="['name', 'email']"
      :items="users"
       class="text-caption"
    density="compact"
      :headers="headers"
    >
      <template #item.created_at="{ item }">
                {{ moment(item.created_at).format("DD MMM YYYY, hh:mm A") }}
            </template>

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

        <v-btn v-if="can('edit user')" size="x-small" icon color="primary" @click="edituser(item.id)">
    <v-icon>mdi-pencil</v-icon>
  </v-btn>
 
        <v-btn
        v-if="can('delete user')"
          size="x-small"
          class="ml-2"
          icon
          color="red"
          @click="deleteUser(item.id)"
        >
          <v-icon>mdi-delete</v-icon>
        </v-btn>

          <v-btn v-if="can('view user')" icon color="success" class="ml-2" size="x-small" @click="impersonateUser(item)">
            <v-icon left>mdi-account-switch</v-icon>
    </v-btn>
 
</template>

    </v-data-table>
  </v-card>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { inject } from "vue";
import { useRouter, useRoute } from "vue-router";
const permissions = JSON.parse(localStorage.getItem("permissions") || "[]");
 
import moment from "moment";
const can = (permission) => {
  return permissions.includes(permission);
};
const router = useRouter();
const route = useRoute();
const api = inject("api");
const toast = inject("toast");
const search = ref("");
const users = ref([]);
const headers = [
  { title: "Name", key: "name" },
  { title: "Email", key: "email" },
  { title: "Created At", key: "created_at" },
  { title: "Actions", key: "actions", sortable: false },
];

const goToAdduser = () => {
  router.push("/adduser");
}

const edituser = (id) => {
  router.push(`/edituser/${id}`);
}

const getUsers = async () => {
   if (!can("view user")) {
    toast.value.showToast("You are not authorized to view user", "error");
    router.push("/users"); 
    return;
  }
  try {
    const res = await api.get("/users");
    users.value = res.data.data;
  } catch (error) {

    toast.value.showToast(
      error?.response?.data?.message || "Something went wrong!",
      "error"
    );
  }
};
const deleteUser = async (id) => {
  
   if (!can("delete user")) {
    toast.value.showToast("You are not authorized to delete user", "error");
    router.push("/users"); 
    return;
  }
  if (!confirm("Are you sure you want to delete this user?")) return;

  try {
    await api.delete(`/users/delete/${id}`);
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
    const permissions = JSON.parse(localStorage.getItem("permissions"));

    const response = await api.post(
        "impersonate",
        { user_id: userToImpersonate.id }
    );
    
    localStorage.setItem("adminBackupToken", token);
    localStorage.setItem("permissionsBackupToken", JSON.stringify(permissions));

    const newToken = response.data.data.token;
    const newUser = response.data.data.user;

    localStorage.setItem("token", newToken);
    localStorage.setItem("user", JSON.stringify(newUser));
    
    localStorage.setItem(
      "permissions",
      JSON.stringify(response.data.data.permissions || [])
    );
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

