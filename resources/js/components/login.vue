<template>
    <div class="card card-authentication1 mx-auto my-5">
        <div class="card-body mb-5"">
            <div class=" card-content p-2">
            <div class="text-center">
                <img :src="'/logontt.png'" style="width: 80%;" alt="logo icon">
            </div>
            <div class="card-title text-uppercase text-center py-3">Sign In</div>
            <form method="post" @submit="login">
                <div class="form-group">
                    <label for="exampleInputUsername" class="">Username</label>
                    <div class="position-relative has-icon-right">
                        <input type="text" id="exampleInputUsername" class="form-control input-shadow"
                            placeholder="Enter Username" required v-model="username">
                        <div class="form-control-position">
                            <i class="icon-user"></i>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword" class="">Password</label>
                    <div class="position-relative has-icon-right">
                        <input type="password" id="exampleInputPassword" class="form-control input-shadow"
                            placeholder="Enter Password" required v-model="password">
                        <div class="form-control-position">
                            <i class="icon-lock"></i>
                        </div>
                    </div>
                </div>
                <router-link style="float: right;" :to="{ name: 'forgot_password' }"> Forgot Password</router-link>
                <br>
                <br>
                <button type="submit" :disabled="loading" class="
                                    btn btn-primary
                                    shadow-primary
                                    btn-block
                                    waves-effect waves-light
                                ">
                    <i :class="{
                        'fa fa-spinner fa-spin': loading,
                        'fa fa-sign-in': !loading,
                    }"></i>
                    Login
                </button>

            </form>
        </div>
    </div>
    </div>
</template>

<script>
import errorMessage from '../components/errorMessage.vue'
export default {
    components: {
        errorMessage
    },
    data() {
        return {
            username: '',
            password: '',
            errors: '',
            loading: false
        }
    },
    methods: {
        login(e) {
            e.preventDefault();
            const vm = this;
            this.loading = true;
            if (!this.username) {
                this.$swal({
                    icon: "error",
                    title: "Oops...",
                    text: "Username Cant Be Empty!",
                });
                this.loading = false;
                return;
            }
            if (!this.password) {
                this.$swal({
                    icon: "error",
                    title: "Oops...",
                    text: "Password Cant Be Empty!",
                });
                this.loading = false;
                return;
            }
            vm.globalLoader.show = true;
            $.ajax({
                url: "/api/v1/web/auth/login",
                type: "POST",
                data: {
                    username: this.username,
                    password: this.password,
                },
                success: function (data) {
                    if (data.status === 1) {
                        localStorage.setItem("token", data.data.token);
                        vm.$swal({
                            icon: "success",
                            title: "Login Success",
                            text: "Selamat Datang",
                        });
                        location.href = "/?token=" + localStorage.getItem('token');
                    } else {
                        vm.$swal({
                            icon: "info",
                            title: "Information",
                            text: data.message,
                        });
                        vm.globalLoader.show = false;
                        vm.loading = false;
                    }
                },
                error: function (err) {
                    vm.$swal({
                        icon: "error",
                        title: "Error",
                        text: "Internal Server Error",
                    });
                    vm.globalLoader.show = false;
                    vm.loading = false;
                }
            });
        },
        check_login() {
            const vm = this;
            if (localStorage.getItem('token')) {
                $.ajax({
                    url: "/api/v1/web/auth/login/check",
                    type: "POST",
                    data: {
                        token: localStorage.getItem('token')
                    },
                    success: function (data) {
                        if (data.status === 1) {
                            location.href = "/?token=" + localStorage.getItem('token');
                        }
                        else {
                            vm.globalLoader.show = false;
                            localStorage.removeItem('token');
                        }
                    },
                    error: function (err) {
                        vm.globalLoader.show = false;
                        vm.$swal({
                            icon: "error",
                            title: "Error",
                            text: "Internal Server Error",
                        });
                    }
                });
            }
            else {
                vm.globalLoader.show = false;
            }
        }
    },
    mounted() {
        this.check_login();
    }
}
</script>
