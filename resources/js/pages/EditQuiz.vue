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
import { reactive } from "vue";
import { useRoute, useRouter } from "vue-router";

const router = useRouter();
const route = useRoute();
const quizId = route.params.id;

const quiz = reactive({
  title: "Sample Quiz " + quizId,
  description: "This is a demo quiz.",
  category: "Science",
});

const categories = ["Science", "Math", "History", "GK", "Sports"];

const updateQuiz = () => {
  alert("Quiz updated successfully!");
  router.push("/quiz");
};

const goBack = () => {
  router.push("/quiz");
};
</script>
