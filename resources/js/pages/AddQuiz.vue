<template>
  <v-container class="py-8">
    <v-row>
        <v-col cols="12">
    <v-card class="mx-auto" max-width="600" elevation="8">
      <v-card-title class="text-h6">
        <v-icon icon="mdi-plus-circle-outline" class="mr-2" />
        Add New Quiz
      </v-card-title>

      <v-divider></v-divider>

      <v-card-text>
        <v-form @submit.prevent="handleSubmit">
          <v-text-field
            v-model="quiz.title"
            label="Quiz Title"
            prepend-inner-icon="mdi-format-title"
            required
          />

          <v-textarea
            v-model="quiz.description"
            label="Quiz Description"
            prepend-inner-icon="mdi-text-box-outline"
            rows="3"
            auto-grow
          />

          <v-select
            v-model="quiz.category"
            :items="categories"
            label="Select Category"
            prepend-inner-icon="mdi-shape-outline"
            required
          />

          <v-textarea
            v-model="quiz.question"
            label="Question"
            prepend-inner-icon="mdi-help-circle-outline"
            rows="2"
            auto-grow
          />

          <v-text-field
            v-for="(option, index) in quiz.options"
            :key="index"
            v-model="quiz.options[index]"
            :label="`Option ${index + 1}`"
            prepend-inner-icon="mdi-format-list-bulleted"
          />

          <v-select
            v-model="quiz.correctAnswer"
            :items="quiz.options"
            label="Select Correct Answer"
            prepend-inner-icon="mdi-check-circle-outline"
            required
          />

          <v-card-actions class="justify-end">
            <v-btn variant="outlined" color="grey" @click="goBack">
              Cancel
            </v-btn>
            <v-btn color="primary" type="submit" class="ml-2">
              Save Quiz
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card-text>
    </v-card>
    </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { reactive } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

const goToAdQuiz = () => router.push("/addQuiz");

const quiz = reactive({
  title: "",
  description: "",
  category: "",
  question: "",
  options: ["", "", "", ""],
  correctAnswer: "",
});

const categories = ["Science", "Math", "History", "GK", "Sports"];

const handleSubmit = () => {
  console.log("Quiz Data:", quiz);

  alert("Quiz added successfully!");
  router.push("/quiz");
};

const goBack = () => {
  router.push("/quiz");
};
</script>
