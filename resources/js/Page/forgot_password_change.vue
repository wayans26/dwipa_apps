<template>
    <div class="card card-authentication1 mx-auto my-5">
        <div class="card-body">
            <div class="card-content p-2">
                <div class="text-center">
                    <img :src="'/logontt.png'" style="width: 50%;" alt="logo icon">
                </div>
                <div class="card-title text-uppercase text-center py-3">Reset Password</div>
                <Form @submit="changePassword" :validation-schema="validate">
                    <div class="form-group">
                        <label for="input-1">New Password</label>
                        <div class="position-relative has-icon-right">
                            <Field name="password" type="password" class="form-control input-shadow" id="input-1"
                                placeholder="New Password" v-model="password">
                            </Field>
                            <div class="form-control-position">
                                <i class="icon-lock"></i>
                            </div>
                        </div>
                        <ErrorMessage style="color: red;" name="password" />
                    </div>
                    <div class="form-group">
                        <label for="input-1">Confirm New Password</label>
                        <div class="position-relative has-icon-right">
                            <Field name="confirm_password" type="password" class="form-control input-shadow"
                                id="input-1" placeholder="Confirm New Password" v-model="confirm_password">
                            </Field>
                            <div class="form-control-position">
                                <i class="icon-lock"></i>
                            </div>
                        </div>
                        <ErrorMessage style="color: red;" name="confirm_password" />
                    </div>
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
                        Reset Password
                    </button>
                </Form>
            </div>
        </div>
    </div>
</template>

<script>
import { Form, Field, ErrorMessage } from 'vee-validate';
import * as yup from 'yup';
import swalNotif from '../Utils/swalNotif.js';
import axios from 'axios';
export default {
    components: {
        Form,
        Field,
        ErrorMessage
    },
    data() {
        return {
            password: '',
            confirm_password: '',
            token: this.$route.query.token,
            loading: false,
            validate: yup.object({
                password: yup.string().required("New Password is required").min(6, "Password must be at least 6 characters"),
                confirm_password: yup.string().required("Confirm New Password is required").min(6, "Password must be at least 6 characters").oneOf([yup.ref("password"), null], "Passwords must match"),
            }),
        }
    },
    methods: {
        changePassword() {
            const vm = this;
            this.loading = true;
            vm.globalLoader.show = true;
            axios.post("/api/v1/web/auth/reset/password/change", {
                password: this.password,
                confirm_password: this.confirm_password,
                token: this.token
            }).then(res => {
                if (res.data.status === 1) {
                    swalNotif.success(res.data.message);
                    vm.$router.push({ name: 'login' });
                } else {
                    swalNotif.info(res.data.message);
                }
            }).catch(err => {
                swalNotif.error("Failed to Change password!");
            }).finally(() => {
                this.loading = false;
                vm.globalLoader.show = false;
            });
        },
        tokenCheck() {
            const vm = this;
            vm.globalLoader.show = true;
            axios.post("/api/v1/web/auth/reset/password/check", {
                token: this.token
            }).then(res => {
                if (res.data.status === 1) {
                    vm.globalLoader.show = false;
                } else {
                    swalNotif.error(res.data.message);
                    vm.$router.push({ name: 'login' });
                }
            }).catch(err => {
                vm.$router.push({ name: 'login' });
            });
        }
    },
    mounted() {
        this.tokenCheck();
    }
}
</script>
