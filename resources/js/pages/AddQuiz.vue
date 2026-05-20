<template>
  <v-container class="py-8">
    <v-row>
      <v-col cols="12">
        <v-card class="mx-auto" min-width="600" elevation="8">
          <v-card-title class="text-h6">
            <v-icon size="x-small" icon="mdi-plus-circle-outline" class="mr-2" />
            Add New Quiz
          </v-card-title>

          <v-divider></v-divider>

          <v-card-text>
            <v-form ref="formRef" @submit.prevent="handleSubmit">
              <v-row>
                <v-col cols="6">
                  <v-text-field v-model="quiz.title" label="Quiz Title" :rules="validateMaxLength('Quiz Title', 20)"
                    prepend-inner-icon="mdi-format-title" required />
                </v-col>


                <v-col cols="6">
                  <v-text-field v-model="quiz.price" label="Quiz Price (₹)" type="number" min="1"
                    :rules="validateRequired('Quiz Price')" prepend-inner-icon="mdi-currency-inr" required />
                </v-col>

                <v-col cols="6">
                  <v-text-field v-model="quiz.passing_score" label="Passing Score" type="number"
                    :rules="validatePassingScore(100)" prepend-inner-icon="mdi-target" min="0" max="100" required />
                </v-col>
              </v-row>
              <v-textarea v-model="quiz.description" label="Quiz Description"
                :rules="validateMaxLength('Quiz Description', 200)" prepend-inner-icon="mdi-text-box-outline" rows="3"
                auto-grow />
              <v-select v-model="quiz.category_id" :items="categories" item-title="title" item-value="id"
                label="Select Category" :rules="validateRequired('Category')" prepend-inner-icon="mdi-shape-outline"
                required />

              <div v-for="(q, qIndex) in quiz.questions" :key="qIndex" class="mb-4">
                <v-textarea v-model="q.question" :label="`Question ${qIndex + 1}`"
                  prepend-inner-icon="mdi-help-circle-outline" :rules="validateMaxLength('question', 200)" rows="2"
                  auto-grow class="mb-2" />

                <div v-for="(o, oIndex) in q.options" :key="oIndex" class="mb-2">
                  <v-text-field v-model="q.options[oIndex]" :label="`Option ${oIndex + 1}`"
                    :rules="validateRequired(`Option ${oIndex + 1}`)" class="flex-grow-1" />
                  <v-icon size="x-small" color="red" @click="removeOption(qIndex, oIndex)">mdi-delete</v-icon>
                </div>

                <v-btn small color="primary" @click="addOption(qIndex)">Add Option</v-btn>

                <v-select v-model="q.correctAnswer" :items="q.options" label="Select Correct Answer"
                  :rules="validateRequired(`Correct Answer for Question ${qIndex + 1}`)" required class="mt-2" />

                <v-btn v-if="quiz.questions.length > 1" color="red" small class="mt-2" @click="removeQuestion(qIndex)">
                  Delete Question
                </v-btn>

                <v-btn v-if="quiz.questions.length > 1" color="secondary" small class="mt-2"
                  @click="duplicateQuestion(qIndex)">
                  Duplicate Question
                </v-btn>

                <v-divider class="my-4"></v-divider>
              </div>

              <v-btn color="secondary" @click="addQuestion" class="mb-4">
                Add Question
              </v-btn>
              <v-card-actions class="justify-end">
                <v-btn color="grey" @click="goBack"> Cancel </v-btn>
                <v-btn color="primary" :loading="loading" type="submit" class="ml-2">
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
import { ref, reactive, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import { inject } from "vue";
// import { can } from '@/permission'

import {
  validateRequired,
  validatePassingScore,
  validateMaxLength
} from "@/utils/validationRules.js";



const permissions = JSON.parse(localStorage.getItem("permissions") || "[]");

const can = (permission) => {
  return permissions.includes(permission);
};
const router = useRouter();
const api = inject("api");
const toast = inject("toast");
const goToAdQuiz = () => router.push("/addQuiz");
const formRef = ref("");

const categories = ref([]);
const quiz = reactive({
  title: "",
  passing_score: 0,
  description: "",
  category_id: null,
  questions: [
    {
      question: "",
      options: ["", ""],
      correctAnswer: "",
    },
  ],
});

const apiQuiz = computed(() => {
  return {
    ...quiz,
    passing_score: quiz.passing_score,
  };
});

const loading = ref(false);
onMounted(async () => {

  if (!can("view category")) {
    toast.value.showToast("You are not authorized to view category", "error");
    return;
  }
  try {
    const res = await api.get("/category/index");
    categories.value = res.data.data;
    console.log("Categories loaded:", categories.value);
  } catch (error) {
    console.error("Error loading categories:", error);
  }
});

const addQuestion = () => {
  const lastQuestion = quiz.questions[quiz.questions.length - 1];

  const isQuestionEmpty = !lastQuestion.question.trim();
  const isOptionEmpty = lastQuestion.options.some((opt) => !opt.trim());
  const isAnswerEmpty = !lastQuestion.correctAnswer.trim();

  if (isQuestionEmpty || isOptionEmpty || isAnswerEmpty) {
    toast.value.showToast(
      "Please fill the current question completely before adding a new one!",
      "error"
    );
    return;
  }
  quiz.questions.push({
    question: "",
    options: ["", ""],
    correctAnswer: "",
  });
};

const removeQuestion = (qIndex) => {
  quiz.questions.splice(qIndex, 1);
};
const duplicateQuestion = (qIndex) => {
  const question = quiz.questions[qIndex];

  const isQuestionEmpty = !question.question.trim();
  const isOptionEmpty = question.options.some((opt) => !opt.trim());
  const isAnswerEmpty = !question.correctAnswer.trim();

  if (isQuestionEmpty || isOptionEmpty || isAnswerEmpty) {
    toast.value.showToast("Cannot duplicate an incomplete question!", "erorr");
    return;
  }

  const clonedQuestion = JSON.parse(JSON.stringify(question));
  quiz.questions.splice(qIndex + 1, 0, clonedQuestion);
};

const addOption = (qIndex) => {
  quiz.questions[qIndex].options.push("");
};

const removeOption = (qIndex, oIndex) => {
  quiz.questions[qIndex].options.splice(oIndex, 1);
};

const handleSubmit = async () => {
  if (!can("create quiz")) {
    toast.value.showToast("You are not authorized to create quiz", "error");
    router.push("/quiz");
    return;
  }
  const { valid } = await formRef.value.validate();
  if (!valid) return;


  console.log("Submitting quiz:", JSON.stringify(apiQuiz.value));
  try {
    loading.value = true;

    const token = localStorage.getItem("token");
    const res = await api.post("/quiz/store", quiz, {
      headers: { Authorization: `Bearer ${token}` },
    });
    toast.value.showToast(res.data.message, "success");
    router.push("/quiz");
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
  router.push("/quiz");
};
</script>
