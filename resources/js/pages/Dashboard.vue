  <template>
  <v-row>
    <v-col cols="3">
      <v-card class="mx-auto my-8" elevation="16" max-width="344"  @click="goToQuiz" color="#1F7087">

                    <v-card-item>
                        <v-card-title> Total Quiz </v-card-title>
                    </v-card-item>
                    <v-card-text> 5 </v-card-text>


                </v-card>
            </v-col>

    <v-col cols="3">
      <v-card class="mx-auto my-8" elevation="16" max-width="344" @click="goToUsers" color="#1F7087">
        <v-card-item>
          <v-card-title> Total Users </v-card-title>
        </v-card-item>
        <v-card-text> 10 </v-card-text>
      </v-card>
    </v-col>

    <v-col cols="3">
      <v-card class="mx-auto my-8" elevation="16" max-width="344" @click="goToCategory" color="#1F7087">
        <v-card-item>
          <v-card-title> Total Category </v-card-title>
        </v-card-item>
        <v-card-text> 10 </v-card-text>
      </v-card>
    </v-col>
  </v-row>

  <v-card flat>
    <v-card-title class="d-flex align-center pe-2">
      <v-icon icon="mdi-account-group"></v-icon> &nbsp; Recent users

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
      :filter-keys="['name']"
      :items="items"
    >
      <template v-slot:header.stock>
        <div class="text-end">Stock</div>
      </template>

      <template v-slot:item.image="{ item }">
        <v-card class="my-2" elevation="2" rounded>
          <v-img
            :src="`https://cdn.vuetifyjs.com/docs/images/graphics/gpus/${item.image}`"
            height="64"
            cover
          ></v-img>
        </v-card>
      </template>

      <template v-slot:item.rating="{ item }">
        <v-rating
          :model-value="item.rating"
          color="orange-darken-2"
          density="compact"
          size="small"
          readonly
        ></v-rating>
      </template>

      <template v-slot:item.stock="{ item }">
        <div class="text-end">
          <v-chip
            :color="item.stock ? 'green' : 'red'"
            :text="item.stock ? 'In stock' : 'Out of stock'"
            class="text-uppercase"
            size="small"
            label
          ></v-chip>
        </div>
      </template>
    </v-data-table>
  </v-card>
</template>
<script setup>
console.log("Dashboard");

import { ref } from "vue";
import { useRouter } from "vue-router";

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
const items = [
  {
    name: "Nebula GTX 3080",
    image: "1.png",
    price: 699.99,
    rating: 5,
    stock: true,
  },
  {
    name: "Galaxy RTX 3080",
    image: "2.png",
    price: 799.99,
    rating: 4,
    stock: false,
  },


];
</script>

