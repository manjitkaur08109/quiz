<template>
  <v-container class="py-8">
    <v-card class="mx-auto" max-width="500" elevation="8">
      <v-card-title class="text-h6">
        <v-icon icon="mdi-plus-circle-outline" class="mr-2" />
        Add New Category
      </v-card-title>

      <v-divider></v-divider>

      <v-card-text>
        <v-form @submit.prevent="handleSubmit">
          <v-text-field
            v-model="category.title"
            label="Category Title"
            prepend-inner-icon="mdi-shape-outline"
            required
          />

          <v-textarea
            v-model="category.description"
            label="Category Description"
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

const category = reactive({
  title: "",
  description: "",
});
const loading = ref(false);

const handleSubmit = async () => {
  try {
    loading.value = true;

    const res = await axios.post("/api/category/store", category);

    alert(" Category Added Successfully!");
    console.log("Saved:", res.data);

    category.title = "";
    category.description = "";

    router.push("/category");
  } catch (error) {
    console.error(error.response?.data);
  } finally {
    loading.value = false;
  }
};
const goBack = () => {
  router.push("/category");
};
</script>
