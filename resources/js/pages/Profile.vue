<template>
  <v-card class="mx-auto" max-width="500" elevation="8">
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
                <v-text-field class="mt-2"
                variant="outlined"
                  v-model="Name"
                  :rules="NameRules"
                  label="Name"
                   prepend-inner-icon="mdi-account"
                   required
                ></v-text-field>

                <v-text-field class="mt-2"
                variant="outlined"
                  v-model="email"
                  :rules="emailRules"
                  label="Email"
                   prepend-inner-icon="mdi-email"
                   required
                ></v-text-field>

                <v-text-field class="mt-2"
                variant="outlined"
                  v-model="phoneNo"
                  :rules="phoneNoRules"
                  label="Phone No"
                  prepend-inner-icon="mdi-phone"
                  required
                ></v-text-field>
                  <v-btn class="mt-4" color="info" type="submit" block>Update Profile</v-btn>
              </v-form>
          <!-- <v-btn class="mt-2" type="submit" color="info">Submit</v-btn> -->
          </v-card>
        </v-container>
      </v-tabs-window-item>
      <v-tabs-window-item :value="2">
        <v-container class="py-8">
    <v-card class="mx-auto" max-width="500" elevation="8">
          <!-- <v-row>
            <v-col cols="12"> -->
                <v-form ref="passwordForm" fast-fail @submit.prevent="changePassword">
                <v-text-field class="mt-2"
                variant="outlined"
                  v-model="password"
                  :rules="passwordRules"
                  label="New password"
                    prepend-inner-icon="mdi-lock-outline"
                     type="password"
              required
                ></v-text-field>

                <v-text-field class="mt-2"
                variant="outlined"
                  v-model="confirmPassword"
                  :v-rules="confirmpasswordRules"
                  label="Confirm password"
                   prepend-inner-icon="mdi-lock-check"
                    type="password"
              required
                ></v-text-field>
                 <v-btn class="mt-4" color="info" type="submit" block>Change Password</v-btn>
              </v-form>
            <!-- </v-col>
          </v-row> -->
          </v-card>
        </v-container>
      </v-tabs-window-item>
    </v-tabs-window>

  </v-card>
</template>
<script setup>
import { ref } from "vue";

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
    if (value?.length >= 8) return true;
    return "Password must be at least 8 characters.";
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
    if (!value) return "Email is required.";
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return pattern.test(value) || "Invalid email address.";
  },
];
const phoneNoRules = [
  (value) => !!value || "Phone number is required.",
  (value) => /^[0-9]{10,15}$/.test(value) || "Enter a valid phone number.",
];

// 🧍 Update Profile Handler
const updateProfile = async () => {
  const { valid } = await profileForm.value.validate();
  if (!valid) return;

  console.log("Updated Profile:", {
    name: Name.value,
    email: email.value,
    phone: phoneNo.value,
  });
};

// 🔒 Change Password Handler
const changePassword = async () => {
  const { valid } = await passwordForm.value.validate();
  if (!valid) return;

  console.log("Password changed:", password.value);
  password.value = "";
  confirmPassword.value = "";
};

</script>
