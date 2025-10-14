<template>
  <v-container class="py-8">
    <v-card class="mx-auto" max-width="600" elevation="8">
      <v-card-title>
        <v-icon class="mr-2">mdi-pencil</v-icon>
        Edit Quiz
      </v-card-title>

      <v-divider></v-divider>

      <v-card-text>
        <v-form @submit.prevent="updateQuiz">
          <v-text-field
            v-model="quiz.title"
            label="Quiz Title"
            prepend-inner-icon="mdi-format-title"
          />

          <v-textarea
            v-model="quiz.description"
            label="Description"
            prepend-inner-icon="mdi-text-box-outline"
          />

          <v-select
            v-model="quiz.category"
            :items="categories"
            label="Select Category"
            prepend-inner-icon="mdi-shape-outline"
          />
          <v-textarea
            v-model="quiz.question"
            label="Question"
            prepend-inner-icon="mdi-help-circle-outline"
            rows="2"
            auto-grow
          />

          <v-card-actions class="justify-end">
            <v-btn color="grey" variant="outlined" @click="goBack">Cancel</v-btn>
            <v-btn color="primary" type="submit">Update Quiz</v-btn>
          </v-card-actions>
        </v-form>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup>
import axios from "axios";
import { reactive,ref,onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

const router = useRouter();
const route = useRoute();
const quizId = route.params.id;

const quiz = reactive({
  title: "",
  description: "",
  category: "",
  question: "",
  correctAnswer: "",
});

const category = ["", "", "", "", ""];
const loading = ref(false);


onMounted(async () => {
  try {
    const res = await axios.get(`/api/quiz/show/${quizId}`);
    const data = res.data.data;
    quiz.title = data.title;
    quiz.description = data.description ;
    quiz.category = data.category;
  quiz.question = data.question;
  quiz.correctAnswer = data.correctAnswer;
  } catch (err) {
    console.error("Error loading quiz:", err);
  }
});


const updateQuiz = async () => {
  loading.value = true;
  try {
    const res = await axios.put(`/api/quiz/update/${quizId}`, quiz);
    alert(res.data.message || "Quiz Updated Successfully!");
    router.push("/quiz");
  } catch (err) {
    console.error( err);
  } finally {
    loading.value = false;
  }
};

const goBack = () => {
  router.push("/quiz");
};
</script>
