<template>
  <v-container>
    <v-card>
      <v-form ref="formRef" @submit.prevent="submit">
        <v-card-title>
          <v-icon size="x-small">mdi-plus</v-icon>
          Add New Product
        </v-card-title>
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
            prepend-inner-icon="mdi-text-box-outline"
            :rules="ProductDescriptionRules"
            required
          >
          </v-textarea>
          <v-select
            v-model="product.category_id"
            :items="categories"
            item-title="title"
            item-value="id"
            label="Select Category"
            prepend-inner-icon="mdi-shape-outline"
            :rules="SCRules"
            required
          ></v-select>

          <v-text-field
            v-model="product.price"
            label="Price"
            prepend-inner-icon="mdi-price-outline"
            :rules="ProductPriceRules"
            required
          ></v-text-field>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn btn-color="gray" @click="back">Back</v-btn>
          <v-btn  type="submit" color="primary" class="ml-2" >Save </v-btn>
        </v-card-actions>
      </v-form>
    </v-card>
  </v-container>
</template>
<script setup>
import axios from "axios";
import { inject, onMounted, ref } from "vue";
import { useRouter } from "vue-router";

const toast = inject('toast');
const router = useRouter();
const product = ref({
  title: "",
  description: "",
  category_id: "",
  price: "",
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
const formRef = ref(null);
const submit = async () => {
    const {valid} = await formRef.value.validate();
    if (!valid) return;

    const token = localStorage.getItem('token');
    const response = await axios.post('/api/products/store',product.value,{
        headers:{ Authorization:`Bearer ${token}`}
    });
    toast.value.showToast(response.data.message, 'success');
    back();
};
const categories = ref([]);
onMounted (async()=>{
    try{
    const token = localStorage.getItem('token');
    const response = await axios.get('/api/category/index',{
        headers:{Authorization:`Bearer ${token}`}
    });
    categories.value = response.data.data;
}catch(error){
    console.log('Error loaded categories');
}
})
</script>
