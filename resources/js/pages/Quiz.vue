<template>
  <v-card flat>
    <v-card-title class="d-flex align-center pe-2">
      Quiz List
      <v-spacer></v-spacer>

      <v-text-field v-model="search" density="compact" label="Search" prepend-inner-icon="mdi-magnify"
        variant="solo-filled" @update:modelValue="onSearch" flat hide-details single-line></v-text-field>
      <v-btn  @click="goToAddQuiz" color="primary" prepend-icon="mdi-plus" class="ml-4 mb-3">
        Add New
      </v-btn>
    </v-card-title>
    <v-divider></v-divider>
    <v-data-table v-model:search="search" :headers="headers" 
    v-model:page="pagination.current_page"
    :items-per-page-options="[5, 10, 25, 50]"
    v-model:items-per-page="pagination.per_page"
    :items-length="pagination.total"
    @update:page="onPageChange"
    @update:items-per-page="onPerPageChange"
    density="compact"
    :items="quizzes">
      <template #item.sn="{ index }">
        {{ index + 1 }}
      </template>

      <template #item.description="{ item }">
        {{ item.description.length > 50
          ? item.description.slice(0, 50) + "..."
          : item.description }}
      </template>
      <template #item.category="{ item }">
        {{ item?.category?.title }}
      </template>
      <template #item.actions="{ item }">
        <v-btn v-if="can('view quiz')" size="x-small" icon color="success" class="mr-2" @click="info(item)">
          <v-icon>mdi-information</v-icon>
        </v-btn>
        <v-btn v-if="can('edit quiz')" size="x-small" icon color="primary" @click="editQuiz(item.id)">
          <v-icon>mdi-pencil</v-icon>
        </v-btn>
        <v-btn v-if="can('delete quiz')" size="x-small" class="ml-2" icon color="red" @click="deleteQuiz(item.id)">
          <v-icon>mdi-delete</v-icon>
        </v-btn>

        <v-dialog v-model="infoDialog" max-width="650">
          <v-card>
            <v-card-title class="text-h6">Quiz Information</v-card-title>
            <v-divider></v-divider>

            <v-card-text v-if="selectedQuiz">
              <p><strong>Title:</strong> {{ selectedQuiz.title }}</p>
              <p>
                <strong>Description:</strong> {{ selectedQuiz.description }}
              </p>
              <p>
                <strong>Category:</strong> {{ selectedQuiz.category?.title }}
              </p>
              <p>
                <strong>Created At:</strong>
                {{ moment(selectedQuiz.created_at).format("DD MMM YYYY, hh:mm A") }}
              </p>

              <v-divider class="my-3"></v-divider>

              <h4 class="text-h6 mb-2">Questions</h4>

              <div v-for="(q, index) in selectedQuiz.questions" :key="index" class="mb-4 pa-3 rounded-lg border"
                style="border: 1px solid #ccc">
                <p>
                  <strong>Q{{ index + 1 }}:</strong> {{ q.question }}
                </p>

                <v-list density="compact">
                  <v-list-item v-for="(opt, i) in q.options" :key="i" :title="opt">
                    <template #prepend>
                      <v-icon :color="opt.id === q.correct_option?.id ? 'green' : 'grey'
                        ">
                        {{
                          opt === q.correctAnswer
                            ? "mdi-check-circle"
                            : "mdi-circle-outline"
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
import { inject } from "vue";
import moment from "moment";
const permissions = JSON.parse(localStorage.getItem("permissions") || "[]");

const can = (permission) => {
  return permissions.includes(permission);
};
const api = inject("api");
const toast = inject("toast");
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

const pagination = ref({
  current_page: 1,
    per_page: 5,
  total: 0,
});

const onPageChange = (page) => {
  pagination.value.current_page = page;
  fetchQuizzes();
};
const onPerPageChange = (perPage) => {
  pagination.value.per_page = perPage;
  pagination.value.current_page = 1;
  fetchQuizzes();
};
const onSearch = () => {
  pagination.value.current_page = 1;
  fetchQuizzes();
};
const fetchQuizzes = async () => {
    if (!can("view quiz")) {
    toast.value.showToast("You are not authorized to view quiz", "error");
    router.push("/quiz"); 
    return;
  }
  loading.value = true;
  try {
    const res = await api.get(`/quiz/index?page=${pagination.value.current_page}&per_page=${pagination.value.per_page}&search=${search.value}`);
    quizzes.value = res.data.data.data;
    pagination.value.current_page = res.data.data.current_page;
    pagination.value.per_page =  res.data.data.per_page;
    pagination.value.total = res.data.data.total;
  } catch (error) {

    toast.value.showToast(
      error?.response?.data?.message || "Something went wrong!",
      "error"
    );
  } finally {
    loading.value = false;
  }
};

const info = async (item) => {
  selectedQuiz.value = item;
  infoDialog.value = true;
};

const deleteQuiz = async (id) => {
    if (!can("delete quiz")) {
    toast.value.showToast("You are not authorized to delete quiz", "error");
    router.push("/quiz"); 
    return;
  }
  if (!confirm("Delete this quiz?")) return;
  try {
    await api.delete(`/quiz/delete/${id}`);
    toast.value.showToast("Quiz deleted successfully", 'success');

    fetchQuizzes();
  } catch (error) {
    console.error("Error deleting quiz:", error);

    toast.value.showToast(error?.response?.data?.message || "Something went wrong!", 'error');

  }
};
onMounted(() => {
  fetchQuizzes();
});

const goToAddQuiz = () => router.push("/addQuiz");
const editQuiz = (id) => router.push(`/editQuiz/${id}`);
</script>
