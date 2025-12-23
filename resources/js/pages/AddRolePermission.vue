<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" md="8">
        <v-card elevation="6" class="pa-6">
          <!-- Header -->
          <v-card-title class="d-flex align-center">
            <v-icon class="mr-2">mdi-shield-key</v-icon>
            Add Role 
            <v-spacer />
            <v-btn variant="text" @click="goBack">
              <v-icon>mdi-arrow-left</v-icon>
              Back
            </v-btn>
          </v-card-title>

          <v-divider class="my-4" />

          <!-- Role Name -->
          <v-text-field
            v-model="form.name"
            label="Role Name"
            placeholder="Enter role name"
            variant="outlined"
            required
          />

          <!-- Permissions -->
          <div class="mt-6">
            <h4 class="mb-3">Assign Permissions</h4>
            <v-row>
              <v-col
                v-for="permission in allPermissions"
                :key="permission.name"
                cols="12"
                sm="6"
                md="4"
              >
                <v-checkbox
                  v-model="form.permissions"
                  :label="permission.name"
                  :value="permission.name"
                  density="compact"
                />
              </v-col>
            </v-row>
          </div>

          <!-- Actions -->
          <v-divider class="my-4" />

          <v-card-actions class="justify-end">
            <v-btn variant="outlined" @click="goBack">
              Cancel
            </v-btn>
            <v-btn
              color="primary"
              :loading="loading"
              @click="submit"
            >
              Save Role
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, reactive, onMounted, inject } from "vue";
import { useRouter } from "vue-router";

const permissions = JSON.parse(localStorage.getItem("permissions") || "[]");

const can = (permission) => {
  return permissions.includes(permission);
};

const api = inject("api");
const toast = inject("toast");
const router = useRouter();

const loading = ref(false);

const allPermissions = ref([]);

const form = reactive({
  name: "",
  permissions: [],
});

const fetchPermission = async () => {
    if (!can("view rolepermission")) {
    toast.value.showToast("You are not authorized to view rolepermission", "error");
    router.push("/rolepermission"); 
    return;
  } 
  try {
    const res = await api.get("/rolepermission/get-permissions");

console.log(res.data.data);
        // permissions.value = res.data.permissions ?? res.data;
    allPermissions.value = res.data.data;
  } catch (e) {
    console.error(e);
  }
};

const submit = async () => {
  
    if (!can("create rolepermission")) {
    toast.value.showToast("You are not authorized to create rolepermission", "error");
    router.push("/rolepermission"); 
    return;
  } 
  if (!form.name) {
    toast.value.showToast("Role name is required", "error");
    return;
  }

  loading.value = true;
  try {
    await api.post("/rolepermission/store", form);

    toast.value.showToast("Role created successfully", "success");
    router.push("/roles");
  } catch (e) {
    toast.value.showToast("Something went wrong", "error");
  } finally {
    loading.value = false;
  }
};



const goBack = () => {
  router.push("/rolepermission");
};

onMounted(fetchPermission);
</script>
