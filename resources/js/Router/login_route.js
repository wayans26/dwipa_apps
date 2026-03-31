import {
    createRouter,
    createWebHistory
} from "vue-router"


import login from "../components/login.vue"
import forgot_password from "../Page/forgot_password.vue";
import forgot_password_change from "../Page/forgot_password_change.vue";

const routes = [{
    path: "/auth/login",
    name: "login",
    component: login
}, {
    path: "/auth/forgot/password",
    name: "forgot_password",
    component: forgot_password
}, {
    path: "/auth/forgot/password/change",
    name: "forgot_password_change",
    component: forgot_password_change
}, ];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
