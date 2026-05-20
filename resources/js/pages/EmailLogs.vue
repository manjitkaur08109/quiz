<template>
    <v-card>
        <v-card-title class="d-flex align-center pe-2">
            Email Logs
            <v-spacer></v-spacer>

            <v-text-field v-model="search" @update:modelValue="onSearch" density="compact" label="Search"
                prepend-inner-icon="mdi-magnify" variant="solo-filled" :flat="true" :hide-details="true"
                :single-line="true" />
        </v-card-title>
        <v-divider></v-divider>
        <v-data-table-server :headers="headers" :items="emailitems" :items-length="pagination.total"
            v-model:page="pagination.current_page" v-model:items-per-page="pagination.per_page"
            :items-per-page-options="[5, 10, 25, 50]"
            @update:page="onPageChange" @update:items-per-page="onPerPageChange">

            <template #item.sn="{ index }">
                {{ index + 1 }}
            </template>
            <template #item.created_at="{ item }">
                {{ moment(item.created_at).format("DD-MM-YYYY") }}
            </template>

            <template #item.status="{ item }">
                <v-chip size="small" :color="item.status === 'success'
                        ? 'green'
                        : item.status === 'pending'
                            ? 'orange'
                            : 'red'
                    ">
                    {{ item.status }}
                </v-chip>
            </template>

            <template #item.actions="{ item }">
                <v-btn class="ml-2" icon="mdi-information" size="x-small" color="success" @click="infoDialog(item)">
                    <v-icon>mdi-information</v-icon>
                </v-btn>
                <v-btn class="ml-2" icon="mdi-delete" size="x-small" color="red" @click.stop="deleteEmail(item.id)" />

                <v-dialog v-model="info_dialog" max-width="650">
                    <v-card>
                        <v-card-title>Email Information</v-card-title>
                        <v-divider></v-divider>

                        <v-card-text v-if="selectedEmail">
                            <p><strong>subject:</strong> {{ selectedEmail.subject }}</p>
                            <p>
                                <strong>Message:</strong> {{ selectedEmail.message }}
                            </p>
                            <p>
                                <strong>Created At:</strong>
                                {{ moment(selectedEmail.created_at).format("DD MMM YYYY, hh:mm A") }}
                            </p>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="primary" text @click="infoDialogClose">
                                Close
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </template>
        </v-data-table-server>
    </v-card>
</template>
<script setup>
import { ref, onMounted, reactive } from 'vue';
import { inject } from "vue";
import moment from 'moment';
const api = inject("api");
const search = ref("");
const emailitems = ref([]);
const info_dialog = ref(false);
const loading = ref(false);
const selectedEmail = ref(null);
const headers = [
    { title: "SN", value: "sn" },
    { title: "Subject", value: "subject" },
    { title: "Status", value: "status" },
    { title: "Date", value: "created_at" },
    { title: "Actions", value: "actions", sortable: false },

];

const onPageChange = (page) => {
    pagination.value.current_page = page;
    getEmailLogs();
};

const onPerPageChange = (perPage) => {
    pagination.value.per_page = perPage;
    pagination.value.current_page = 1;
    getEmailLogs();
};

const onSearch = () => {
    pagination.value.current_page = 1;
    getEmailLogs();
};

const pagination = ref({
    current_page: 1,
    per_page: 5,
    total: 0,
    
});


const getEmailLogs = async () => {
    loading.value = true;
    try {
        const res = await api.get(
            `/email_logs/index?page=${pagination.value.current_page}&per_page=${pagination.value.per_page}&search=${search.value}`
        );

        emailitems.value = res.data.data.data;

        pagination.value.current_page = res.data.data.current_page;
        pagination.value.per_page = res.data.data.per_page;
        pagination.value.total = res.data.data.total;
    } catch (e) {
        console.log(e);
    } finally {
        loading.value = false;
    }
};

const deleteEmail = async (id) => {
    if (!confirm("Delete this email?")) return;
    try {
        await api.delete(`/email_logs/delete/${id}`);
        emailitems.value = emailitems.value.filter((u) => u.id !== id);
        toast.value.showToast("Email deleted successfully", 'success');
    } catch (error) {
        console.error("Error deleting Email:", error.response?.data || error);
        toast.value.showToast(error?.response?.data?.message || "Something went wrong!", 'error');
    }

}

const infoDialog = async (item) => {
    selectedEmail.value = item;
    info_dialog.value = true;
}
const infoDialogClose = () => {
    selectedEmail.value = null;
    info_dialog.value = false;
}

onMounted(() => {
    getEmailLogs(1);
})
</script>