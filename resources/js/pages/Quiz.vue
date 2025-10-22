<template>
  <v-card flat>
    <v-card-title class="d-flex align-center pe-2">
      Quiz List
      <v-spacer></v-spacer>

      <v-text-field
        v-model="search"
        density="compact"
        label="Search"
        prepend-inner-icon="mdi-magnify"
        variant="solo-filled"
        flat
        hide-details
        single-line
      ></v-text-field>
      <v-btn
        color="primary"
        @click="goToAddQuiz"
        prepend-icon="mdi-plus"
        class="ml-4 mb-3"
      >
        Add New
      </v-btn>
    </v-card-title>
    <v-divider></v-divider>
    <v-data-table v-model:search="search" :headers="headers" :items="quizzes">
      <template #item.sn="{ index }">
        {{ index + 1 }}
      </template>

      <template #item.category="{ item }">
        {{ item.category.title }}
      </template>
      <template #item.actions="{ item }">
        <v-btn
          size="x-small"
          icon
          color="success"
          class="mr-2"
          @click="info(item)"
        >
          <v-icon>mdi-information</v-icon>
        </v-btn>
        <v-btn size="x-small" icon color="primary" @click="editQuiz(item.id)">
          <v-icon>mdi-pencil</v-icon>
        </v-btn>
        <v-btn size="x-small" class="ml-2" icon color="red" @click="deleteQuiz(item.id)">
          <v-icon>mdi-delete</v-icon>
        </v-btn>

         <!-- 📘 Quiz Info Dialog -->
    <v-dialog v-model="infoDialog" max-width="650">
      <v-card>
        <v-card-title class="text-h6">Quiz Information</v-card-title>
        <v-divider></v-divider>

        <v-card-text v-if="selectedQuiz">
          <p><strong>Title:</strong> {{ selectedQuiz.title }}</p>
          <p><strong>Description:</strong> {{ selectedQuiz.description }}</p>
          <p><strong>Category:</strong> {{ selectedQuiz.category?.title }}</p>
          <p><strong>Created At:</strong> {{ new Date(selectedQuiz.created_at).toLocaleString() }}</p>

          <v-divider class="my-3"></v-divider>

          <h4 class="text-h6 mb-2">Questions</h4>


          <div
            v-for="(q, index) in selectedQuiz.questions"
            :key="index"
            class="mb-4 pa-3 rounded-lg border"
            style="border: 1px solid #ccc;"
          >
            <p><strong>Q{{ index + 1 }}:</strong> {{ q.question }}</p>

            <v-list density="compact">
              <v-list-item
                v-for="(opt, i) in q.options"
                :key="i"
                :title="opt"
              >
                <template #prepend>
                  <v-icon
                    :color="opt.id === q.correct_option?.id ? 'green' : 'grey'"
                  >
                    {{
                      opt === q.correctAnswer
                        ? 'mdi-check-circle'
                        : 'mdi-circle-outline'
                    }}
                  </v-icon>
                </template>
              </v-list-item>
            </v-list>

          </div>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" text @click="infoDialog = false">
            Close
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

      </template>
    </v-data-table>
  </v-card>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();
const search = ref("");
const quizzes = ref([]);
const loading = ref(false);
const infoDialog = ref(false);
const selectedQuiz = ref(null);

const headers = [
  { title: "S.No", key: "sn" },
  { title: "Quiz Title", key: "title" },
  { title: "Description", value: "description" },
  { title: "Category", key: "category" },
  { title: "Actions", key: "actions", sortable: false },
];


const fetchQuizzes = async () => {
  loading.value = true;
  try {
    const res = await axios.get("/api/quiz/index");

    quizzes.value = res.data.data;
  } finally {
    loading.value = false;
  }
};

const info = async (item) => {
selectedQuiz.value = item
    infoDialog.value = true;
}

const deleteQuiz = async (id) => {
  if (!confirm("Delete this quiz?")) return;
  try {
    await axios.delete(`/api/quiz/delete/${id}`);
    fetchQuizzes();
  } catch (error) {
    console.error("Error deleting quiz:", error);
  }
};
onMounted(() => {
  fetchQuizzes();
});

const goToAddQuiz = () => router.push("/addQuiz");
const editQuiz = (id) => router.push(`/editQuiz/${id}`);
</script>

