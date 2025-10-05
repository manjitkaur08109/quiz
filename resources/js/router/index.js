import { createRouter, createWebHistory } from "vue-router";
import Dashboard from "../pages/Dashboard.vue";
import Quiz from "../pages/Quiz.vue";
import Users from "../pages/Users.vue";
import Profile from "../pages/Profile.vue";


const routes = [
  { path: "/", name: "Dashboard", component: Dashboard },
  { path: "/quiz", name: "Quiz", component: Quiz },
  { path: "/users", name: "Users", component: Users },
  {path:"/profile",name:"Profile",component:Profile}
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
