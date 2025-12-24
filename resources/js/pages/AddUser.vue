<template>
  <v-container class="py-8">
    <v-card class="mx-auto" min-width="500" elevation="8">
      <v-card-title class="text-h6">
        <v-icon size="x-small" icon="mdi-plus-circle-outline" class="mr-2" />
        Add New user
      </v-card-title>

      <v-divider></v-divider>

      <v-card-text>
        <v-form ref="formRef" @submit.prevent="handleSubmit">
          <v-text-field
            v-model="users.name"
            label="users name"
            :rules="validateMaxLength('users', 20)"
            prepend-inner-icon="mdi-shape-outline"
            required
          />

          <v-text-field
            v-model="users.email"
            label="Email"
            prepend-inner-icon="mdi-email"
            type="email"
            :rules="validateEmail('Email')"
            required
          />

          <v-text-field
          v-model="users.password"
          label="Password"
          prepend-inner-icon="mdi-lock"
          type="password"
          :rules="validateRequired('Password')"
          required
        />

        <v-select v-model="users.role_id" :items="rolepermission" item-title="name" item-value="id" label="Select Role" :rules="validateRequired('Role')" prepend-inner-icon="mdi-shape-outline" required />

       
          <v-card-actions class="justify-end">
            <v-btn color="secondary" @click="goBack"> Cancel </v-btn>
            <v-btn
              color="primary"
              :loading="loading"
              type="submit"
              class="ml-2"
            >
              Save user
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref, reactive , onMounted } from "vue";
import { useRouter } from "vue-router";
import { inject } from "vue";

import { validateMaxLength , validateEmail , validateRequired } from "@/utils/validationRules.js";
const permissions = JSON.parse(localStorage.getItem("permissions") || "[]");

const can = (permission) => {
  return permissions.includes(permission);
};
const api = inject("api");
const toast = inject("toast");
const router = useRouter();
const formRef = ref(null);

const rolepermission = ref([]);
const users = reactive({
  name: "abc",
 email:"abc@abc.com",
  password:"qwerty",
  role_id:"",
});
const loading = ref(false);

onMounted(async () => {

  if (!can("view user")) {
    toast.value.showToast("You are not authorized to view user", "error");
    return;
  }
  try {
    const res = await api.get("/rolepermission/index");
    rolepermission.value = res.data.data;
    console.log("Roles loaded:", rolepermission.value);
  } catch (error) {
    console.error("Error loading categories:", error);
  }
});

const handleSubmit = async () => {
  if (!can("create user")) {
    toast.value.showToast("You are not authorized to create user", "error");
    router.push("/users"); 
    return;
  } 
  const { valid } = await formRef.value.validate(); // ✅ validate all fields
  if (!valid) return;
  try {
    loading.value = true;

    const res = await api.post("/users/store", users);

    toast.value.showToast(res.data.message, "success");
    router.push("/users");
  } catch (error) {
    toast.value.showToast(
      error?.response?.data?.message || "Something went wrong!",
      "error"
    );
  } finally {
    loading.value = false;
  }
};
const goBack = () => {
  router.push("/users");
};
</script>
