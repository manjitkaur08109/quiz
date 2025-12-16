<template>
  <v-container class="fill-height d-flex align-center justify-center">

    <v-card elevation="10" class="pa-6" min-width="500">
      <v-card-title class="text-h5 text-center mb-4">
        <v-icon size="x-small" icon="mdi-account-plus" class="mr-2" />
        Create Account
      </v-card-title>

      <v-form ref="formRef" @submit.prevent="handleRegister">
        <v-text-field
          v-model="user.name"
          label="Full Name"
          prepend-inner-icon="mdi-account"
          :rules="validateRequired('Name')"
          required
        />

        <v-text-field
          v-model="user.email"
          label="Email Address"
          prepend-inner-icon="mdi-email"
          type="email"
          :rules="validateEmail('Email')"
          required
        />

        <v-text-field
          v-model="user.password"
          label="Password"
          prepend-inner-icon="mdi-lock"
          type="password"
          :rules="validateRequired('Password')"
          required
        />

        <v-text-field
          v-model="user.password_confirmation"
          label="Confirm Password"
          prepend-inner-icon="mdi-lock-check"
          type="password"
          :rules="validateConfirmPassword('password_confirmation')"
          required
        />

        <v-btn
          :loading="loading"
          color="primary"
          type="submit"
          block
          class="mt-4"
        >
          Register
        </v-btn>

        <v-btn color="success" block class="mt-2" @click="goToLogin">
           Login
        </v-btn>
      </v-form>
    </v-card>

  </v-container>
</template>

<script setup>
import { ref, reactive } from "vue";
import { useRouter } from "vue-router";
import { inject } from "vue";
import { validateConfirmPassword, validateRequired, validateEmail } from "../utils/validationRules";
const api = inject("api");
const toast = inject("toast");
const router = useRouter();
const formRef = ref(null);
const loading = ref(false);

const user = reactive({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

const errors = reactive({
  name: [],
  email: [],
  password: [],
  password_confirmation: [],
});



const handleRegister = async () => {
    Object.keys(errors).forEach(key => (errors[key] = []));
  const { valid } = await formRef.value.validate();
  if (!valid) return;

  try {

    loading.value = true;

    const registerRes = await api.post("/register", user);

    const token = registerRes.data.data.token;
    const userData = registerRes.data.data.user;

    localStorage.setItem("token", token);
    localStorage.setItem("user", JSON.stringify(userData));

    api.defaults.headers.common["Authorization"] = `Bearer ${token}`;

     router.push('/');
  } catch (error) {
         toast.value.showToast(error?.response?.data?.message || "Something went wrong!",'error');

  } finally {
    loading.value = false;
  }
};

const goToLogin = () => {
  router.push("/login");
};
</script>

<style scoped>
.v-card {
  border-radius: 16px;
}
</style>
