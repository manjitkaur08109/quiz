<template>
  <v-container class="py-8">
    <v-card class="mx-auto" max-width="500" elevation="8">
      <v-card-title class="text-h6">
        <v-icon icon="mdi-pencil-outline" class="mr-2" />
        Edit Category
      </v-card-title>

      <v-divider></v-divider>

      <v-card-text>
        <v-form @submit.prevent="handleSubmit">
          <v-text-field
            v-model="category.name"
            label="Category Name"
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
            <v-btn variant="outlined" color="grey" @click="goBack">
              Cancel
            </v-btn>
            <v-btn color="primary" type="submit" class="ml-2">
              Update Category
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup>
import { reactive, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

const router = useRouter();
const route = useRoute();
const categoryId = route.params.id;

const category = reactive({
  name: "",
  description: "",
});

onMounted(() => {
  const dummyCategories = [
    { id: 1, name: "Science", description: "Science related quiz" },
    { id: 2, name: "Math", description: "Math related quiz" },
    { id: 3, name: "History", description: "History topics" },
  ];

  const existing = dummyCategories.find((c) => c.id == categoryId);
  if (existing) {
    category.name = existing.name;
    category.description = existing.description;
  }
});

const handleSubmit = () => {
  console.log("Category Updated:", category);
  alert("Category Updated Successfully!");
  router.push("/category");
};

const goBack = () => {
  router.push("/category");
};
</script>
