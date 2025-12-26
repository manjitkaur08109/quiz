<template>
  <v-tabs v-model="tab" align-tabs="center" color="deep-purple-accent-4">
    <v-tab :value="1">Overview</v-tab>
    <v-tab :value="2">Change password</v-tab>
  </v-tabs>

  <v-tabs-window v-model="tab">
    <v-tabs-window-item :value="1">
      <v-container class="py-8">
        <v-card class="mx-auto" max-width="500" elevation="8">
          <v-form ref="profileForm" fast-fail @submit.prevent="updateProfile">
            <v-text-field
              class="mt-2"
              v-model="Name"
              :rules="validateRequired('Name')"
              label="Name"
              prepend-inner-icon="mdi-account"
              required
            ></v-text-field>

            <v-text-field
              class="mt-2"
              v-model="email"
              :rules="validateEmail()"
              label="Email"
              prepend-inner-icon="mdi-email"
               readonly
            ></v-text-field>

            <v-text-field
              class="mt-2"
              v-model="phoneNo"
              :rules="validatePhoneNo()"
              label="Phone No"
              prepend-inner-icon="mdi-phone"
              required
            ></v-text-field>
            <v-btn class="mt-4" color="info" type="submit" block
              >Update Profile</v-btn
            >
          </v-form>
        </v-card>
      </v-container>
    </v-tabs-window-item>
    <v-tabs-window-item :value="2">
      <v-container class="py-8">
        <v-card class="mx-auto" max-width="500" elevation="8">
          <v-form ref="passwordForm" fast-fail @submit.prevent="changePassword">
            <v-text-field
              class="mt-2"
              v-model="password"
              :rules="validatePassword()"
              label="New password"
              prepend-inner-icon="mdi-lock-outline"
              :append-inner-icon="
                showPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'
              "
              :type="showPassword ? 'text' : 'password'"
              @click:append-inner="showPassword = !showPassword"
              required
            ></v-text-field>

            <v-text-field
              class="mt-2"
              v-model="confirmPassword"
              :rules="validateConfirmPassword(password)"
              label="Confirm password"
              prepend-inner-icon="mdi-lock-check"

              :append-inner-icon="
                showConfirmPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'
              "
              :type="showConfirmPassword ? 'text' : 'password'"
              @click:append-inner="showConfirmPassword = !showConfirmPassword"
              required
            ></v-text-field>
            <v-btn class="mt-4" color="info" type="submit" block
              >Change Password</v-btn
            >
          </v-form>
        </v-card>
      </v-container>
    </v-tabs-window-item>
  </v-tabs-window>
</template>
<script setup>
import { ref, inject, onMounted, onBeforeUnmount } from "vue";
const api = inject("api");
const showPassword = ref(false);
const showConfirmPassword = ref(false);
const togglePassword = (e) => {
  e.stopPropagation();
  showPassword.value = !showPassword.value;
};
let isMounted = true;
onBeforeUnmount(() => {
  isMounted = false;
});
const toast = inject("toast");
const tab = ref(1);
const password = ref("");
const confirmPassword = ref("");
const passwordForm = ref(null);
const Name = ref("");
const email = ref("");
const phoneNo = ref("");
const profileForm = ref(null);

import {
  validateRequired,
  validateEmail,
  validatePhoneNo,
  validatePassword,
  validateConfirmPassword,
} from "@/utils/validationRules";

const loaded = ref(false);
onMounted(async () => {
  try {
    const res = await api.get("/profile");

    const data = res.data.data || res.data;
    Name.value = data.name;
    email.value = data.email;
    phoneNo.value = data.phone_no;

    loaded.value = true;
  } catch (error) {
    console.error("Failed to load profile", error);
  }
});

const updateProfile = async () => {
  const { valid } = await profileForm.value.validate();
  if (!valid) return;

  try {
    await api.put(
      "/profile",
      { name: Name.value, phone_no: phoneNo.value }
    );
    toast?.value?.showToast("Profile updated successfully!", "success");
  } catch (error) {
    toast?.value?.showToast(
      error?.response?.data?.message || "Failed to update profile",
      "error"
    );
  }
};

const changePassword = async () => {
  const { valid } = await passwordForm.value.validate();
  if (!valid) return;
  try {
    await api.put(
      "/profile/password",
      {
        password: password.value,
        password_confirmation: confirmPassword.value,
      }
    );
    toast?.value?.showToast("Password changed successfully!", "success");
    password.value = "";
    confirmPassword.value = "";
  } catch (error) {
    toast?.value?.showToast(
      error?.response?.data?.message || "Failed to change password",
      "error"
    );
  }
};
</script>
