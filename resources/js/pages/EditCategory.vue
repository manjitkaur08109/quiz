<template>
  <v-container class="py-8">
    <v-card class="mx-auto" max-width="500" elevation="8">
      <v-card-title class="text-h6">
        <v-icon size="x-small" icon="mdi-pencil-outline" class="mr-2" />
        Edit Category
      </v-card-title>

      <v-divider></v-divider>

      <v-card-text>
        <v-form ref="formRef" @submit.prevent="handleSubmit">
          <v-text-field
            v-model="category.title"
            label="Category Title"
            :rules="CategoryTitleRules"
            prepend-inner-icon="mdi-shape-outline"
            required
          />

          <v-textarea
            v-model="category.description"
            label="Category Description"
            :rules="CategoryDescriptionRules"
            prepend-inner-icon="mdi-text-box-outline"
            rows="3"
            auto-grow
          />

          <v-card-actions class="justify-end">
            <v-btn color="grey" @click="goBack">
              Cancel
            </v-btn>
            <v-btn
              color="primary"
              type="submit"
              class="ml-2"
              :loading="loading"
            >
              Update Category
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup>
import { reactive, ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();
const route = useRoute();
const categoryId = route.params.id;

const category = reactive({
  title: "",
  description: "",
});
const loading = ref(false);

const formRef = ref("");
const CategoryTitleRules = [
    (value) => {
    if (value?.length >= 3) return true;
    return ' Title required|string|max:15';
  },
];
const CategoryDescriptionRules = [
  (value) => {
    if (value?.length >= 10) return true;
    return "Description must be at least 10 characters.";
  },
];

onMounted(async () => {
  try {
    const token = localStorage.getItem("token");
    const res = await axios.get(`/api/category/show/${categoryId}`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    category.value = res.data.data;
    const data = res.data.data;
    category.title = data.title;
    category.description = data.description ;
  }
catch (error) {
    if(error?.response?.status == 401){
     localStorage.removeItem("token");
  localStorage.removeItem("user");
      router.push("/login");
    }
        if(error?.response?.status == 409){
     alert(error?.response?.message);
    }
    console.error("Error loading category:", error.response?.data || error);
     alert("Something went wrong!");
  }
});

const handleSubmit = async () => {
    const { valid } = await formRef.value.validate(); // ✅ validate all fields
  if (!valid) return; // stop if invalid
  loading.value = true;
  try {
        const token = localStorage.getItem("token");
    const res = await axios.put(`/api/category/update/${categoryId}`, category,{
        headers:{Authorization:`Bearer ${token}`},
    });
    alert(res.data.message || "Category Updated Successfully!");
    router.push("/category");
  }
  catch (error) {
    if(error?.response?.status == 401){
     localStorage.removeItem("token");
  localStorage.removeItem("user");
      router.push("/login");
    }
        if(error?.response?.status == 409){
     alert(error?.response?.message);
    }
    console.error( error.response?.data || error);
     alert("Something went wrong!");
  } finally {
    loading.value = false;
  }
};

const goBack = () => router.push("/category");

</script>
