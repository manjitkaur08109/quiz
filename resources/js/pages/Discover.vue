<template>
  <div>
    <v-tabs
      class="mb-4"
      v-model="tab"
      align-tabs="end"
      color="deep-purple-accent-4"
    >
      <v-tab value="all" @click="showAllCategories">All</v-tab>
      <v-tab
        v-for="category in categories"
        :key="category.id"
        :value="category.id"
        @click="selectCategory(category)"
      >
        {{ category.title }}
      </v-tab>
    </v-tabs>
    <!-- 🧠 Quizzes under Selected Category -->
    <v-row>
      <v-col
        v-for="quiz in quizzes"
        :key="quiz.id"
        cols="12"
        sm="6"
        md="4"
        lg="4"
      >
        <v-card
          class="hover:scale-105 transition-all mb-4 shadow-lg rounded-xl quiz-card"
          elevation="8"
          @click="openQuizDialog(quiz)"
        >
          <v-card-text>
            <div class="d-flex align-center justify-space-between mb-2">
              <span class="text-h6 font-weight-bold">📘 {{ quiz.title }}</span>
              <v-chip
                color="deep-purple-accent-4"
                text-color="white"
                size="small"
                class="ml-2"
                >{{ quiz.category?.title }}</v-chip
              >
            </div>
            <div class="mb-2 text-caption text-grey-darken-1">
              {{
                quiz.description.length > 60
                  ? quiz.description.slice(0, 60) + "..."
                  : quiz.description
              }}
            </div>
            <div class="d-flex flex-wrap gap-2 mb-1">
              <v-chip color="blue" size="x-small"
                >🧮 {{ quiz.questions ? quiz.questions.length : 0 }} Qs</v-chip
              >
              <v-chip color="green" size="x-small"
                >🎯 {{ quiz.passing_score }} Pass</v-chip
              >
            </div>
          </v-card-text>
          <v-card-actions>
            <v-spacer />
            <v-btn
              color="primary"
              variant="flat"
              size="small"
              class="float-right"
              @click.stop="openQuizDialog(quiz)"
              >Start Quiz</v-btn
            >
          </v-card-actions>
        </v-card>
      </v-col>
      <v-col
        v-if="quizzes.length === 0"
        cols="12"
        class="text-center py-10 text-grey"
      >
        <v-icon size="x-large" color="grey">mdi-emoticon-sad-outline</v-icon>
        <div>No quizzes available for this category.</div>
      </v-col>
    </v-row>

    <v-dialog v-model="resultDialog" max-width="600">
      <v-card>
        <v-card-title class="text-h6">Quiz Result</v-card-title>
        <v-card-text>
          <div v-if="quizResult !== null">
            Passing Score: {{ selectedQuiz.passing_score }}.<br />
            Correct answers: {{ quizResult.correct }}.
            <br />
            <v-alert :type="quizResult.passed ? 'success' : 'error'" dense>
              You scored {{ quizResult.attemptedQuestions }}/{{
                selectedQuiz.questions.length
              }}.
              {{
                quizResult.passed
                  ? "Congratulations, you passed!"
                  : "Try again to pass."
              }}
            </v-alert>
          </div>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn text @click="closeQuizDialog">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <!-- Start Quiz Dialog -->
    <v-dialog v-model="dialog" max-width="900">
      <v-card>
        <v-card-title class="text-h6">Start Quiz</v-card-title>
        <v-card-text>
          <div v-if="selectedQuiz">
            <div class="mb-2 d-flex align-center justify-space-between">
              <span
                >📘 <strong>{{ selectedQuiz.title }}</strong></span
              >
              <v-chip
                color="deep-purple-accent-4"
                text-color="white"
                size="small"
                >{{ selectedQuiz.category?.title }}</v-chip
              >
            </div>
            <div class="mb-1 text-caption">{{ selectedQuiz.description }}</div>
            <div class="mb-1">
              🧮 Total Questions:
              {{ selectedQuiz.questions ? selectedQuiz.questions.length : 0 }}
            </div>
            <div class="mb-1">
              🎯 Passing Score: {{ selectedQuiz.passing_score }}
            </div>
            <div class="mt-2 text-subtle">Answer all questions below:</div>
            <v-row>
              <v-col
                v-for="(question, index) in selectedQuiz.questions"
                :key="index"
                cols="12"
                md="12"
              >
                <div class="pa-4 rounded-lg" style="background: #f8f8ff">
                  <div class="font-weight-bold mb-2">
                    Q{{ index + 1 }}. {{ question.question }}
                  </div>
                  <v-radio-group
                    v-model="userAnswers[index]"
                    :mandatory="true"
                    class="ml-2"
                  >
                    <v-radio
                      v-for="(option, oindex) in question.options"
                      :key="oindex"
                      :label="option"
                      :value="option"
                      color="deep-purple-accent-4"
                      class="mb-1"
                    />
                  </v-radio-group>
                </div>
              </v-col>
            </v-row>
          </div>
          <div v-else>
            <v-progress-circular indeterminate color="deep-purple-accent-4" />
            Loading...
          </div>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn text @click="closeQuizDialog">Cancel</v-btn>
          <v-btn color="primary" @click="submitQuiz">Submit</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, inject } from "vue";
