import { createRouter, createWebHistory } from "vue-router";
import Home from "../pages/Home.vue";
import Login from "../pages/Login.vue";
import Register from "../pages/Register.vue";
import MyOrders from "../pages/MyOrders.vue";
import Profile from "../pages/Profile.vue";
import EventDetails from "../pages/EventDetails.vue";
import CreateEvent from "../pages/CreateEvent.vue";
import EditEvent from "../pages/EditEvent.vue";
import Favorites from "../pages/Favorites.vue";


const routes = [
  { path: "/", component: Home },
  { path: "/login", component: Login, meta: { guestOnly: true } },
  { path: "/register", component: Register, meta: { guestOnly: true } },
  {
    path: "/orders",
    component: MyOrders,
    meta: { requiresAuth: true },
  },
  {
    path: "/profile",
    component: Profile,
    meta: { requiresAuth: true },
  },
  {path: "/events/:id", component: EventDetails},
  {path: "/create-event", component: CreateEvent, meta: { requiresAuth: true }},
  {path: "/edit-event/:id", component: EditEvent, meta: { requiresAuth: true }},
  {path: "/favorites", component: Favorites, meta: { requiresAuth: true }},
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// guard
router.beforeEach((to) => {
  const token = localStorage.getItem("token");

  if (to.meta.requiresAuth && !token) {
    return "/login";
  }

  if (to.meta.guestOnly && token) {
    return "/";
  }
});

export default router;
