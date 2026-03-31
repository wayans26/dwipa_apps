<template>
    <div id="sidebar-wrapper" data-simplebar="" data-simplebar-auto-hide="true">
        <div class="brand-logo">
            <img :src="'/logontt.png'" style="width: 19% !important;" class="logo-icon" alt="logo icon" />
            <h5 class="logo-text">Visitor</h5>
        </div>
        <ul class="sidebar-menu do-nicescrol">
            <li class="sidebar-header">MAIN NAVIGATION</li>
            <li v-for="(item, index) in globalNavigation" :key="index" :class="{ active: isActiveParent(item) }">
                <a href="#" class="waves-effect">
                    <i :class="item.icon"></i> <span>{{ item.parent }}</span>
                    <i class="fa fa-angle-left float-right"></i>
                </a>
                <ul class="sidebar-submenu">
                    <li v-for="(sub, indexsub) in item.child" :key="index" :class="{ active: isActive(sub.page_name) }">
                        <router-link :to="{ name: sub.page_name }" class="waves-effect">
                            <i class="fa fa-arrow-right"></i> <span>{{ sub.name }}</span>
                        </router-link>
                    </li>
                </ul>

            </li>
            <li>
                <a @click="confimLogout" href="javascript:void(0)" class="waves-effect"><i class="fa fa-power-off"></i>
                    <span>Logout</span></a>
            </li>
        </ul>
    </div>
</template>

<script>
import Swal from 'sweetalert2';

export default {
    created() { },
    data() {
        return {
            items: [],
            user: {},
        };
    },
    methods: {
        logout() {
            localStorage.removeItem('token');
            location.href = '/auth/login';
        },
        isActive(routeName) {
            return this.$route.name === routeName;
        },
        isActiveParent(item) {
            return Array.isArray(item?.child) && item.child.some((c) => c.page_name === this.$route.name);
        },
        isMandatoryPage(routeName) {
            const mandatory = [
                'welcome'
            ];

            return mandatory.includes(this.$route.name);
        },
        confimLogout() {
            const vm = this;
            Swal.fire({
                icon: "warning",
                title: "Warning",
                allowOutsideClick: false,
                allowEscapeKey: false,
                text: "Are you sure you want to logout!",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "Yes!",
                cancelButtonText: "No, cancel!",
                showCancelButton: true,
                didOpen: () => {
                    Swal.showLoading();
                    setTimeout(() => { Swal.hideLoading() }, 1000)
                }
            }).then((result) => {
                $(".confirm").attr('disabled', 'disabled');
                if (result.isConfirmed) {
                    vm.logout();
                }
            });
        }
    }
};
</script>
