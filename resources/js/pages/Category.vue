<template>
  <v-card flat>
    <v-card-title class="d-flex align-center pe-2">
      Categories
      <v-spacer></v-spacer>

      <v-text-field
        v-model="search"
        density="compact"
        label="Search"
        prepend-inner-icon="mdi-magnify"
        variant="solo-filled"
        :flat="true"
        :hide-details="true"
        :single-line="true"
      />
      <v-btn
        class="ml-5"
        color="primary"
        @click="goToAddCategory"
        prepend-icon="mdi-plus"
      >
        Add New
      </v-btn>
    </v-card-title>

    <v-divider></v-divider>
    <v-data-table
      v-model:search="search"
      :filter-keys="['name']"
      :headers="headers"
      :items="categoryItems"
    >
      <template #item.sn="{ index }">
        {{ index + 1 }}
      </template>



<template #item.actions="{ item }">
  <v-btn size="x-small" icon color="primary" @click="editCategory(item.id)">
    <v-icon>mdi-pencil</v-icon>
  </v-btn>
  <v-btn size="x-small" class="ml-2" icon color="red" @click="deleteCategory(item.id)">
    <v-icon>mdi-delete</v-icon>
  </v-btn>
</template>

    </v-data-table>
  </v-card>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { inject } from "vue";

const toast = inject("toast");
const router = useRouter();
const search = ref("");
const loading = ref(false);
const categoryItems = ref([]);

const headers = [
  { title: "S.No", value: "sn" },
  { title: "Title", value: "title" },
  { title: "Description", value: "description" },
  { title: "Actions", value: "actions", sortable: false },
];

const goToAddCategory = () => {
  router.push("/addCategory");
};

const editCategory = (id) => {
  router.push(`/editCategory/${id}`);
};
//


const fetchCategories = async () => {
  loading.value = true;
  try {
      const token = localStorage.getItem("token");
    const res = await axios.get("/api/category/index", {
      headers: { Authorization: `Bearer ${token}` },
    });
    categoryItems.value = res.data.data;
  }
  catch (error) {
    if(error?.response?.status == 401){
     localStorage.removeItem("token");
  localStorage.removeItem("user");
      router.push("/login");
    }
     toast.value.showToast(error?.response?.data?.message || "Something went wrong!",'error');

  }
   finally {
    loading.value = false;
  }
};

const deleteCategory = async (id) => {
  if (!confirm("Delete this category?")) return;
  try {
    const token = localStorage.getItem("token");
    await axios.delete(`/api/category/delete/${id}`, {
      headers: { Authorization: `Bearer ${token}` },
    });

     toast.value.showToast("Category deleted successfully",'success');
    fetchCategories();
  } catch (error) {
    console.error("Error deleting category:", error.response?.data || error);
    if (error?.response?.status === 401) {
      localStorage.removeItem("token");
      localStorage.removeItem("user");
      router.push("/login");
    }

     toast.value.showToast(error?.response?.data?.message || "Something went wrong!",'error');
  }
};

onMounted(() => {
  fetchCategories();
});

</script>



