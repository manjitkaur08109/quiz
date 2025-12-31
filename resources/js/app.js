import { createApp } from 'vue';
import App from './App.vue';

import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import router from "./router";
import { aliases, mdi } from 'vuetify/iconsets/mdi'
import '@mdi/font/css/materialdesignicons.css'

// import "vue3-editor/dist/style.css";
import VueEditor from "vue3-editor";

import api from "./plugins/api";
import Toast from "./components/Toast.vue"; // or your toast store


import './echo';
const vuetify = createVuetify({
    components,
    directives,
    icons: {
        defaultSet: 'mdi',
        aliases,
        sets: { mdi },
    },
})

createApp(App)
    .provide("api", api)
    .use(vuetify)
    .use(router)
    .component("VueEditor", VueEditor)
    .mount('#app');
