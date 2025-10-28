  <template>
  <v-row>
    <v-col cols="3">
      <v-card
        class="mx-auto my-8"
        elevation="16"
        max-width="344"
        @click="goToQuiz"
        color="#1F7087"
      >
        <v-card-item>
          <v-card-title> Total Quiz </v-card-title>
        </v-card-item>
        <v-card-text> {{ totalQuiz }} </v-card-text>
      </v-card>
    </v-col>

    <v-col cols="3">
      <v-card
        class="mx-auto my-8"
        elevation="16"
        max-width="344"
        @click="goToUsers"
        color="#1F7087"
      >
        <v-card-item>
          <v-card-title> Total Users </v-card-title>
        </v-card-item>
        <v-card-text> {{ totalUsers }} </v-card-text>
      </v-card>
    </v-col>

    <v-col cols="3">
      <v-card
        class="mx-auto my-8"
        elevation="16"
        max-width="344"
        @click="goToCategory"
        color="#1F7087"
      >
        <v-card-item>
          <v-card-title> Total Category </v-card-title>
        </v-card-item>
        <v-card-text> {{ totalCategory }} </v-card-text>
      </v-card>

    </v-col>
  </v-row>

  <v-card flat>
    <v-card-title class="d-flex align-center pe-2">
      Recent users

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
    </v-card-title>

    <v-divider></v-divider>
    <v-data-table
      v-model:search="search"
      :filter-keys="['name', 'email']"
      :items="recentUsers"
    >
    </v-data-table>
  </v-card>
  
</template>
<script setup>
import axios from "axios";
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import { inject } from "vue";

const toast = inject("toast");
const router = useRouter();
const search = ref("");

const goToQuiz = () => {
  router.push("/quiz");
};

const goToUsers = () => {
  router.push("/users");
};

const goToCategory = () => {
  router.push("/category");
};

const recentUsers = ref([]);
const totalUsers = ref(0);
const totalCategory = ref(0);
const totalQuiz = ref(0);
const getDashboard = async () => {
  try {
    const token = localStorage.getItem("token");

    const response = await axios.get("api/get-dashboard", {
      headers: { Authorization: `Bearer ${token}` },
    });
    totalCategory.value = response.data.data.totalCategory;
    totalQuiz.value = response.data.data.totalQuiz;
    totalUsers.value = response.data.data.totalUsers;
    recentUsers.value = response.data.data.recentUsers;
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
  }
};
onMounted(() => {
  getDashboard();
});
</script>

