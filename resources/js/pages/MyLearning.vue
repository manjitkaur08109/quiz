<template>
  <div>
    <v-tabs class="mb-4" v-model="tab" align-tabs="end" color="deep-purple-accent-4">
      <v-tab value="all" @click="showAllCategories">All</v-tab>
      <v-tab v-for="category in categories" :key="category.id" :value="category.id" @click="selectCategory(category)">
        {{ category.title }}
      </v-tab>
    </v-tabs>

    <v-row>
      <v-col v-for="attempted in quizzes" :key="attempted.id" cols="12" sm="6" md="4" lg="4">
        <v-card class="hover:scale-105 transition-all mb-4 shadow-lg rounded-xl quiz-card" elevation="8"
          @click="openQuizDialog(attempted)">
          <v-card-text>
            <div class="d-flex align-center justify-space-between mb-2">
              <span class="text-h6 font-weight-bold">📘 {{ attempted.quiz.title }}</span>
              <v-chip color="deep-purple-accent-4" text-color="white" size="small" class="ml-2">{{
                attempted.quiz.category?.title }}</v-chip>
            </div>
            <div class="mb-2 text-caption text-grey-darken-1">
              {{
                attempted.quiz.description.length > 60
                  ? attempted.quiz.description.slice(0, 60) + "..."
                  : attempted.quiz.description
              }}
            </div>

            <div class="d-flex flex-wrap gap-2 mb-1">
              <v-chip color="blue" size="x-small">🧮
                {{
                  attempted.attempted_answers?.length ||
                  attempted.quiz?.questions?.length ||
                  0
                }}
                Qs</v-chip>

              <v-chip color="green" size="x-small">
                🎯 {{ attempted.passing_score }} Pass
              </v-chip>

              <v-chip :color="attempted.passed ? 'green' : 'red'" text-color="white" size="x-small">
                {{ attempted.passed ? "Passed" : "Failed" }}
              </v-chip>
            </div>
          </v-card-text>
          <v-card-actions>
            <v-spacer />
            <v-btn color="primary" variant="flat" size="small" class="float-right"
              @click.stop="openQuizDialog(attempted)">View Quiz</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
      <v-col v-if="quizzes.length === 0" cols="12" class="text-center py-10 text-grey">
        <v-icon size="x-large" color="grey">mdi-emoticon-sad-outline</v-icon>
        <div>No quizzes available for this category.</div>
      </v-col>
    </v-row>

    <v-dialog v-model="dialog" max-width="900">
      <v-card>
        <v-card-title class="text-h6">Quiz Details</v-card-title>
        <v-card-text>
          <v-card-text v-if="selectedQuiz">
            <p><strong>Title:</strong> {{ selectedQuiz.quiz.title }}</p>
            <p>
              <strong>Description:</strong> {{ selectedQuiz.quiz.description }}
            </p>
            <p>
              <strong>Category:</strong> {{ selectedQuiz.quiz.category?.title }}
            </p>
            <p>
              <strong>Created At:</strong>
              {{ moment(selectedQuiz.created_at).format("DD MMM YYYY, hh:mm A") }}
            </p>

            <v-divider class="my-3"></v-divider>

            <div class="d-flex flex-wrap gap-2 mb-2">
              <v-chip color="blue" size="small">
                🧮 Total Questions:
                {{
                  selectedQuiz.quiz.questions?.length ||
                  selectedQuiz.attempted_answers?.length ||
                  0
                }}
              </v-chip>

              <v-chip color="orange" size="small">
                ✏️ Attempted:
                {{
                  selectedQuiz.attempted_answers?.filter(
                    (q) => q.attempted && q.attempted !== ""
                  ).length || 0
                }}
              </v-chip>

              <v-chip color="green" size="small">
                🎯 Passing Score: {{ selectedQuiz.passing_score }}
              </v-chip>

              <v-chip color="deep-purple-accent-4" text-color="white" size="small">
                ✅ Correct Answers:
                {{
                  selectedQuiz.attempted_answers?.filter(
                    (q) => q.attempted === q.correctAnswer
                  ).length || 0
                }}
              </v-chip>

              <v-chip :color="selectedQuiz.passed ? 'green' : 'red'" text-color="white" size="small">
                {{ selectedQuiz.passed ? "Passed 🎉" : "Failed ❌" }}
              </v-chip>
            </div>

            <v-divider class="my-3"></v-divider>

            <h4 class="text-h6 mb-2">Questions</h4>

            <div v-for="(q, index) in selectedQuiz.attempted_answers" :key="index" class="mb-4 pa-3 rounded-lg border"
              style="border: 1px solid #ccc">
              <p>
                <strong>Q{{ index + 1 }}:</strong> {{ q.question }}
              </p>

              <v-list density="compact">
                <v-list-item v-for="(opt, i) in q.options" :key="i" :title="opt" :class="{
                  'bg-green-lighten-5': opt === q.correctAnswer,
                  'bg-red-lighten-5': opt === q.attempted && opt !== q.correctAnswer
                }">
                  <template #prepend>
                    <v-icon v-if="q.attempted && opt === q.attempted"
                      :color="q.attempted === q.correctAnswer ? 'green' : 'red'">
                      {{
                        q.attempted === q.correctAnswer
                          ? "mdi-check-circle-outline"
                          : "mdi-close-circle-outline"
                      }}
                    </v-icon>

                    <v-icon v-else-if="!q.attempted" color="grey">
                      mdi-circle-outline
                    </v-icon>
                  </template>

                </v-list-item>
                <v-list-item class="text-caption">
                  Correct Answer: {{ q.correctAnswer }}
                </v-list-item>
              </v-list>
            </div>
          </v-card-text>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn text @click="dialog = false">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, inject } from "vue";
