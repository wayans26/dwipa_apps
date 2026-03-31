import {
    createRouter,
    createWebHistory
} from "vue-router"

const prefix = "/user";

const routes = [{
        path: prefix + "/redirect",
        name: "welcome",
        component: () => import("../Page/welcome.vue"),
        meta: {
            parent: "welcome",
        }
    },
    {
        path: prefix + "/menu/group",
        name: "menu_group",
        component: () => import("../Page/menu_group.vue"),
        meta: {
            parent: "menu_group",

        }
    }, {
        path: prefix + "/sub/menu/group",
        name: "sub_menu",
        component: () => import("../Page/sub_menu.vue"),
        meta: {
            parent: "sub_menu",
        }
    }, {
        path: prefix + "/role",
        name: "role",
        component: () => import("../Page/role.vue"),
        meta: {
            parent: "role",
        }
    }, {
        path: prefix + "/user/manager",
        name: "user_manager",
        component: () => import("../Page/user_manager.vue"),
        meta: {
            parent: "user_manager",
        }
    },
    {
        path: "/:pathMatch(.*)*",
        name: "notFound",
        component: () => import("../components/notFound.vue"),
        meta: {
            parent: "notFound",
        }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
