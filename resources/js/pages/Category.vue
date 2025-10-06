<template>
  <v-card flat>
    <v-card-title class="d-flex align-center pe-2">
    Category
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
    </v-card-title>

 <v-btn color="primary" @click="goToAddCategory" prepend-icon="mdi-plus">
        Add New
      </v-btn>
    <v-divider></v-divider>
    <v-data-table
      v-model:search="search"
      :filter-keys="['name']"
       :headers="headers"
      :items="categoryItems"
    >
<template #item.sn="{ index }">
  {{ index + 1 }}
</template>

<template #item.name="{ item }">
  {{ item.name }}
</template>

<template #item.actions="{ item }">
  <v-btn icon color="primary" @click="editCategory(item.id)">
    <v-icon>mdi-pencil</v-icon>
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


const headers = [
    { title: "S.No", value: "sn" },
  { title: "Name", value: "name" },
  
  { title: "Actions", value: "actions", sortable: false },
];
const categoryItems = [
  { id: 1, name: "Science",  },
  { id: 2, name: "Math",  },
  { id: 3, name: "History",  },
];


const goToAddCategory = () => {
  router.push("/addCategory");
};

const editCategory = (id) => {
  router.push(`/editCategory/${id}`);
};
</script>


