<template>
  <v-card flat>
    <v-card-title class="d-flex align-center pe-2">
      Quiz
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
      ></v-text-field>
      <v-btn color="primary" @click="goToAddQuiz" prepend-icon="mdi-plus" class="ml-4 mb-3">
     Add New
   </v-btn>
    </v-card-title>
    <v-divider></v-divider>
    <v-data-table
      v-model:search="search"
       :headers="headers"
      :filter-keys="['name']"
      :items="items"
    >
     <template #item.sn="{ index }">
        {{ index + 1 }}
      </template>

      <template #item.actions="{ item }">
        <v-btn size="x-small" icon color="primary" @click="editQuiz(item.id)">
          <v-icon >mdi-pencil</v-icon>
        </v-btn>
      </template>




    </v-data-table>
  </v-card>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const search = ref("");
const goToAddQuiz = () => {
  router.push("/addQuiz");
};
const editQuiz = (id) => router.push(`/editQuiz/${id}`);
const items = [
  {
    name: "Nebula GTX 3080",
    image: "1.png",
    price: 699.99,
    rating: 5,
    stock: true,
  },
  {
    name: "Galaxy RTX 3080",
    image: "2.png",
    price: 799.99,
    rating: 4,
    stock: false,
  },

];
const headers = [
  { title: "S.No", key: "sn" },
  { title: "Quiz Name", key: "name" },
  { title: "Status", key: "stock" },
  { title: "Actions", key: "actions", sortable: false },
];
const item = [
  { id: 1, name: "Science Quiz", stock: true },
  { id: 2, name: "Math Quiz", stock: false },
  { id: 3, name: "History Quiz", stock: true },
];
</script>

