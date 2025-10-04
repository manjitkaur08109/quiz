import {createRouter,createWEbHistory} from 'vue-router'
import Login from './Components/Login.vue'

const routes =
    {name:'Login',component:Login}

 const router = createRouter({
  history: createWebHistory(),
  router,
})
export default router
