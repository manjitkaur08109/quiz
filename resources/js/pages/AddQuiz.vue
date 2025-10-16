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
            <v-form ref="formRef" @submit.prevent="handleSubmit">
              <v-text-field
                v-model="quiz.title"
                label="Quiz Title"
                :rules="QuizTitleRules"
                prepend-inner-icon="mdi-format-title"
                required
              />

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

               <div v-for="(q, qIndex) in quiz.questions" :key="qIndex" class="mb-4">
                <v-textarea
                  v-model="q.question"
                  :label="`Question ${qIndex + 1}`"
                  :rules="QuestionRules"
                  rows="2"
                  auto-grow
                  class="mb-2"
                />

                <!-- Options -->
                <div v-for="(o, oIndex) in q.options" :key="oIndex" class="mb-2">
                  <v-text-field
                    v-model="q.options[oIndex]"
                    :label="`Option ${oIndex + 1}`"
                    :rules="OptionRules"
                    class="flex-grow-1"
                  />
                  <v-icon color="red" @click="removeOption(qIndex, oIndex)">mdi-delete</v-icon>
                </div>

                <v-btn small color="primary" @click="addOption(qIndex)">Add Option</v-btn>

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

                <v-divider class="my-4"></v-divider>
              </div>

              <v-btn
                color="secondary"
                @click="addQuestion"
                class="mb-4"
              >
                Add Question
              </v-btn>
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
const formRef = ref('')
const QuizTitleRules = [
    (value) => {
    if (value?.length >= 3) return true;
    return ' Title required|string|max:15';
  },
];
const QuizDescriptionRules = [
  (value) => {
    if (value?.length >= 10) return true;
    return "Description must be at least 10 characters.|nullable";
  },
];
const SCRules = [
    (value) =>{
        if(value) return true;
        return ' Category is required';
    },
];
const QuestionRules =[
    (value)=>{
        if(value) return true;
        return 'Required';
    },
];
const OptionRules =[
    (value)=>{
        if(value) return true;
        return 'Option is Required';
    },
];
const SCARules =[
    (value)=>{
        if(value) return true;
        return 'Required';
    },
];
const categories = ref([]);
const quiz = reactive({
  title: "",
  description: "",
  category_id: null,
  questions:[
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
    const res = await axios.get("/api/quiz/create");
    categories.value = res.data.data;
    console.log("Categories loaded:", categories.value);
  } catch (error) {
    console.error("Error loading categories:", error);
  }
});
const addQuestion = () => {
  quiz.questions.push({
    question: "",
    options: ["", ""],
    correctAnswer: "",
  });
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


const handleSubmit = async () => {
      const { valid } = await formRef.value.validate(); // ✅ validate all fields
  if (!valid) return; // stop if invalid
  console.log("Submitting quiz:", JSON.stringify(quiz));
  try {
    loading.value = true;

    const res = await axios.post("/api/quiz/store", quiz);
    alert("Quiz added successfully!");
    console.log("Quiz created:", res.data);
    quiz.title = "";
    quiz.description = "";
    quiz.category_id = "";

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
