<template>
    <div class="card card-authentication1 mx-auto my-5">
        <div class="card-body">
            <router-link :to="{ name: 'login' }" class="btn btn-outline-dark btn-sm waves-effect waves-light"> <i
                    class="fa fa-arrow-left"></i> Back</router-link>
            <div class="card-content p-2">
                <div class="text-center">
                    <img :src="'/logontt.png'" style="width: 50%;" alt="logo icon">
                </div>
                <div class="card-title text-uppercase text-center py-3">Reset Password</div>
                <Form @submit="forgot_password" :validation-schema="validate">
                    <div class="form-group">
                        <label for="input-1">Email</label>
                        <div class="position-relative has-icon-right">
                            <Field name="email" type="email" class="form-control input-shadow" id="input-1"
                                placeholder="Email Address" v-model="email">
                            </Field>
                            <div class="form-control-position">
                                <i class="icon-user"></i>
                            </div>
                        </div>
                        <ErrorMessage style="color: red;" name="email" />
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
            email: '',
            loading: false,
            validate: yup.object({
                email: yup.string().email("Email is invalid").required("Email is required"),
            }),
        }
    },
    methods: {
        forgot_password() {
            const vm = this;
            this.loading = true;
            vm.globalLoader.show = true;
            axios.post("/api/v1/web/auth/reset/password", {
                email: this.email
            }).then(res => {
                if (res.data.status === 1) {
                    swalNotif.success(res.data.message);
                    vm.$router.push({ name: 'login' });
                } else {
                    swalNotif.info(res.data.message);
                }
            }).catch(err => {
                swalNotif.error("Failed to reset password!");
            }).finally(() => {
                this.loading = false;
                vm.globalLoader.show = false;
            });
        }
    },
    mounted() {
        this.globalLoader.show = false;
    }
}
</script>
