<template>
  <v-container class="py-8">
    <v-card class="mx-auto" min-width="600" elevation="8">
      <v-card-title>
        <v-icon size="x-small" class="mr-2">mdi-pencil</v-icon>
        Edit Quiz
      </v-card-title>

      <v-divider></v-divider>

      <v-card-text>
        <v-form ref="formRef" @submit.prevent="updateQuiz">
          <v-text-field
            v-model="quiz.title"
            label="Quiz Title"
            :rules="validateMaxLength('quiz title',20)"
            prepend-inner-icon="mdi-format-title"
          />

          <v-col cols="6">
            <v-text-field
              v-model="quiz.price"
              label="Quiz Price (₹)"
              type="number"
              min="1"
              :rules="validateRequired('Quiz Price')"
              prepend-inner-icon="mdi-currency-inr"
              required
            />
          </v-col>
          
          <v-col cols="6">
            <v-text-field
              v-model="quiz.passing_score"
              label="Passing Score "
              type="number"
              :rules="validatePassingScore(100)"
              prepend-inner-icon="mdi-target"
              min="0"
              max="100"
              required
            />
          </v-col>

          <v-textarea
            v-model="quiz.description"
            label="Description"
            prepend-inner-icon="mdi-text-box-outline"
            :rules="validateMaxLength('quiz description', 200)"
          />

          <v-select
            v-model="quiz.category_id"
            :items="categories"
            item-title="title"
            item-value="id"
            label="Select Category"
            prepend-inner-icon="mdi-shape-outline"
            :rules="validateRequired('Category')"
          />

          <div v-for="(q, qIndex) in quiz.questions" :key="qIndex" class="mb-4">
            <v-textarea
              v-model="q.question"
              :label="`Question ${qIndex + 1}`"
              prepend-inner-icon="mdi-help-circle-outline"
              :rules="validateMaxLength('question', 200)"
              rows="2"
              auto-grow
              class="mb-2"
            />

            <div v-for="(o, oIndex) in q.options" :key="oIndex" class="mb-2">
              <v-text-field
                v-model="q.options[oIndex]"
                :label="`Option ${oIndex + 1}`"
                :rules="validateRequired(`Option ${oIndex + 1}`)"
                class="flex-grow-1"
              />
              <v-icon
                size="x-small"
                color="red"
                @click="removeOption(qIndex, oIndex)"
                >mdi-delete</v-icon
              >
            </div>

            <v-btn small color="primary" @click="addOption(qIndex)"
              >Add Option</v-btn
            >

            <v-select
              v-model="q.correctAnswer"
              :items="q.options"
              label="Select Correct Answer"
              :rules="validateRequired('Correct Answer')"
              prepend-inner-icon="mdi-check-circle-outline"
              required
              class="mt-2"
            />

            <v-btn
              v-if="quiz.questions.length > 1"
              color="red"
              small
              class="mt-2"
              @click="removeQuestion(qIndex)"
            >
              Delete Question
            </v-btn>

            <v-btn
              v-if="quiz.questions.length > 1"
              color="secondary"
              small
              class="mt-2"
              @click="duplicateQuestion(qIndex)"
            >
              Duplicate Question
            </v-btn>

            <v-divider class="my-4"></v-divider>
          </div>

          <v-btn color="secondary" @click="addQuestion" class="mb-4">
            Add Question
          </v-btn>

          <v-card-actions class="justify-end">
            <v-btn color="grey" variant="outlined" @click="goBack"
              >Cancel</v-btn
            >
            <v-btn color="primary" type="submit" :loading="loading"
              >Update Quiz</v-btn
            >
          </v-card-actions>
        </v-form>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup>
import { reactive, ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
const api = inject("api");
const formRef = ref(null);
const router = useRouter();
const route = useRoute();
const quizId = route.params.id;
const loading = ref(false);
import { inject } from "vue";

import { validateMaxLength, validateRequired, validatePassingScore } from "../utils/validationRules";

const toast = inject("toast");

const quiz = reactive({
  title: "",
  price:"",
  passing_score: 0,
  description: "",
  category_id: "",
  questions: [{ question: "", options: ["", ""], correctAnswer: "" }],
});

const categories = ref([]);


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
const duplicateQuestion = (qIndex) => {
  const question = quiz.questions[qIndex];

  const isQuestionEmpty = !question.question.trim();
  const isOptionEmpty = question.options.some((opt) => !opt.trim());
  const isAnswerEmpty = !question.correctAnswer.trim();

  if (isQuestionEmpty || isOptionEmpty || isAnswerEmpty) {
    toast.value.showToast("Cannot duplicate an incomplete question!", "error");
    return;
  }

  const clonedQuestion = JSON.parse(JSON.stringify(question));
  quiz.questions.splice(qIndex + 1, 0, clonedQuestion);
};
const removeQuestion = (qIndex) => {
  quiz.questions.splice(qIndex, 1);
};

const addOption = (qIndex) => {
  quiz.questions[qIndex].options.push("");
};

const removeOption = (qIndex, oIndex) => {
  quiz.questions[qIndex].options.splice(oIndex, 1);
};

onMounted(async () => {
  try {
    const res = await api.get("/category/index");
    categories.value = res.data.data;
    console.log("Categories loaded:", categories.value);
  } catch (error) {

    toast.value.showToast(
      error?.response?.data?.message || "Something went wrong!",
      "error"
    );
  }
});

onMounted(async () => {
  try {
    const res = await api.get(`/quiz/show/${quizId}`);
    const data = res.data.data;

    quiz.title = data.title;
    quiz.price = data.price;
    quiz.passing_score = data.passing_score;
    quiz.description = data.description;
    quiz.category_id = data.category_id;
    quiz.questions = data.questions || [
      { question: "", options: ["", ""], correctAnswer: "" },
    ];

  } catch (error) {

    toast.value.showToast(
      error?.response?.data?.message || "Something went wrong!",
      "error"
    );
  }
});

const updateQuiz = async () => {
  const { valid } = await formRef.value.validate();
  if (!valid) return;
  loading.value = true;
  try {
    const res = await api.put(`/quiz/update/${quizId}`, quiz);
    toast.value.showToast(
       "Quiz Updated Successfully!",
      "success"
    );
    router.push("/quiz");
  } catch (error) {


  } finally {
    loading.value = false;
  }
};

const goBack = () => {
  router.push("/quiz");
};
</script>
