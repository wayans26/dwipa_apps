<template>
    <div id="sidebar-wrapper" data-simplebar="" data-simplebar-auto-hide="true">
        <div class="brand-logo">
            <img :src="'/logontt.png'" class="logo-icon" alt="logo icon" />
            <h5 class="logo-text">GEM</h5>
        </div>
        <ul class="sidebar-menu do-nicescrol">
            <li class="sidebar-header">MAIN NAVIGATION</li>
            <li :class="{ active: isActive('exhibitions') }">
                <router-link :to="{ name: 'exhibitions' }" class="waves-effect">
                    <i class="fa fa-industry"></i> <span>Exhibitions</span>
                </router-link>
            </li>
            <li :class="{ active: isActive('schedule') }">
                <router-link :to="{ name: 'schedule' }" class="waves-effect">
                    <i class="fa fa-calendar-check-o"></i> <span>Schedule</span>
                </router-link>
            </li>
            <li :class="{ active: isActive('company') }">
                <router-link :to="{ name: 'company' }" class="waves-effect">
                    <i class="fa fa-building"></i> <span>Company</span>
                </router-link>
            </li>
            <li :class="{ active: isActive('exhibitor') }">
                <router-link :to="{ name: 'exhibitor' }" class="waves-effect">
                    <i class="fa fa-address-card-o"></i> <span>Exhibitor</span>
                </router-link>
            </li>
            <li :class="{ active: isActive('contract') }">
                <router-link :to="{ name: 'contract' }" class="waves-effect">
                    <i class="fa fa-file"></i> <span>Contract</span>
                </router-link>
            </li>
            <li :class="{ active: isActiveParent('product') }">
                <a href="#" class="waves-effect">
                    <i class="fa fa-navicon"></i> <span>Product</span>
                    <i class="fa fa-angle-left float-right"></i>
                </a>
                <ul class="sidebar-submenu">
                    <li :class="{ active: isActive('productCategory') }">
                        <router-link :to="{ name: 'productCategory' }" class="waves-effect">
                            <i class="fa fa-cube"></i> <span>Product Category</span>
                        </router-link>
                    </li>
                    <li :class="{ active: isActive('product') }">
                        <router-link :to="{ name: 'product' }" class="waves-effect">
                            <i class="fa fa-cubes"></i> <span>Product Item</span>
                        </router-link>
                    </li>
                </ul>
            </li>
            <li :class="{ active: isActiveParent('rentItem') }">
                <a href="#" class="waves-effect">
                    <i class="fa fa-navicon"></i> <span>Rent</span>
                    <i class="fa fa-angle-left float-right"></i>
                </a>
                <ul class="sidebar-submenu">
                    <li :class="{ active: isActive('rentItemCategory') }">
                        <router-link :to="{ name: 'rentItemCategory' }" class="waves-effect">
                            <i class="fa fa-arrow-right"></i> <span>Rent Category</span>
                        </router-link>
                    </li>
                    <li :class="{ active: isActive('rentItem') }">
                        <router-link :to="{ name: 'rentItem' }" class="waves-effect">
                            <i class="fa fa-arrow-right"></i> <span>Rent Item</span>
                        </router-link>
                    </li>
                </ul>
            </li>
            <li :class="{ active: isActiveParent('advertising') }">
                <a href="#" class="waves-effect">
                    <i class="fa fa-navicon"></i> <span>Advertising</span>
                    <i class="fa fa-angle-left float-right"></i>
                </a>
                <ul class="sidebar-submenu">
                    <li :class="{ active: isActive('advertisingCategory') }">
                        <router-link :to="{ name: 'advertisingCategory' }" class="waves-effect">
                            <i class="fa fa-buysellads"></i> <span>Advertising Category</span>
                        </router-link>
                    </li>
                    <li :class="{ active: isActive('advertising') }">
                        <router-link :to="{ name: 'advertising' }" class="waves-effect">
                            <i class="fa fa-buysellads"></i> <span>Advertising Item</span>
                        </router-link>
                    </li>
                </ul>
            </li>
            <li :class="{ active: isActive('tempStaf') }">
                <router-link :to="{ name: 'tempStaf' }" class="waves-effect">
                    <i class="zmdi zmdi-accounts"></i> <span>Temporary Staf</span>
                </router-link>
            </li>
            <li :class="{ active: isActive('password') }">
                <router-link :to="{ name: 'password' }" class="waves-effect">
                    <i class="zmdi zmdi-key"></i> <span>Change Password</span>
                </router-link>
            </li>
            <li :class="{ active: isActive('export') }">
                <router-link :to="{ name: 'export' }" class="waves-effect">
                    <i class="zmdi zmdi-file"></i> <span>File</span>
                </router-link>
            </li>
            <li :class="{ active: isActiveParent('file') }">
                <a href="#" class="waves-effect">
                    <i class="fa fa-files-o"></i> <span>File Urls</span>
                    <i class="fa fa-angle-left float-right"></i>
                </a>
                <ul class="sidebar-submenu">
                    <li :class="{ active: isActive('fileExhibitions') }">
                        <router-link :to="{ name: 'fileExhibitions' }" class="waves-effect">
                            <i class="fa fa-arrow-right"></i> <span>Exhibitions File</span>
                        </router-link>
                    </li>
                    <li :class="{ active: isActive('fileStand') }">
                        <router-link :to="{ name: 'fileStand' }" class="waves-effect">
                            <i class="fa fa-arrow-right"></i> <span>Space Only Stand File</span>
                        </router-link>
                    </li>
                </ul>
            </li>
            <li>
                <a @click="logout" href="javascript:void(0)" class="waves-effect"><i class="fa fa-power-off"></i>
                    <span>Logout</span></a>
            </li>
        </ul>
    </div>
</template>

<script>
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
        isActiveParent(routeName) {
            return this.$route.meta.parent === routeName;
        },
    },
};
</script>
