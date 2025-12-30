<template>
    <v-card>
        <v-card-title class="d-flex align-center pe-2">
            Payments</v-card-title>
        <v-divider></v-divider>
        <v-data-table-server v-model:search="search" :items="paymentsitems" :headers="headers"
           :items-length="pagination.total"
           :items-per-page-options="[5, 10, 25, 50]"
            v-model:page="pagination.current_page"
            v-model:items-per-page="pagination.per_page"
            @update:page="onPageChange"
            @update:items-per-page="onPerPageChange"
            >
            <template #item.sn="{ index }">
                {{ index + 1 }}
            </template>

            <template #item.created_at="{ item }">
                {{ moment(item.created_at).format("DD MMM YYYY, hh:mm A") }}
            </template>
        </v-data-table-server>
    </v-card>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import { inject } from "vue";
import moment from 'moment';

const permissions = JSON.parse(localStorage.getItem("permissions") || "[]");

const can = (permission) => {
  return permissions.includes(permission);
};


const api = inject("api");

const toast = inject("toast");
const search = ref("");
const paymentsitems = ref([]);
const headers = [
    {
        title: "SN",
        align: "start",
        key: "sn",
    },
    { title: "User", key: "user.name" },
    { title: "Quiz", key: "quiz.title" },
    { title: "Amount", key: "amount" },
    { title: "Status", key: "status" },
    { title: "Date", key: "created_at" },
];
const pagination = ref({
    current_page: 1,
    per_page: 5,
total :0
});

const onPageChange = (page)=> {
    pagination.value.current_page = page;
    fetchPayments();
};

const onPerPageChange = (perPage) => {
    pagination.value.per_page = perPage;
    pagination.value.current_page = 1;
    fetchPayments();
};

const fetchPayments = async () => {
     if (!can("view payment")) {
    toast.value.showToast("You are not authorized to view payment", "error");
    router.push("/payments"); 
    return;
  }
    try {

        const res = await api.get(`/payments/index?page=${pagination.value.current_page}&per_page=${pagination.value.per_page}`);
        paymentsitems.value = res.data.data.data;
        console.log(res.data.data);
        pagination.value.current_page = res.data.data.current_page;
        pagination.value.per_page = res.data.data.per_page;
        pagination.value.total = res.data.data.total;
   } catch (error) {
        toast.value.showToast(error?.response?.data?.message || "Something went wrong!", 'error');
    }
};

onMounted(() => {
    fetchPayments();
});

</script>