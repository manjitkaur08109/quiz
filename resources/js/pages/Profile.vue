<template>
  <!-- <v-card v-if="loaded" class="mx-auto" min-width="500" elevation="8"> -->
  <v-tabs v-model="tab" align-tabs="center" color="deep-purple-accent-4">
    <v-tab :value="1">Overview</v-tab>
    <v-tab :value="2">Change password</v-tab>
  </v-tabs>

  <v-tabs-window v-model="tab">
    <v-tabs-window-item :value="1">
      <v-container class="py-8">
        <v-card class="mx-auto" max-width="500" elevation="8">
          <!-- <v-row>
            <v-col cols="12"> -->
          <v-form ref="profileForm" fast-fail @submit.prevent="updateProfile">
            <!-- <v-form fast-fail @submit.prevent> -->
            <v-text-field
              class="mt-2"
              v-model="Name"
              :rules="NameRules"
              label="Name"
              prepend-inner-icon="mdi-account"
              required
            ></v-text-field>

            <v-text-field
              class="mt-2"
              v-model="email"
              :rules="emailRules"
              label="Email"
              prepend-inner-icon="mdi-email"
               readonly
            ></v-text-field>

            <v-text-field
              class="mt-2"
              v-model="phoneNo"
              :rules="phoneNoRules"
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
          <!-- <v-row>
                      <v-col cols="12"> -->
          <v-form ref="passwordForm" fast-fail @submit.prevent="changePassword">
            <v-text-field
              class="mt-2"
              v-model="password"
              :v-rules="passwordRules"
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
              :v-rules="confirmpasswordRules"
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
          <!-- </v-col>
          </v-row> -->
        </v-card>
      </v-container>
    </v-tabs-window-item>
  </v-tabs-window>
  <!-- </v-card> -->
</template>
<script setup>
import { ref, inject, onMounted, onBeforeUnmount } from "vue";
import axios from "axios";

const showPassword = ref(false);
const showConfirmPassword = ref(false);
const togglePassword = (e) => {
  e.stopPropagation(); // 👈 prevent extra bubbling
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
const passwordRules = [
  (value) => {
    if (value?.length >= 6) return true;
    return "Password must be at least 6 characters.";
  },
];

const confirmpasswordRules = [
  (value) => {
    if (value == password.value) return true;
    return "Confirm password mismatch.";
  },
];
const NameRules = [
  (value) => {
    if (value?.length >= 3) return true;
    return " Name must be at least 3 characters.";
  },
];

const emailRules = [
  (value) => {
    if (!value) return ;
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return pattern.test(value) || "Invalid email address.";
  },
];
const phoneNoRules = [
  (value) => !!value || "Phone number is required.",
  (value) => /^[0-9]{10,15}$/.test(value) || "Enter a valid phone number.",
];
const loaded = ref(false);
// ✅ Fetch logged-in user info
onMounted(async () => {
  try {
    const token = localStorage.getItem("token");
    const res = await axios.get("http://127.0.0.1:8000/api/profile", {
      headers: { Authorization: `Bearer ${token}` },
    });
    console.log("Profile response:", res.data); // 👈 check structure
    if (!isMounted) return;
    const data = res.data.data || res.data;
    Name.value = data.name;
    email.value = data.email;
    phoneNo.value = data.phone_no;

    loaded.value = true;
  } catch (error) {
    console.error("Failed to load profile", error);
  }
});

// 🧍 Update Profile Handler
const updateProfile = async () => {
  const { valid } = await profileForm.value.validate();
  if (!valid) return;

  try {
    const token = localStorage.getItem("token");
    await axios.put(
      "/api/profile",
      { name: Name.value, phone_no: phoneNo.value },
      { headers: { Authorization: `Bearer ${token}` } }
    );
    toast?.value?.showToast("Profile updated successfully!", "success");
  } catch (error) {
    toast?.value?.showToast(
      error?.response?.data?.message || "Failed to update profile",
      "error"
    );
  }
};

// 🔒 Change Password Handler
const changePassword = async () => {
  const { valid } = await passwordForm.value.validate();
  if (!valid) return;
  try {
    const token = localStorage.getItem("token");
    await axios.put(
      "/api/profile/password",
      {
        password: password.value,
        password_confirmation: confirmPassword.value,
      },
      { headers: { Authorization: `Bearer ${token}` } }
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
