import {
    createApp
} from 'vue';
import master from '../Page/index_login.vue'
import router from "../Router/login_route";
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import {
    reactive
} from 'vue';

const app = createApp(master);

const globalLoader = app.config.globalProperties.globalLoader = reactive({
    show: true
});
app.use(router);
app.use(VueSweetalert2);
app.mount('#app');
