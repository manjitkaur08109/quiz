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
            :rules="validateMaxLength('Category Title', 100)"
            prepend-inner-icon="mdi-shape-outline"
            required
          />

          <v-textarea
            v-model="category.description"
            label="Category Description"
            :rules="validateMaxLength('Category Description', 200)"
            prepend-inner-icon="mdi-text-box-outline"
            rows="3"
            auto-grow
          />

          <v-card-actions class="justify-end">
            <v-btn color="grey" @click="goBack"> Cancel </v-btn>
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
import { inject } from "vue";

import {
    validateMaxLength
} from "@/utils/validationRules.js";
const api = inject("api");
const toast = inject("toast");
const router = useRouter();
const route = useRoute();
const categoryId = route.params.id;

const category = ref({
  title: "",
  description: "",
});
const loading = ref(false);

const formRef = ref("");

onMounted(async () => {
  try {
    const res = await api.get(`/category/show/${categoryId}`);
    const data = res.data.data;
    category.value.title = data.title;
    category.value.description = data.description;
  } catch (error) {

    toast.value.showToast(
      error?.response?.data?.message || "Something went wrong!",
      "error"
    );
  }
});

const handleSubmit = async () => {
  const { valid } = await formRef.value.validate(); // ✅ validate all fields
  if (!valid) return; // stop if invalid
  loading.value = true;
  try {
    const res = await api.put(`/category/update/${categoryId}`, category.value);

    toast.value.showToast(res?.data?.message || "Category updated!", "success");
    router.push("/category");
  } catch (error) {

    toast.value.showToast(
      error?.response?.data?.message || "Something went wrong!",
      "error"
    );
  } finally {
    loading.value = false;
  }
};

const goBack = () => router.push("/category");
</script>
