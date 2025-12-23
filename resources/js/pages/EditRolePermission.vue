<template>
    <v-container>
        <v-row justify="center">
            <v-col cols="12" md="8">
                <v-card elevation="6" class="pa-6">
                    <!-- Header -->
                    <v-card-title class="d-flex align-center">
                        <v-icon class="mr-2">mdi-shield-key</v-icon>
                        Edit Role
                        <v-spacer />
                        <v-btn variant="text" @click="goBack">
                            <v-icon>mdi-arrow-left</v-icon>
                            Back
                        </v-btn>
                    </v-card-title>

                    <v-divider class="my-4" />

                    <!-- Role Name -->
                    <v-text-field v-model="form.name" label="Role Name" variant="outlined" required />

                    <!-- Permissions -->
                    <div class="mt-6">
                        <h4 class="mb-3">Assign Permissions</h4>

                        <v-row>
                            <v-col v-for="permission in allPermissions" :key="permission.name" cols="12" sm="6" md="4">
                                <v-checkbox v-model="form.permissions" :label="permission.name" :value="permission.name"
                                    density="compact" />
                            </v-col>
                        </v-row>
                    </div>

                    <v-divider class="my-4" />

                    <!-- Actions -->
                    <v-card-actions class="justify-end">
                        <v-btn variant="outlined" @click="goBack">
                            Cancel
                        </v-btn>
                        <v-btn color="primary" :loading="loading" @click="updateRole">
                            Update Role
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>
<script setup>
import { ref, reactive, onMounted, inject } from "vue";
import { useRouter, useRoute } from "vue-router";

const permissions = JSON.parse(localStorage.getItem("permissions") || "[]");

const can = (permission) => {
  return permissions.includes(permission);
};

const api = inject("api");
const toast = inject("toast");
const router = useRouter();
const route = useRoute();

const loading = ref(false);
const allPermissions = ref([]);

const form = reactive({
    name: "",
    permissions: [],
});

const roleId = route.params.id;

/* Fetch ALL permissions */
const fetchPermissions = async () => {
    if (!can("view rolepermission")) {
    toast.value.showToast("You are not authorized to view rolepermission", "error");
    router.push("/rolepermission"); 
    return;
  } 
    const res = await api.get("/rolepermission/get-permissions");
    allPermissions.value = res.data.data;
};

/* Fetch ROLE details */
const fetchRole = async () => {
    if (!can("view rolepermission")) {
    toast.value.showToast("You are not authorized to view rolepermission", "error");
    router.push("/rolepermission"); 
    return;
  } 
    const res = await api.get(`/rolepermission/show/${roleId}`);

    form.name = res.data.data.name;

    // already assigned permissions
    form.permissions = res.data.data.permissions.map(p => p.name);
};

const updateRole = async () => {
    
 if (!can("edit rolepermission")) {
    toast.value.showToast("You are not authorized to edit rolepermission", "error");
    router.push("/rolepermission"); 
    return;
  } 
    loading.value = true;
    try {
        await api.put(`/rolepermission/update/${roleId}`, form);
        toast.value.showToast("Role updated successfully", "success");
        router.push("/rolepermission");
    } catch (e) {
        toast.value.showToast("Update failed", "error");
    } finally {
        loading.value = false;
    }
};

const goBack = () => router.push("/rolepermission");

onMounted(async () => {
    await fetchPermissions();
    await fetchRole();
});
</script>
