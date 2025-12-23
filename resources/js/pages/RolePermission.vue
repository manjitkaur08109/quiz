<template>
    <v-card>
        <v-card-title class="d-flex align-center pe-2">Roles
            <v-spacer></v-spacer>
            <v-btn class="ml-5" color="primary" @click="goToAddRolePermission" prepend-icon="mdi-plus">
            </v-btn>
        </v-card-title>
    </v-card>
    <v-divider></v-divider>

    <v-row class="mt-2">
        <v-col v-for="role in roles" :key="role.id" cols="12" sm="6" md="4">


            <v-card elevation="4" class="pa-4">
                <v-chip color="deep-purple-lighten-4" text-color="deep-purple" size="small" class="mb-3">
                    <!-- {{ role.users_count }} <span v-if="role.users_count > 1"></span> -->
                </v-chip>

                <div class="d-flex align-center">
                    <div class="text-subtitle-1 font-weight-medium">
                        {{ role.name }}
                    </div>

                    <v-spacer />

                    <v-btn icon size="small" variant="text">
                        <v-icon v-if="can('edit rolepermission')" size="18" @click="goToEditRolePermission(role.id)">mdi-pencil-outline</v-icon>
                    </v-btn>

                    <v-btn v-if="can('view rolepermission')" icon size="small" variant="text">
                        <v-icon size="18" @click="copyRole(role)" > mdi-content-copy</v-icon>
                    </v-btn>

                    <v-btn v-if="can('delete rolepermission')" icon size="small" variant="text" color="red">
                        <v-icon size="18" @click="deleteRole(role.id)">mdi-delete-outline</v-icon>
                    </v-btn>
                </div>
            </v-card>
        </v-col>

    </v-row>

</template>
<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from "vue-router";
import { inject } from "vue";

const permissions = JSON.parse(localStorage.getItem("permissions") || "[]");

const can = (permission) => {
  return permissions.includes(permission);
};
const api = inject("api");
const toast = inject("toast");
const router = useRouter();
const search = ref("");

const goToAddRolePermission = () => {
    router.push('/addrolepermission');
};

const goToEditRolePermission =(id) => {
    router.push('/editrolepermission/'+ id);
};

const headers = [
    { text: "Name", value: "name" },
    { text: "Permissions", value: "permissions" },
    { text: "Actions", value: "actions" },
]

const user = reactive({
    user: null,
    permissions: [],
});
const roles = ref([]);

const fetchRoles = async () => {
      if (!can("view rolepermission")) {
    toast.value.showToast("You are not authorized to view rolepermission", "error");
    router.push("/rolepermission"); 
    return;
  }
    const res = await api.get('/rolepermission/index');
    console.log(res.data);
    roles.value = res.data.data;

}

const deleteRole = async (id) => {
      if (!can("delete rolepermission")) {
    toast.value.showToast("You are not authorized to delete rolepermission", "error");
    router.push("/rolepermission"); 
    return;
  }
    if (!confirm("Delete this role?")) return;
    try {
        await api.delete(`/rolepermission/delete/${id}`);

        toast.value.showToast("Role deleted successfully", 'success');
        fetchRoles();
    } catch (error) {
        console.error("Error deleting role:", error.response?.data || error);
    }
};

const copyRole = async (role) => {
      if (!can("view rolepermission")) {
    toast.value.showToast("You are not authorized to view rolepermission", "error");
    router.push("/rolepermission"); 
    return;
  }
    const newName = prompt(
        "Enter new role name",
        `${role.name}_copy`
    );

    if (!newName) return;

    try {
        await api.post("/rolepermission/copy", {
            role_id: role.id,
            name: newName,
        });

        toast.value.showToast("Role copied successfully", "success");
        fetchRoles();
    } catch (error) {
        toast.value.showToast("Failed to copy role", "error");
        console.error(error);
    }
};


onMounted(() => {
    fetchRoles();
});


</script>