import { useRouter } from "vue-router";
import moment from "moment";
const api = inject("api");
const router = useRouter();

const tab = ref(null);
const toast = inject("toast");
const categories = ref([]);
const quizzes = ref([]);
const selectedCategory = ref(null);
const dialog = ref(false);
const selectedQuiz = ref(null);

const fetchCategories = async () => {
  try {
    const res = await api.get("/category/index");
    const data = res.data.data || res.data;
    categories.value = data;
  } catch (error) {
    console.error("Error fetching categories:", error);
    toast?.value?.showToast("Failed to load categories", "error");
  }
};

const fetchQuizzes = async (categoryId = null) => {
  try {
    const token = localStorage.getItem("token");
    const url = categoryId
      ? `/quiz-attempt/index?category_id=${categoryId}`
      : `/quiz-attempt/index`;
    const res = await api.get(url);
    const data = res.data.data || res.data;

    data.forEach((quiz) => {
      if (quiz.score !== undefined && quiz.passing_score !== undefined) {
        quiz.passed = quiz.score >= quiz.passing_score;
      } else if (quiz.attempted_answers) {
        const correctCount = quiz.attempted_answers.filter(
          (q) => q.attempted === q.correctAnswer
        ).length;
        quiz.passed = correctCount >= quiz.passing_score;
        quiz.score = correctCount;
      } else {
        quiz.passed = false;
      }
    });

    quizzes.value = data;
  } catch (error) {
    console.error("Error fetching quizzes:", error);
    toast?.value?.showToast("Failed to load quizzes", "error");
    quizzes.value = [];
  }
};

const selectCategory = async (category) => {
  selectedCategory.value = category;
  await fetchQuizzes(category.id);
};

const showAllCategories = async () => {
  quizzes.value = [];
  selectedCategory.value = null;
  await fetchQuizzes();
};

const openQuizDialog = async (quiz) => {
  selectedQuiz.value = quiz;
  dialog.value = true;
};

onMounted(fetchCategories);
onMounted(() => fetchQuizzes());
</script>
