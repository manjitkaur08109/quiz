  <template>
    <div>
      <div >
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

    <v-col cols="3" >
<v-card
  class="mx-auto my-8"
  elevation="16"
  max-width="344"
  @click="goToPayments"
  color="#1F7087"
>
  <v-card-item>
    <v-card-title> Total Payments </v-card-title>
  </v-card-item>
  <v-card-text> {{ totalPayments }} </v-card-text>
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
<v-row>
  <v-col cols="6">

    <v-card flat>
      <v-card-title class="d-flex align-center pe-2">
      Recent users

      <v-spacer></v-spacer>

    </v-card-title>

    <v-divider></v-divider>
    <v-data-table
  class="bordered-table"
    :headers="headers"
    density="compact"
    v-model:search="search"
    hide-default-footer
    :filter-keys="['name', 'email']"
    :items="recentUsers"
    >
    
    <template #item.sn="{ index }">
      {{ index + 1 }}
    </template>
  </v-data-table>
</v-card>
</v-col>
<v-col cols="6">
  <v-card flat>
    <v-card-title>Recent Payments</v-card-title>
    <v-divider></v-divider>
    
    <v-data-table
    :headers="paymentHeaders"
    class="bordered-table"
    v-model:search="search"
    density="compact"
    hide-default-footer
    :items="recentPayments"
    >
      <template #item.sn="{ index }">
        {{ index + 1 }}
      </template>

      <template #item.status="{ item }">
        <v-chip
          :color="item.status === 'paid' ? 'green' : 'red'"
          size="small"
        >
          {{ item.status }}
        </v-chip>
      </template>
      
            <template #item.created_at="{ item }">
                {{ moment(item.created_at).format("DD MMM YYYY, hh:mm A") }}
            </template>
    </v-data-table>
  </v-card>
</v-col>

</v-row>
</div>

  </div>
</template>
<script setup>
import { onMounted, ref , computed } from "vue";
import { useRouter } from "vue-router";
import { inject } from "vue";
import moment from "moment";

const api = inject("api");
const toast = inject("toast");
const router = useRouter();
const search = ref("");

const goToQuiz = () => {
  router.push("/quiz");
};

const goToUsers = () => {
  router.push("/users");
};

const goToPayments = () => {
  router.push("/payments");
};

const goToCategory = () => {
  router.push("/category");
};

const headers = [
  { title: "S.No", key: "sn" },
  { title: "Name", key: "name" },
  { title: "Email", key: "email" },
];
const paymentHeaders = [
  { title: "S.No", key: "sn" },
  { title: "User", key: "user.name" },
  { title: "Amount", key: "amount" },
  { title: "Status", key: "status" },
  { title: "Date", key: "created_at" },
];


const recentUsers = ref([]);
const totalUsers = ref(0);
const totalCategory = ref(0);
const totalQuiz = ref(0);
const totalPayments = ref(0);
const recentPayments = ref([]);


const getDashboard = async () => {
  try {

    const response = await api.get("/get-dashboard");
    totalCategory.value = response.data.data.totalCategory;
    totalQuiz.value = response.data.data.totalQuiz;
    totalUsers.value = response.data.data.totalUsers;
    totalPayments.value = response.data.data.totalPayments;
    recentPayments.value = response.data.data.recentPayments;
    recentUsers.value = response.data.data.recentUsers;
  } catch (error) {
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
<style>
.bordered-table {
  border: 1px solid #ccc;
  padding: 8px;
  text-align: left;
}


</style>


