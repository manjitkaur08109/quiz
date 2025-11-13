<template>
  <v-container>
    <v-card>
      <v-form ref="formRef" @submit.prevent="submit">
        <v-card-title>
          <v-icon size="x-small">mdi-pencil</v-icon>
          Edit Product
        </v-card-title>
        <v-divider></v-divider>
        <v-card-text>
          <v-text-field
            v-model="product.title"
            label="Product Title"
            prepend-inner-icon="mdi-format-title"
            :rules="ProductTitleRules"
            required
          >
          </v-text-field>
          <v-textarea
            v-model="product.description"
            label="Product Description"
            prepend-inner-icon="mdi-text-box"
            :rules="ProductDescriptionRules"
            rows="3"
            auto-grow
            required
          ></v-textarea>
          <v-select
            v-model="product.category_id"
            label="Select Category"
            :items="categories"
            item-title="title"
            item-value="id"
            prepend-inner-icon="mdi-shape"
            :rules="SCRules"
            required
          ></v-select>
          <v-text-field
            v-model="product.price"
            label="Product Price"
            prepend-inner-icon="mdi-price"
            :rules="ProductPriceRules"
            required
          ></v-text-field>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="gray" @click="back">cancel</v-btn>
          <v-btn color="primary" class="ml-2" type="submit">update</v-btn>
        </v-card-actions>
      </v-form>
    </v-card>
  </v-container>
</template>
<script setup>
import axios from "axios";
import { inject, onMounted, ref } from "vue";
import { useRoute, useRouter } from "vue-router";

const api = inject("api");
const route = useRoute();
const toast = inject("toast");
const router = useRouter();
const categories = ref([]);
const product = ref({
  title: "",
  description: "",
  category_id: "",
  price:""
});
const ProductTitleRules = [
  (value) => {
    if (value?.length >= 3)
      return true;
    else
    return "Title required|string";
  },
];
const ProductPriceRules = [
  (value) => {
    if (value)
    return true;
    return "Price is required|number";
  },
];
const ProductDescriptionRules = [
  (value) => {
    if (value?.length >= 5)
      return true;
    return "nullable|string|max:5";
  },
];
const SCRules = [
  (value) => {
    if (value)
      return true;
    return "category required";
  },
];


const back = () => {
  router.push("/products");
};
const productId = route.params.id;
onMounted(async () => {
  try {
    const response = await api.get(`/products/show/${productId}`);
    toast.value.showToast("Product show", "success");
    product.value = response.data.data;
  } catch (error) {
    toast.value.showToast(
      error?.response?.data?.message || "Something went wrong!",
      "error"
    );
  }
});

const formRef = ref(null);
const submit = async () => {
  const { valid } = await formRef.value.validate();
  if (!valid) return;
  try {
    const response = await api.put(`/products/update/${productId}`, product.value);
    product.value = response.data.data;

    toast.value.showToast(response.data.message, "success");
    back();
  } catch (error) {
    toast.value.showToast(
      error?.response?.data?.message || "Something went wrong!",
      "error"
    );
  }
};

onMounted(async () => {
  try {
    const response = await api.get("/category/index");
    categories.value = response.data.data;
    toast.value.showToast("categories loaded ", categories.value);
  } catch (error) {
    console.log("Error loaded categories");
  }
});
</script>
