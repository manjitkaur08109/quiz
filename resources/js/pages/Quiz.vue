<template>
  <v-card flat>
    <v-card-title class="d-flex align-center pe-2">
      Quiz List
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
      :items="quizzes"
    >
     <template #item.sn="{ index }">
        {{ index + 1 }}
      </template>

      <template #item.category="{ item }">
        {{ item.category.title }}
        </template>
      <template #item.actions="{ item }">
        <v-btn size="x-small" icon color="primary" @click="editQuiz(item.id)">
          <v-icon >mdi-pencil</v-icon>
        </v-btn>
         <v-btn size="x-small" class="ml-2" icon color="red" @click="deleteQuiz(item.id)">
          <v-icon>mdi-delete</v-icon>
        </v-btn>
      </template>
    </v-data-table>
  </v-card>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();
const search = ref("");
const quizzes = ref([]);
const loading = ref(false);

const headers = [
  { title: "S.No", key: "sn" },
  { title: "Quiz Title", key: "title" },
  { title: "Description", value: "description" },
  { title: "Category", key: "category" },
  { title: "Actions", key: "actions", sortable: false },
];

const fetchQuizzes = async () => {
  loading.value = true;
  try {
    const res = await axios.get("/api/quiz/index");

    quizzes.value = res.data.data;
  } finally {
    loading.value = false;
  }
};

const deleteQuiz = async (id) => {
  if (!confirm("Delete this quiz?")) return;
  try {
    await axios.delete(`/api/quiz/delete/${id}`);
    fetchQuizzes();
  } catch (error) {
    console.error("Error deleting quiz:", error);
  }
};
onMounted(() => {
  fetchQuizzes();
});

const goToAddQuiz = () => router.push("/addQuiz");
const editQuiz = (id) => router.push(`/editQuiz/${id}`);


</script>

