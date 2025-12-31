<template>
  <v-container class="d-flex align-center justify-center fill-height">
    <v-card elevation="10" class="pa-6" min-width="450">
      <v-card-title class="text-h5 text-center mb-4">
        <v-icon size="x-small" icon="mdi-login" class="mr-2" />
        Login
      </v-card-title>

      <v-form ref="formRef" @submit.prevent="handleLogin">
        <v-text-field
          v-model="credentials.email"
          label="Email Address"
          prepend-inner-icon="mdi-email"
          type="email"
          :rules="emailRules"
          required
        />

        <v-text-field
          v-model="credentials.password"
          label="Password"
          prepend-inner-icon="mdi-lock"
          type="password"
          :rules="passwordRules"
          required
        />

        <v-btn
          color="primary"
          type="submit"
          :loading="loading"
          block
          class="mt-4"
        >
          Login
        </v-btn>

        <v-btn color="success" block class="mt-2" @click="goToRegister">
          Register
        </v-btn>
      </v-form>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref, reactive } from "vue";
import { useRouter } from "vue-router";
import { inject } from "vue";
const api = inject("api");

const toast = inject("toast");
const router = useRouter();
const formRef = ref(null);
const loading = ref(false);

const credentials = reactive({
  email: "",
  password: "",
});

const emailRules = [
  (v) => !!v || "Email is required",
  (v) => /.+@.+\..+/.test(v) || "Email must be valid",
];
const passwordRules = [
  (v) => !!v || "Password is required",
  (v) => v.length >= 6 || "Password must be at least 6 characters",
];

const handleLogin = async () => {
  const { valid } = await formRef.value.validate();
  if (!valid) return;

  try {
    loading.value = true;
    const res = await api.post("/login", credentials);
    localStorage.setItem("user", JSON.stringify(res.data.data.user));
    localStorage.setItem("token", res.data.data.token);

    localStorage.setItem(
      "permissions",
      JSON.stringify(res.data.data.permissions || [])
    );

    // Reconnect Echo with new token after login
    if (window.reconnectEcho) {
      window.reconnectEcho();
    }

    router.push("/");
  } catch (error) {
    toast.value.showToast(
      error?.response?.data?.message || "Something went wrong!",
      "error"
    );
  } finally {
    loading.value = false;
  }
};

const goToRegister = () => {
  router.push("/register");
};
</script>
