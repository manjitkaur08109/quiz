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
import Registration from "../pages/Registration.vue";
import Login from "../pages/Login.vue";

const routes = [
  { path: "/", name: "Dashboard", component: Dashboard },
  { path: "/users", name: "Users", component: Users },
  {path:"/profile",name:"Profile",component:Profile},
  { path: "/quiz", name: "Quiz", component: Quiz },
  { path:"/addQuiz", name:"AddQuiz", component:AddQuiz },
  { path: "/editQuiz/:id", name: "EditQuiz", component: EditQuiz },
  {path:"/category",name:"Category",component:Category},
  { path: "/addCategory", name: "AddCategory", component: AddCategory },
  { path: "/editCategory/:id", name: "EditCategory", component: EditCategory },
  { path: "/register", name: "Registration", component: Registration },
  { path: "/login", name: "Login", component: Login },
  { path: "/category", redirect: "/category" },
  { path: "/quiz", redirect: "/quiz" },
    // { path: "/quiz", name: "Quiz", component: Quiz, meta: { requiresAuth: true } },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// router.beforeEach((to, from, next) => {
//   const token = localStorage.getItem("token");

//   if (to.meta.requiresAuth && !token) {
//     if (to.path !== "/login") next("/login");
//     else next();
//   } else if ((to.path === "/login" || to.path === "/register") && token) {
//     next("/");
//   } else {
//     next();
//   }
// });

export default router;
