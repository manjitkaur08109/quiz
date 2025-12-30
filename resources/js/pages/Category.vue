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
        @update:modelValue="onSearch"
        :hide-details="true"
        :single-line="true"
      />
      <v-btn
       v-if="can('create category')"
        class="ml-5"
        color="primary"
        @click="goToAddCategory"
        prepend-icon="mdi-plus"
      >
        Add New
      </v-btn>
    </v-card-title>

    <v-divider></v-divider>
    <v-data-table-server
      v-model:search="search"
      :filter-keys="['name']"
      :headers="headers"
      :items="categoryItems"
      :items-per-page-options="[5, 10, 25, 50]"
      :items-length="pagination.total"
      v-model:page="pagination.current_page"
      v-model:items-per-page="pagination.per_page"
      @update:page="onPageChange"
      @update:items-per-page="onPerPageChange"
    >
      <template #item.sn="{ index }">
        {{ index + 1 }}
      </template>
      <template #item.description="{ item }">
        <span v-html="item.description"></span>
      </template>

<template #item.actions="{ item }">
  <v-btn v-if="can('edit category')" size="x-small" icon color="primary" @click="editCategory(item.id)">
    <v-icon>mdi-pencil</v-icon>
  </v-btn>
  <v-btn v-if="can('delete category')" size="x-small" class="ml-2" icon color="red" @click="deleteCategory(item.id)">
    <v-icon>mdi-delete</v-icon>
  </v-btn>
</template>

    </v-data-table-server>
  </v-card>
</template>

<script setup>
import { ref, onMounted } from "vue";
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
const loading = ref(false);
const categoryItems = ref([]);

const headers = [
  { title: "S.No", value: "sn" },
  { title: "Title", value: "title" },
  { title: "Description", value: "description" },
  { title: "Actions", value: "actions", sortable: false },
];

const pagination = ref({
  current_page: 1,
  per_page: 5,
  total: 0,
});

const onPageChange = (page) => {
  pagination.value.current_page = page;
  fetchCategories();
};

const onPerPageChange = (perPage) => {
  pagination.value.per_page = perPage;
  pagination.value.current_page = 1;
  fetchCategories();
};

const onSearch = () => {
  pagination.value.current_page = 1;
  fetchCategories();
};
const goToAddCategory = () => {
  router.push("/addCategory");
};

const editCategory = (id) => {
  router.push(`/editCategory/${id}`);
};

const fetchCategories = async () => {
  if (!can("view category")) {
    toast.value.showToast("You are not authorized to view category", "error");
    router.push("/category"); 
    return;
  }
  loading.value = true;
  try {
    const res = await api.get(`/category/index?page=${pagination.value.current_page}&per_page=${pagination.value.per_page}&search=${search.value}`);
    categoryItems.value = res.data.data.data;

    pagination.value.current_page = res.data.data.current_page;
    pagination.value.per_page = res.data.data.per_page;
    pagination.value.total = res.data.data.total;

  } catch (error) {

    toast.value.showToast(error?.response?.data?.message || "Something went wrong!", 'error');

  }
   finally {
    loading.value = false;
  }
};

const deleteCategory = async (id) => {
   if (!can("delete category")) {
    toast.value.showToast("You are not authorized to delete category", "error");
    router.push("/category"); 
    return;
  }
  if (!confirm("Delete this category?")) return;
  try {
    await api.delete(`/category/delete/${id}`);

    toast.value.showToast("Category deleted successfully", 'success');
    fetchCategories();
  } catch (error) {
    console.error("Error deleting category:", error.response?.data || error);


     toast.value.showToast(error?.response?.data?.message || "Something went wrong!",'error');
  }
};

onMounted(() => {
   console.log('Injected permissions:', permissions);
  fetchCategories();
});

</script>



