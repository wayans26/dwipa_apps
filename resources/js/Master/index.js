import {
    createApp
} from 'vue';
import master from '../Page/index.vue'
import router from "../Router/index_route.js"

import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

import {
    VueDatePicker
} from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

import vSelect from 'vue-select'
import "vue-select/dist/vue-select.css";
import {
    reactive
} from 'vue';
import axios from 'axios';

import notification from '../Utils/notification.js';
import breadCrumb from '../components/breadCrumb.vue';

import print from 'vue3-print-nb'

function roleCheck(parent) {
    globalLoader.show = true;
    return new Promise((resolve) => {
        axios.post("/api/v1/web/auth/user/check", {
            page: parent
        }, {
            headers: {
                token: localStorage.getItem("token")
            }
        }).then((res) => {
            resolve(res.data);
            globalLoader.show = false;
        }).catch((err) => {
            notification.notif_error("Invalid Session, Please Login Again");
            globalLoader.show = false;
            resolve(err);
        });
    });
}

function getNavigation() {
    return new Promise((resolve) => {
        axios.post("/api/v1/web/user/navigation/get", {}, {
            headers: {
                token: localStorage.getItem("token")
            }
        }).then((res) => {
            resolve(res.data.data);
        }).catch((err) => {
            resolve(err);
        });
    });
}

const app = createApp(master);
const globalLoader = app.config.globalProperties.globalLoader = reactive({
    show: true
});

const globalBreadCrumb = app.config.globalProperties.globalBreadcrumb = reactive({
    menu_group: "",
    menu_sub_group: ""
});

app.config.globalProperties.globalNavigation = await getNavigation();
app.config.globalProperties.firstPage = app.config.globalProperties.globalNavigation[0].child[0].page_name;

router.beforeEach(async (to, from, next) => {
    let role = await roleCheck(to.meta.parent);
    if (role.data) {
        globalBreadCrumb.menu_group = role.data.menu_group;
        globalBreadCrumb.menu_sub_group = role.data.menu_sub;
    }
    if (role) {
        if (role.status === 401) {
            location.href = "/auth/login";
        }
    }
    next();
});


app.use(router);
app.use(VueSweetalert2);
app.use(print);
app.component('VueDatePicker', VueDatePicker);
app.component('v-select', vSelect);
app.component('breadCrumb', breadCrumb);
app.mount('#app');
