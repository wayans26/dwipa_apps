<template>
    <div>
        <div class="table-responsive" v-if="!loader">
            <table class="table table-striped" style="width: 100%;border: solid 2px; padding: 8px;">
                <thead class="thead-primary">
                    <tr>
                        <th style="width: 2%;">#</th>
                        <th style="width: 78%;">Order Number</th>
                        <th style="width: 20%;text-align: center;">Order Summary</th>
                    </tr>
                </thead>
                <tbody v-if="order.length > 0">
                    <tr v-for="(item, index) in order" :key="index">
                        <td style="">{{ index + 1 }}</td>
                        <td style="">{{ item.idorder }}</td>
                        <td style="text-align: center;">
                            <router-link :to="{ name: 'detailOrder', params: { idorder: item.idorder } }"
                                class="btn btn-primary"> <i class="zmdi zmdi-eye"></i>
                            </router-link>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td colspan="2" style="text-align: center;">No Data</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <content-loader width="100%" height="250" v-else></content-loader>
    </div>
</template>

<script>
import {
    ContentLoader,
} from 'vue-content-loader';
import notification from '../Utils/notification';

export default {
    components: {
        ContentLoader
    },
    props: ['typeRent'],
    data() {
        return {
            order: [],
            loader: true
        }
    },
    methods: {
        getOrder() {
            const vm = this;
            $.ajax({
                url: "/api/v1/user/order/get",
                type: "POST",
                headers: {
                    token: localStorage.getItem('token'),
                    cmd: "Rent"
                },
                data: {
                    type: vm.typeRent
                },
                success: function (data) {
                    vm.order = data.data;
                },
                error: function (err) {
                    notification.notif_error("Internal Server Error");
                },
                complete: function () {
                    vm.loader = false;
                }
            });
        }
    },
    mounted() {
        this.getOrder();
    }
}
</script>
