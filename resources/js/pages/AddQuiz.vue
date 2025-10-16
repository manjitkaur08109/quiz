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
                v-model="quiz.category_id"
                :items="categories"
                item-title="title"
                item-value="id"
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
                v-for="(o, i) in quiz.options"
                :key="i"
                v-model="quiz.options[i]"
                :label="`Option ${i + 1}`"
                @click="quiz.options.push('')"
                prepend-icon="mdi-plus"
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
import { ref, reactive, onMounted } from "vue";
import { useRouter } from "vue-router";
const router = useRouter();
import axios from "axios";
const goToAdQuiz = () => router.push("/addQuiz");

const categories = ref([]);
const quiz = reactive({
  title: "",
  description: "",
  category_id: null,
  question: "",
  options: ["", "", "", ""],
  correctAnswer: "",
});
const loading = ref(false);
// const categories = ["Science", "Math", "History", "GK", "Sports"];
// ✅ Fetch categories from backend
onMounted(async () => {
  try {
    const res = await axios.get("/api/quiz/create");
    categories.value = res.data.data;
    console.log("Categories loaded:", categories.value);
  } catch (error) {
    console.error("Error loading categories:", error);
  }
});

const handleSubmit = async () => {
  console.log("Submitting quiz:", JSON.stringify(quiz));
  try {
    loading.value = true;

    const res = await axios.post("/api/quiz/store", quiz);
    alert("Quiz added successfully!");
    console.log("Quiz created:", res.data);
    quiz.title = "";
    quiz.description = "";
    quiz.category_id = "";
    quiz.question = "";
    quiz.options = ["", "", "", ""];
    quiz.correctAnswer = "";

    router.push("/quiz");
  } catch (error) {
    console.error("Error adding quiz:", error.response?.data || error);
    alert("Failed to add quiz! Check console for details.");
  } finally {
    loading.value = false;
  }
};
const goBack = () => {
  router.push("/quiz");
};
</script>
