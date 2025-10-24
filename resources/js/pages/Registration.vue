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
          :rules="nameRules"
          required
        />

        <v-text-field
          v-model="user.email"
          label="Email Address"
          prepend-inner-icon="mdi-email"
          type="email"
          :rules="emailRules"
          required
        />

        <v-text-field
          v-model="user.password"
          label="Password"
          prepend-inner-icon="mdi-lock"
          type="password"
          :rules="passwordRules"
          required
        />

        <v-text-field
          v-model="user.password_confirmation"
          label="Confirm Password"
          prepend-inner-icon="mdi-lock-check"
          type="password"
          :rules="confirmPasswordRules"
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
import axios from "axios";
import { useRouter } from "vue-router";
axios.defaults.baseURL = "http://127.0.0.1:8000";
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

const nameRules = [
  (v) => !!v || "Name is required",
  (v) => v.length >= 3 || "Name must be at least 3 characters",
];
const emailRules = [
  (v) => !!v || "Email is required",
  (v) => /.+@.+\..+/.test(v) || "Email must be valid",
];
const passwordRules = [
  (v) => !!v || "Password is required",
  (v) => v.length >= 6 || "Password must be at least 6 characters",
];
const confirmPasswordRules = [
  (v) => !!v || "Confirm password is required",
  (v) => v === user.password || "Passwords do not match",
];

const handleRegister = async () => {
    Object.keys(errors).forEach(key => (errors[key] = []));
  const { valid } = await formRef.value.validate();
  if (!valid) return;

  try {

    loading.value = true;

    // 🔹 1. Register user
    const registerRes = await axios.post("/api/register", user);

    const token = registerRes.data.data.token;
    const userData = registerRes.data.data.user;

    // 🔹 2. Save token to localStorage (auto login)
    localStorage.setItem("token", token);
    localStorage.setItem("user", JSON.stringify(userData));

    // 🔹 3. Set Axios header for authenticated requests
    axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;

    alert("Registration successful! You are now logged in.");

    // 🔹 4. Redirect to dashboard (or home)
    router.push("/dashboard");
  } catch (err) {
    if (err.response && err.response.status === 422) {
      // Laravel validation errors
      const validationErrors = err.response.data.errors;
      Object.keys(validationErrors).forEach((key) => {
        errors[key] = validationErrors[key];
      });
    } else {
      alert(err.response?.data?.message || "Something went wrong!");
    }
  } finally {
    loading.value = false;
  }
};
//     loading.value = true;
//     const res = await axios.post("/api/register", user);
//     localStorage.setItem("token", res.data.data.token);

//     alert("Registration successful!");
//     console.log(res.data);
//     router.push("/login");
//   }
//   catch (err) {
//     if (err.response && err.response.status === 422) {
//       // Validation errors from Laravel
//       const validationErrors = err.response.data.errors;
//       Object.keys(validationErrors).forEach(key => {
//         errors[key] = validationErrors[key];
//       });
//     } else {
//       alert(err.response?.data?.message || "Something went wrong!");
//     }
// //   } catch (error) {
// //     console.error("Registration error:", error.response?.data || error);
// //     alert(error.response?.data?.message || "Something went wrong!");
//   } finally {
//     loading.value = false;
//   }
// };

const goToLogin = () => {
  router.push("/login");
};
</script>

<style scoped>
.v-card {
  border-radius: 16px;
}
</style>
