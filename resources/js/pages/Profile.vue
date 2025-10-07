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
              <v-form fast-fail @submit.prevent>
                <v-text-field class="mt-2"
                variant="outlined"
                  v-model="Name"
                  :rules="NameRules"
                  label="Name"
                ></v-text-field>

                <v-text-field class="mt-2"
                variant="outlined"
                  v-model="email"
                  :rules="emailRules"
                  label="Email"
                ></v-text-field>

                <v-text-field class="mt-2"
                variant="outlined"
                  v-model="phoneNo"
                  :rules="phoneNoRules"
                  label="Phone No"
                ></v-text-field>
              </v-form>
            <!-- </v-col>
          </v-row> -->
          <v-btn class="mt-2" type="submit" color="info">Submit</v-btn>
          </v-card>
        </v-container>
      </v-tabs-window-item>
      <v-tabs-window-item :value="2">
        <v-container class="py-8">
    <v-card class="mx-auto" max-width="500" elevation="8">
          <!-- <v-row>
            <v-col cols="12"> -->
              <v-form fast-fail @submit.prevent>
                <v-text-field class="mt-2"
                variant="outlined"
                  v-model="password"
                  :rules="passwordRules"
                  label="New password"
                ></v-text-field>

                <v-text-field class="mt-2"
                variant="outlined"
                  v-model="confirmPassword"
                  :rules="confirmpasswordRules"
                  label="Confirm password"
                ></v-text-field>

                <v-btn class="mt-2" type="submit"  color="info">Submit</v-btn>
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

const tab = ref(null);
const password = ref("");
const passwordRules = [
  (value) => {
    if (value?.length >= 8) return true;
    return "Password must be at least 8 characters.";
  },
];

const confirmPassword = ref("");
const confirmpasswordRules = [
  (value) => {
    if (value == password.value) return true;
    return "Confirm password mismatch.";
  },
];
const Name = ref("");
const NameRules = [
  (value) => {
    if (value?.length >= 3) return true;
    return " Name must be at least 3 characters.";
  },
];

const email = ref("");
const emailRules = [
  (value) => {
    if (/[^0-9]/.test(value)) return true;
    return "Last name can not contain digits.";
  },
];
const phoneNo = ref("");
const phoneNoRules = [
  (value) => {
    if (value?.length >= 10) return true;
    return "Phone no must be at least 10 characters.";
  },
];
</script>
