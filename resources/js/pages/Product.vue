<template>
  <v-card flat>
    <v-card-title class="d-flex align-center pe-2">
      Products
      <v-spacer></v-spacer>
      <v-text-field
        v-model="search"
        density="compact"
        label="Search"
        prepend-inner-icon="mdi-magnify"
        variant="solo-filled"
        flat
        hide-details
        single-line
      >
      </v-text-field>
      <v-btn prepend-icon="mdi-plus" color="primary" @click="goToAddProduct" >Add New</v-btn>
    </v-card-title>
    <v-divider></v-divider>
    <v-data-table
      v-model:search="search"
      :headers="headers"
      :items="productitems"
    >
      <template #item.sn="{ index }">
        {{ index + 1 }}
      </template>
      <template #item.category_id="{ item }">
        {{ item?.category?.title }}
      </template>

      <template #item.actions="{ item }">
        <v-btn icon color="success" size="x-small" @click="editProduct(item.id)" >
          <v-icon>mdi-pencil</v-icon>
        </v-btn>
        <v-btn icon @click="deleteProduct(item.id)" color="red" class="ml-2" size="x-small"
          ><v-icon>mdi-delete</v-icon></v-btn
        >
      </template>
    </v-data-table>
  </v-card>
</template>
<script setup>
import axios from "axios";
import { inject, onMounted, ref } from "vue";
import { useRouter } from "vue-router";
const router = useRouter();

const toast = inject("toast");
const productitems = ref([
  {
    title: "Apple",
    description: "apple description",
    category: { title: "fruit" },
    price: 50,
  },
  {
    title: "Apple",
    description: "apple description",
    category: { title: "fruit" },
    price: 50,
  },
]);
const headers = [
  { title: "S No", value: "sn" },
  { title: "Title", value: "title" },
  { title: "Description", value: "description" },
  { title: "Category", value: "category_id" },
  { title: "Price", value: "price" },
  { title: "Actions", value: "actions" },
];

onMounted(async () => {
  try {
    const token = localStorage.getItem("token");
    const res = await axios.get("/api/products/index", {
      headers: { Authorization: `Bearer ${token}` },
    });
    // toast.value.showToast("Product fetch successfully ");
    productitems.value = res.data.data;
  } catch (error){
    console.log(error.response?.message);
    toast.value.showToast("Something went wrong",'error');
  }
});

const deleteProduct = async (id) => {
  try {
    const token = localStorage.getItem("token");
    const res = await axios.delete("/api/products/delete/"+id, {
      headers: { Authorization: `Bearer ${token}` },
    });
    toast.value.showToast(res.data.message,"success")
    productitems.value = res.data.data;
  } catch (error) {
     console.log(error.response?.message);
    toast.value.showToast("Something went wrong", "error");
  }
};
  const goToAddProduct =()=>{
    router.push('/addProduct')
    };

    const editProduct =(id)=>{
        router.push(`/editProduct/${id}`)
    };
</script>
