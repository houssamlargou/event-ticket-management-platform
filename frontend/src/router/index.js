import { createRouter, createWebHistory } from "vue-router";
import Home from "../pages/Home.vue";
import EventDetails from "../pages/EventDetails.vue";
import Login from "../pages/Login.vue";
import Register from "../pages/Register.vue";
import MyOrders from "../pages/MyOrders.vue";

const routes = [
    { path: "/", component: Home },
    { path: "/event/:id", component: EventDetails },
    { path: "/login", component: Login },
    { path: "/register", component: Register },
    { path: "/orders", component: MyOrders },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;