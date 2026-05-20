<template>
    <v-container class="py-8">
        <v-card class="mx-auto" min-width="500" elevation="8">
            <v-card-title class="text-h6">
                <v-icon size="x-small" icon="mdi-plus-circle-outline" class="mr-2" />
                Edit New user
            </v-card-title>

            <v-divider></v-divider>

            <v-card-text>
                <v-form ref="formRef" @submit.prevent="handleSubmit">
                    <v-text-field v-model="users.name" label="User Name" :rules="validateMaxLength('users', 20)"
                        prepend-inner-icon="mdi-shape-outline" required />

                    <v-text-field v-model="users.email" label="Email" prepend-inner-icon="mdi-email" type="email"
                        :rules="validateEmail('Email')" required />



                    <v-text-field v-model="users.password" label="Password" prepend-inner-icon="mdi-lock"
                        type="password" :rules="validateRequired('Password')" required />

                    <v-select v-model="users.role_id" :items="rolepermission" item-title="name" item-value="id"
                        label="Select Role" :rules="validateRequired('Role')" prepend-inner-icon="mdi-shape-outline"
                        required />


                    <v-card-actions class="justify-end">
                        <v-btn color="secondary" @click="goBack"> Cancel </v-btn>
                        <v-btn color="primary" :loading="loading" type="submit" class="ml-2">
                            update user
                        </v-btn>
                    </v-card-actions>
                </v-form>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script setup>
import { ref, reactive, onMounted } from "vue";
import { useRouter } from "vue-router";
import { inject } from "vue";
import { useRoute } from "vue-router";
const route = useRoute();

import { validateMaxLength, validateEmail, validateRequired } from "@/utils/validationRules.js";
const permissions = JSON.parse(localStorage.getItem("permissions") || "[]");

const can = (permission) => {
    return permissions.includes(permission);
};
const api = inject("api");
const toast = inject("toast");
const router = useRouter();
const formRef = ref(null);
const userId = route.params.id;

const rolepermission = ref([]);
const users = reactive({
    name: "",
    email: "",
    password: "",
    role_id: "",
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

onMounted(async () => {
    if (!can("view user")) {
        toast.value.showToast("You are not authorized to view user", "error");
        router.push("/users");
        return;
    }
    try {
        const res = await api.get(`/users/show/${userId}`);
        const data = res.data.data;

        users.name = data.name;
        users.email = data.email;
        users.password = data.password;
        users.role_id = data.role_id;


    } catch (error) {

        toast.value.showToast(
            error?.response?.data?.message || "Something went wrong!",
            "error"
        );
    }
});


const handleSubmit = async () => {
    if (!can("edit user")) {
        toast.value.showToast("You are not authorized to edit user", "error");
        router.push("/users");
        return;
    }
    const { valid } = await formRef.value.validate();
    if (!valid) return;
    try {
        loading.value = true;

        const res = await api.put(`/users/update/${userId}`, users);

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
