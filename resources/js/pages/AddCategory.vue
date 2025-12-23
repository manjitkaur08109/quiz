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
            :rules="validateMaxLength('Category', 20)"
            prepend-inner-icon="mdi-shape-outline"
            required
          />

            <!-- ⭐⭐ VUE EDITOR FOR DESCRIPTION ⭐⭐ -->
          <label class="mb-2 font-weight-medium">Category Description</label>
          <vue-editor v-model="category.description"></vue-editor>
          <!-- <v-textarea
            v-model="category.description"
            label="Category Description"
            :rules="validateMaxLength('Description', 200)"
            prepend-inner-icon="mdi-text-box-outline"
            rows="3"
            auto-grow
          /> -->

          <v-card-actions class="justify-end">
            <v-btn color="secondary" @click="goBack"> Cancel </v-btn>
            <v-btn
              color="primary"
              :loading="loading"
              type="submit"
              class="ml-2"
            >
              Save Category
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref, reactive } from "vue";
import { useRouter } from "vue-router";
import { inject } from "vue";

import { VueEditor } from "vue3-editor";
import { validateMaxLength } from "@/utils/validationRules.js";
const permissions = JSON.parse(localStorage.getItem("permissions") || "[]");

const can = (permission) => {
  return permissions.includes(permission);
};
const api = inject("api");
const toast = inject("toast");
const router = useRouter();
const formRef = ref(null);
const category = reactive({
  title: "",
  description: "",
});
const loading = ref(false);

const handleSubmit = async () => {
  if (!can("create category")) {
    toast.value.showToast("You are not authorized to create category", "error");
    router.push("/category"); 
    return;
  } 
  const { valid } = await formRef.value.validate(); // ✅ validate all fields
  if (!valid) return;
  try {
    loading.value = true;

    const res = await api.post("/category/store", category);

    toast.value.showToast(res.data.message, "success");
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
const goBack = () => {
  router.push("/category");
};
</script>
