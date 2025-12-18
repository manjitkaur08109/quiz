<template>
    <v-card>
        <v-card-title class="d-flex align-center pe-2">
            Payments</v-card-title>
        <v-divider></v-divider>
        <v-data-table v-model:search="search" :items="paymentsitems" :headers="headers">
            <template #item.sn="{ index }">
                {{ index + 1 }}
            </template>

            <template #item.created_at="{ item }">
                {{ moment(item.created_at).format("DD MMM YYYY, hh:mm A") }}
            </template>
        </v-data-table>
    </v-card>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import { inject } from "vue";
import moment from 'moment';

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

const fetchPayments = async () => {
    try {

        const res = await api.get("/payments/index");
        paymentsitems.value = res.data.data;
    } catch (error) {
        toast.value.showToast(error?.response?.data?.message || "Something went wrong!", 'error');
    }
};

onMounted(() => {
    fetchPayments();
});

</script>