import axios from "axios";
const tab = ref(null);
const toast = inject("toast");
const categories = ref([]);
const quizzes = ref([]);
const selectedCategory = ref(null);
const dialog = ref(false);
const resultDialog = ref(false);
const selectedQuiz = ref(null);
const userAnswers = ref([]);
const quizResult = ref(null);

// ✅ Fetch all categories
const fetchCategories = async () => {
  try {
    const token = localStorage.getItem("token");
    const res = await axios.get("/api/category/index", {
      headers: { Authorization: `Bearer ${token}` },
    });
    const data = res.data.data || res.data;
    categories.value = data;
  } catch (error) {
    console.error("Error fetching categories:", error);
    toast?.value?.showToast("Failed to load categories", "error");
  }
};

// ✅ Fetch quizzes; if categoryId provided, include it as a query param to filter by category
const fetchQuizzes = async (categoryId = null) => {
  try {
    const token = localStorage.getItem("token");
    const url = categoryId
      ? `/api/quiz/index?category_id=${categoryId}`
      : `/api/quiz/index`;
    const res = await axios.get(url, {
      headers: { Authorization: `Bearer ${token}` },
    });
    const data = res.data.data || res.data;
    quizzes.value = data;
  } catch (error) {
    console.error("Error fetching quizzes:", error);
    toast?.value?.showToast("Failed to load quizzes", "error");
    quizzes.value = [];
  }
};

// ✅ Select category and load quizzes (accepts full category object)
const selectCategory = async (category) => {
  selectedCategory.value = category;
  await fetchQuizzes(category.id);
};

// ✅ Show all categories (reset)
const showAllCategories = async () => {
  quizzes.value = [];
  selectedCategory.value = null;
  await fetchQuizzes();
};

// ✅ Open dialog for quiz
const openQuizDialog = (quiz) => {
  selectedQuiz.value = quiz;
  dialog.value = true;
  quizResult.value = null;
};

// ✅ Close quiz dialog
const closeQuizDialog = () => {
  dialog.value = false;
  selectedQuiz.value = null;
  userAnswers.value = [];
  quizResult.value = null;
  resultDialog.value = false;
};

const submitQuiz = async () => {
  if (!selectedQuiz.value) return;

  // 🔹 Step 1: Calculate Result
  let correct = 0;
  const attemptedAnswers = [];
  selectedQuiz.value.questions.forEach((q, index) => {
    const userAnswer = userAnswers.value[index];
    const isCorrect = userAnswer === q.correctAnswer;
    if (isCorrect) {
      correct++;
    }
    q.attempted = userAnswer ?? "";
    q.isCorrect = isCorrect;
    attemptedAnswers.push(q);
  });
  const attemptedQuestions = userAnswers.value.filter((a) => a !== null).length;
  const passed = correct >= selectedQuiz.value.passing_score;

  // 🔹 Step 2: Update UI result instantly
  quizResult.value = { passed, correct, attemptedQuestions };

  // 🔹 Step 3: Save Attempt to Backend
  try {
    const token = localStorage.getItem("token");
    await axios.post(
      "/api/quiz-attempt/store",
      {
        quiz_id: selectedQuiz.value.id,
        score: correct,
        passing_score: selectedQuiz.value.passing_score,
        total_questions: selectedQuiz.value.questions.length,
        marks_obtained: attemptedQuestions,
        attempted_answers: attemptedAnswers,
      },
      {
        headers: { Authorization: `Bearer ${token}` },
      }
    );

    toast?.value?.showToast("✅ Quiz attempt saved!", "success");
    dialog.value = false;
    resultDialog.value = true;
  } catch (error) {
    console.error(" Error saving quiz attempt:", error);
    toast?.value?.showToast("Failed to save quiz attempt", "error");
  }
};

onMounted(fetchCategories);
onMounted(() => fetchQuizzes());
</script>



