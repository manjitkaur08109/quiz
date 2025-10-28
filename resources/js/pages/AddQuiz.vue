<template>
  <v-container class="py-8">
    <v-row>
      <v-col cols="12">
        <v-card class="mx-auto" min-width="600" elevation="8">
          <v-card-title class="text-h6">
            <v-icon
              size="x-small"
              icon="mdi-plus-circle-outline"
              class="mr-2"
            />
            Add New Quiz
          </v-card-title>

          <v-divider></v-divider>

          <v-card-text>
            <v-form ref="formRef" @submit.prevent="handleSubmit">
              <v-row>
                <v-col cols="6">
                  <v-text-field
                    v-model="quiz.title"
                    label="Quiz Title"
                    :rules="QuizTitleRules"
                    prepend-inner-icon="mdi-format-title"
                    required
                  />
                </v-col>
                <v-col cols="6">
                 <v-text-field
                    v-model="quiz.passing_score"
                    label="Passing Score (%)"
                    type="number"
                    :rules="PassingScoreRules"
                    prepend-inner-icon="mdi-target"
                    min="0"
                    max="100"
                    required
                    />
                </v-col>
              </v-row>
              <v-textarea
                v-model="quiz.description"
                label="Quiz Description"
                :rules="QuizDescriptionRules"
                prepend-inner-icon="mdi-text-box-outline"
                rows="3"
                auto-grow
              />
              <v-select
                v-model="quiz.category_id"
                :items="categories"
                item-title="title"
                item-value="id"
                label="Select Category"
                :rules="SCRules"
                prepend-inner-icon="mdi-shape-outline"
                required
              />

              <div
                v-for="(q, qIndex) in quiz.questions"
                :key="qIndex"
                class="mb-4"
              >
                <v-textarea
                  v-model="q.question"
                  :label="`Question ${qIndex + 1}`"
                  prepend-inner-icon="mdi-help-circle-outline"
                  :rules="QuestionRules"
                  rows="2"
                  auto-grow
                  class="mb-2"
                />

                <!-- Options -->
                <div
                  v-for="(o, oIndex) in q.options"
                  :key="oIndex"
                  class="mb-2"
                >
                  <v-text-field
                    v-model="q.options[oIndex]"
                    :label="`Option ${oIndex + 1}`"
                    :rules="OptionRules"
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
                  :rules="SCARules"
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
                <v-btn color="grey" @click="goBack"> Cancel </v-btn>
                <v-btn
                  color="primary"
                  :loading="loading"
                  type="submit"
                  class="ml-2"
                >
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
import { ref, reactive, onMounted } from "vue";
import { useRouter } from "vue-router";
const router = useRouter();
import axios from "axios";
import { inject } from "vue";

const toast = inject("toast");
const goToAdQuiz = () => router.push("/addQuiz");
const formRef = ref("");
const QuizTitleRules = [
  (value) => {
    if (value?.length >= 3) return true;
    return " Title required|string|min:3";
  },
];
const QuizDescriptionRules = [
  (value) => {
    if (value?.length >= 10) return true;
    return "Description must be at least 10 characters.|nullable";
  },
];
const SCRules = [
  (value) => {
    if (value) return true;
    return " Category is required";
  },
];
const QuestionRules = [
  (value) => {
    if (value) return true;
    return "Required";
  },
];
const OptionRules = [
  (value) => {
    if (value) return true;
    return "Option is Required";
  },
];
const SCARules = [
  (value) => {
    if (value) return true;
    return "Required";
  },
];
const PassingScoreRules = [
  (value) => {
    if (value >= 0 && value <= 100) return true;
    return "Passing score must be between 0 and 100.";
  },
];

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

const loading = ref(false);
onMounted(async () => {
  try {
    const token = localStorage.getItem("token");
    const res = await axios.get("/api/quiz/create", {
      headers: { Authorization: `Bearer ${token}` },
    });
    categories.value = res.data.data;
    console.log("Categories loaded:", categories.value);
  } catch (error) {
    console.error("Error loading categories:", error);
  }
});

const addQuestion = () => {
  const lastQuestion = quiz.questions[quiz.questions.length - 1];

  // Check karo current question complete hai ya nahi
  const isQuestionEmpty = !lastQuestion.question.trim();
  const isOptionEmpty = lastQuestion.options.some((opt) => !opt.trim());
  const isAnswerEmpty = !lastQuestion.correctAnswer.trim();

  if (isQuestionEmpty || isOptionEmpty || isAnswerEmpty) {
    toast.value.showToast(
      "Please fill the current question completely before adding a new one!",
      "error"
    );
    return; // Stop adding new question
  }
  // Add new blank question
  quiz.questions.push({
    question: "",
    options: ["", ""], // default 2 blank options
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
    return; // Stop duplication
  }

  const clonedQuestion = JSON.parse(JSON.stringify(question));
  quiz.questions.splice(qIndex + 1, 0, clonedQuestion);
};
// const duplicateQuestion = (qIndex) => {
//   const clonedQuestion = JSON.parse(JSON.stringify(quiz.questions[qIndex]));
//   quiz.questions.splice(qIndex + 1, 0, clonedQuestion);
// };
const addOption = (qIndex) => {
  quiz.questions[qIndex].options.push("");
};

const removeOption = (qIndex, oIndex) => {
  quiz.questions[qIndex].options.splice(oIndex, 1);
};

const handleSubmit = async () => {
  const { valid } = await formRef.value.validate(); // ✅ validate all fields
  if (!valid) return; // stop if invalid
  console.log("Submitting quiz:", JSON.stringify(quiz));
  try {
    loading.value = true;

    const token = localStorage.getItem("token");
    const res = await axios.post("/api/quiz/store", quiz, {
      headers: { Authorization: `Bearer ${token}` },
    });
    quiz.value = res.data.data;
    toast.value.showToast(res.data.message, "success");

    console.log("Quiz created:", res.data);
    quiz.title = "";
    quiz.passing_score = 0;
    quiz.description = "";
    quiz.category_id = "";

    router.push("/quiz");
  } catch (error) {
    if (error?.response?.status == 401) {
      localStorage.removeItem("token");
      localStorage.removeItem("user");
      router.push("/login");
    }
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
