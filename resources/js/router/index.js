import { createRouter, createWebHistory } from "vue-router";
import Dashboard from "../pages/Dashboard.vue";
import Quiz from "../pages/Quiz.vue";
import Users from "../pages/Users.vue";
import Profile from "../pages/Profile.vue";
import Category from "../pages/Category.vue";
import AddQuiz from "../pages/AddQuiz.vue";
import EditQuiz from "../pages/EditQuiz.vue";
import AddCategory from "../pages/AddCategory.vue";
import EditCategory from "../pages/EditCategory.vue";

const routes = [
  { path: "/", name: "Dashboard", component: Dashboard },
  { path: "/quiz", name: "Quiz", component: Quiz },
  { path: "/users", name: "Users", component: Users },
  {path:"/profile",name:"Profile",component:Profile},
  {path:"/category",name:"Category",component:Category},
{ path:"/addQuiz", name:"AddQuiz", component:AddQuiz },
  { path: "/editQuiz/:id", name: "EditQuiz", component: EditQuiz },
  { path: "/addCategory", name: "AddCategory", component: AddCategory },
  { path: "/editCategory/:id", name: "EditCategory", component: EditCategory }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
