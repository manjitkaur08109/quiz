<template>
  <v-container class="py-8">
    <v-card class="mx-auto" max-width="500" elevation="8">
      <v-card-title class="text-h6">
        <v-icon size="x-small" icon="mdi-plus-circle-outline" class="mr-2" />
        Add New Category
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
            <v-btn  color="secondary" @click="goBack">
              Cancel
            </v-btn>
            <v-btn color="primary" type="submit" class="ml-2">
              Save Category
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref,reactive } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
const router = useRouter();
const formRef = ref(null);
const category = reactive({
  title: "",
  description: "",
});
const loading = ref(false);
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

const handleSubmit = async () => {
    const { valid } = await formRef.value.validate(); // ✅ validate all fields
  if (!valid) return; // stop if invalid
  try {
    loading.value = true;

    const token = localStorage.getItem("token");
    const res = await axios.post("/api/category/store", category,{
      headers: { Authorization: `Bearer ${token}` },
    });

    category.value = res.data.data;
    alert(" Category Added Successfully!");
    console.log("Saved:", res.data);

    category.title = "";
    category.description = "";

    router.push("/category");
  } catch (error) {
    alert("title name already exists")
    console.error(error.response?.data);
  } finally {
    loading.value = false;
  }
};
const goBack = () => {
  router.push("/category");
};

</script>
