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
import MyLearning from "../pages/MyLearning.vue";
import Discover from "../pages/Discover.vue";

const routes = [
  { path: "/", name: "Dashboard", component: Dashboard , meta: { requiresAuth: true } },
  { path: "/users", name: "Users", component: Users , meta: { requiresAuth: true } },
  {path:"/profile",name:"Profile",component:Profile, meta: { requiresAuth: true } },
  { path: "/quiz", name: "Quiz", component: Quiz , meta: { requiresAuth: true } },
  { path:"/addQuiz", name:"AddQuiz", component:AddQuiz, meta: { requiresAuth: true }  },
  { path: "/editQuiz/:id", name: "EditQuiz", component: EditQuiz , meta: { requiresAuth: true } },
  {path:"/category",name:"Category",component:Category, meta: { requiresAuth: true } },
  { path: "/addCategory", name: "AddCategory", component: AddCategory , meta: { requiresAuth: true } },
  { path: "/editCategory/:id", name: "EditCategory", component: EditCategory, meta: { requiresAuth: true } },
  { path: "/register", name: "Registration", component: Registration ,meta: { guest: true }},
  { path: "/login", name: "Login", component: Login, meta: { guest: true } },
  { path: "/discover", name: "Discover", component: Discover, meta: { requiresAuth: true } },
  { path: "/myLearning", name: "MyLearning", component: MyLearning, meta: { requiresAuth: true } },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem("token");

  if (to.meta.requiresAuth && !token) {
    // Agar protected page aur login nahi hai
    next("/login");
  } else if (to.meta.guest && token) {
    // Agar login ho aur login/register page open karna hai
    next("/");
  } else {
    next();
  }
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
