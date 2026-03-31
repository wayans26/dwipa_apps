<template>
    <div class="card" v-show="page === 'view'">
        <div class="card-header">
            <bread-crumb></bread-crumb>
            <h5>User Manager
                <button type="button" class="btn btn-primary" style="float: right;" @click="changePage('add')">Add New
                    Data</button>
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive" id="table_container">
                <table class="table table-bordered" style="width: 100%" id="tableUserManager" v-if="!loading">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Username</th>
                            <th>Fullname</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card" v-show="page === 'add' || page === 'edit'">
        <Form :validation-schema="validateRole" @submit="submitForm">
            <div class="card-header">
                <h5>User Manager</h5>
                <div style="text-align-last: right;">
                    <button type="button" class="btn btn-light" @click="changePage('view')">
                        <i class="fa fa-arrow-left"></i>
                    </button>
                    <button v-if="!isEdit && page === 'add'" type="button"
                        class="btn btn-outline-danger waves-effect waves-light mr-1" @click="changePage('view')">
                        Cancle
                    </button>
                    <button v-if="isEdit && page === 'edit'" type="button"
                        class="btn btn-outline-danger waves-effect waves-light mr-1" @click="isEdit = false"> Cancle
                    </button>
                    <button v-if="!isEdit && page === 'edit'" type="button"
                        class="btn btn-success waves-effect waves-light mr-1" @click="isEdit = true">
                        Edit Data
                    </button>
                    <button v-if="!isEdit && page === 'edit'" type="button" class="btn btn-primary"
                        @click="changePage('add')">Add</button>
                    <button v-if="isEdit && page === 'edit' || page === 'add'" type="submit" class="btn btn-primary"
                        :disabled="disabled"><i :class="{
                            'fa fa-spinner fa-spin': disabled,
                            'fa fa-plus': !disabled,
                        }"></i>
                        Save</button>
                </div>
            </div>
            <div class="card-body">
                <button type="button" :class="{
                    'btn': true,
                    'btn-outline-primary': !isSubActive('general'),
                    'btn-primary': isSubActive('general'),
                    'ml-1': true
                }" @click="changeSubPage('general')">General</button>
                <button type="button" :class="{
                    'btn': true,
                    'btn-outline-primary': !isSubActive('role'),
                    'btn-primary': isSubActive('role'),
                    'ml-1': true
                }" @click="changeSubPage('role')">Access Right</button>
                <br><br>
                <div class="card" v-show="subPage === 'general'">
                    <div class="card-header">
                        <h5>Profile Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Username *</label>
                            <Field name="username" type="text" class="form-control" :disabled="!isEdit"
                                placeholder="Username *" v-model="username">
                            </Field>
                            <ErrorMessage style="color: red;" name="username" />
                        </div>
                        <div class="form-group">
                            <label>Full Name *</label>
                            <Field name="fullName" type="text" class="form-control" :disabled="!isEdit"
                                placeholder="Full Name *" v-model="fullName">
                            </Field>
                            <ErrorMessage style="color: red;" name="fullName" />
                        </div>
                        <div class="form-group">
                            <label>Phone Number *</label>
                            <Field name="phoneNumber" type="text" class="form-control" :disabled="!isEdit"
                                placeholder="Phone Number *" v-model="phoneNumber">
                            </Field>
                            <ErrorMessage style="color: red;" name="phoneNumber" />
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <Field name="email" type="text" class="form-control" placeholder="Email *"
                                :disabled="!isEdit" v-model="email">
                            </Field>
                            <ErrorMessage style="color: red;" name="email" />
                        </div>
                        <div class="form-group">
                            <label>Role *</label>
                            <v-select class="form-control" placeholder="Select an Role" :options="listRole"
                                :disabled="!isEdit" label="label" :reduce="option => option.value"
                                v-model="selectedRole">
                            </v-select>
                        </div>
                    </div>
                </div>

                <div class="card" v-show="subPage === 'role'">
                    <div class="card-header">
                        <h5>ROLE : {{ roleName }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" id="table_container">
                            <table class="table table-bordered" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Form Name</th>
                                        <th>Allow Open</th>
                                        <th>Allow Create</th>
                                        <th>Allow Update</th>
                                        <th>Allow Delete</th>
                                        <th>Allow Print</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in userPermission" :key="index">
                                        <td>{{ index + 1 }}
                                        </td>
                                        <td>{{ item.name }}</td>
                                        <td style="text-align: center;">
                                            <input type="checkbox" :id="item.id" disabled :checked="item.allow_view"
                                                class="checkbox-custom" />
                                        </td>
                                        <td style="text-align: center;">
                                            <input type="checkbox" :id="item.id" disabled :checked="item.allow_create"
                                                class="checkbox-custom" />
                                        </td>
                                        <td style="text-align: center;">
                                            <input type="checkbox" :id="item.id" disabled :checked="item.allow_update"
                                                class="checkbox-custom" />
                                        </td>
                                        <td style="text-align: center;">
                                            <input type="checkbox" :id="item.id" disabled :checked="item.allow_delete"
                                                class="checkbox-custom" />
                                        </td>
                                        <td style="text-align: center;">
                                            <input type="checkbox" :id="item.id" disabled :checked="item.allow_print"
                                                class="checkbox-custom" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </Form>
        <div class="card" v-show="subPage === 'general' && page === 'edit'">
            <div class="card-header">Sign-In Method</div>
            <div class="card-body">
                <div class="row" v-if="!isResetPassword">
                    <div class="col-lg-6">
                        <p>Password</p>
                        <span>*********</span>
                    </div>
                    <div class="col-lg-6" style="text-align: right;">
                        <button type="button" class="btn btn-dark" @click="isResetPassword = true">Reset
                            Password</button>
                    </div>
                </div>
                <div v-else>
                    <Form :validation-schema="validateResetPassword" @submit="resetPassword">
                        <div class="form-group">
                            <label>Password *</label>
                            <Field name="password" type="password" class="form-control" placeholder="Password *"
                                v-model="password">
                            </Field>
                            <ErrorMessage style="color: red;" name="password" />
                        </div>
                        <div class="form-group">
                            <label>Confirm Password *</label>
                            <Field name="confirmPassword" type="password" class="form-control"
                                placeholder="Confirm Password *" v-model="confirmPassword">
                            </Field>
                            <ErrorMessage style="color: red;" name="confirmPassword" />
                        </div>
                        <button @click="isResetPassword = false" type="button" class="btn btn-light mr-2"
                            :disabled="disabled"><i :class="{
                                'fa fa-spinner fa-spin': disabled,
                                'fa fa-close': !disabled,
                            }"></i>
                            Cancel</button>

                        <button type="submit" class="btn btn-primary" :disabled="disabled"><i :class="{
                            'fa fa-spinner fa-spin': disabled,
                            'fa fa-plus': !disabled,
                        }"></i>
                            Save</button>
                    </Form>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import { Form, Field, ErrorMessage } from 'vee-validate';
import * as yup from 'yup';
import axios from 'axios';
import swalNotif from '../Utils/swalNotif.js';
import Swal from 'sweetalert2';

export default {
    components: {
        Form,
        Field,
        ErrorMessage
    },
    data() {
        return {
            disabled: false,
            loading: true,
            tableUserManager: "",
            page: "view",
            subPage: "general",
            isEdit: false,
            isResetPassword: false,
            id: "",
            userPermission: [],
            listRole: [],
            listDepartment: [],
            listEmployee: [],
            employee_id: "-",
            selectedRole: "",
            roleName: "",
            selectedDepartment: "",
            username: "",
            fullName: "",
            phoneNumber: "",
            email: "",
            allowMobile: "true",
            previewImage: "/avatar.jpg",
            image: "",
            imageChange: false,
            allowType: ["jpg", "jpeg", "png"],
            password: "",
            confirmPassword: "",
            validateRole: yup.object({
                username: yup.string().required("Username is required"),
                fullName: yup.string().required("Full Name is required"),
                phoneNumber: yup.string().required("Phone Number is required").matches("^[0-9+\-]+$", "Phone Number is invalid"),
                email: yup.string().email("Email is invalid").required("Email is required"),
            }),
            validateResetPassword: yup.object({
                password: yup.string().required("Password is required"),
                confirmPassword: yup.string().required("Confirm Password is required").oneOf([yup.ref('password'), null], 'Passwords must match'),
            })
        }
    },
    methods: {
        get_user_manager() {
            const vm = this;
            this.tableUserManager = $("#tableUserManager").DataTable(
                {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "/api/v1/web/user/manager/get",
                        headers: {
                            token: localStorage.getItem('token')
                        },

                    },
                    pageLength: 25,
                    "columnDefs": [{
                        "width": "2%",
                        "targets": 0
                    }, {
                        "width": "2%",
                        "targets": 5
                    }],
                    columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    }, {
                        data: 'username',
                        name: 'username'
                    },
                    {
                        data: 'full_name',
                        name: 'full_name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'role_name',
                        name: 'role_name'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                    ]
                }
            );
        },
        changePage(page) {
            this.page = page;
            this.initValue();
            if (page === 'view') {
                this.refresh_table();
            }
            if (page === 'add') {
                this.isEdit = true;
            }
        },
        changeSubPage(page) {
            const vm = this;
            this.subPage = page;
        },
        isSubActive(page) {
            return this.subPage === page;
        },
        getAditionalData() {
            this.globalLoader.show = true;
            const vm = this;
            axios.post("/api/v1/web/user/manager/data/get", {

            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    vm.listRole = res.data.data.role.map(item => ({
                        label: item.role_name,
                        value: item.id
                    }));
                } else {
                    swalNotif.error(res.data.message);
                }
            }).catch(res => {
                swalNotif.error("Error Getting Data!");

            }).finally(function () {
                vm.globalLoader.show = false;
            });
        },
        selectImage(e) {
            const tmpFile = e.target.files[0];
            if (!tmpFile) {
                return;
            }
            if (tmpFile.size > 5120000) {
                swalNotif.info("Image size must be less than 5MB");
                this.$refs.fileImage.value = null;
                this.image = "";
                return;
            }
            if (tmpFile.name.split('.').pop() && !this.allowType.includes(tmpFile.name.split('.').pop().toLowerCase())) {
                swalNotif.info("Image type must be JPG, JPEG, PNG");
                this.$refs.fileImage.value = null;
                this.image = "";
                return
            }
            this.imageChange = true;
            this.image = e.target.files[0];
            this.previewImage = URL.createObjectURL(e.target.files[0]);
        },
        addUserManager() {
            if (!this.selectedRole) {
                swalNotif.error("Role Cant Be Empty!");
                return;
            }

            this.disabled = true;
            this.globalLoader.show = true;
            let frmData = new FormData();
            const vm = this;

            frmData.append('image', vm.image);
            frmData.append('username', vm.username);
            frmData.append('full_name', vm.fullName);
            frmData.append('phone', vm.phoneNumber);
            frmData.append('email', vm.email);
            frmData.append('role', vm.selectedRole);

            axios.post("/api/v1/web/user/manager/add", frmData, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    vm.refresh_table();
                    vm.initValue();
                    vm.isEdit = false;
                    vm.getUserManagerById(res.data.data.id);
                    swalNotif.success("Defaul Password : 123456");
                } else {
                    swalNotif.error(res.data.message);
                }
            }).catch(res => {
                swalNotif.error("Error Adding User!");

            }).finally(function () {
                vm.disabled = false;
                vm.globalLoader.show = false;
            });
        },
        submitForm() {
            if (this.page == 'add') {
                this.addUserManager();
            }
            else if (this.page == 'edit') {
                this.editUserManager();
            }
        },
        refresh_table() {
            this.tableUserManager.ajax.reload();
        },
        initValue() {
            this.subPage = "general";
            this.selectedRole = "";
            this.selectedDepartment = "";
            this.username = "";
            this.fullName = "";
            this.phoneNumber = "";
            this.email = "";
            this.previewImage = "/avatar.jpg";
            this.image = "";
            this.imageChange = false;
            this.id = "";
            this.roleName = "";
            this.userPermission = [];
        },
        getUserManagerById(id) {
            const vm = this;
            this.globalLoader.show = true;

            axios.post("/api/v1/web/user/manager/get/id", {
                id: id
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    vm.changePage('edit');
                    vm.username = res.data.data.user.username;
                    vm.previewImage = res.data.data.user.image === null ? "/avatar.jpg" : "/image/user/get/" + res.data.data.user.image;
                    vm.fullName = res.data.data.user.full_name;
                    vm.phoneNumber = res.data.data.user.phone;
                    vm.email = res.data.data.user.email;
                    vm.selectedRole = res.data.data.user.role_id;
                    vm.selectedDepartment = res.data.data.user.department_id;
                    vm.roleName = res.data.data.role.role_name;
                    vm.userPermission = res.data.data.permission;
                    vm.id = res.data.data.user.id;
                }
                else {
                    swalNotif.error(res.data.message);
                }
            }).catch(res => {
                swalNotif.error("Terjadi Kesalahan");
            }).finally(function () {
                vm.globalLoader.show = false;
            });
        },
        editUserManager() {
            if (!this.selectedRole) {
                swalNotif.error("Role Cant Be Empty!");
                return;
            }
            this.disabled = true;
            this.globalLoader.show = true;
            let frmData = new FormData();
            const vm = this;
            frmData.append('id', vm.id);
            frmData.append('image', vm.image);
            frmData.append('username', vm.username);
            frmData.append('full_name', vm.fullName);
            frmData.append('phone', vm.phoneNumber);
            frmData.append('email', vm.email);
            frmData.append('role', vm.selectedRole);
            axios.post("/api/v1/web/user/manager/edit", frmData, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    vm.refresh_table();
                    vm.initValue();
                    vm.getUserManagerById(res.data.data.id);
                    vm.isEdit = false;
                    swalNotif.success(res.data.message);
                } else {
                    swalNotif.error(res.data.message);
                }
            }).catch(res => {
                swalNotif.error("Error Editing User!");

            }).finally(function () {
                vm.disabled = false;
                vm.globalLoader.show = false;
            });
        },
        deleteUserManager(id, ctx) {
            const vm = this;
            ctx.attr('disabled', true);
            axios.post("/api/v1/web/user/manager/delete", {
                id: id
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    vm.refresh_table();
                    vm.initValue();
                    swalNotif.success(res.data.message);
                } else {
                    swalNotif.error(res.data.message);
                }
            }).catch(res => {
                swalNotif.error(res.response.data.message);
            }).finally(function () {
                ctx.attr('disabled', false);
            });

        },
        confimDelete(id, ctx) {
            const vm = this;
            Swal.fire({
                icon: "warning",
                title: "Warning",
                allowOutsideClick: false,
                allowEscapeKey: false,
                text: "This Action Will Delete User, You Won't Be Able To Revert This!",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                showCancelButton: true,
                didOpen: () => {
                    Swal.showLoading();
                    setTimeout(() => { Swal.hideLoading() }, 500)
                }
            }).then((result) => {
                $(".confirm").attr('disabled', 'disabled');
                if (result.isConfirmed) {
                    vm.deleteUserManager(id, ctx);
                }
            });
        },
        resetPassword() {
            const vm = this;
            this.disabled = true;
            this.globalLoader.show = true;
            if (!this.id) {
                swalNotif.error("User Not Found!");
                return;
            }
            axios.post("/api/v1/web/user/manager/password/change", {
                id: vm.id,
                password: vm.password,
                confirm_password: vm.confirmPassword
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    vm.password = "";
                    vm.confirmPassword = "";
                    vm.isResetPassword = false;
                    swalNotif.success(res.data.message);
                } else {
                    swalNotif.error(res.data.message);
                }
            }).catch(res => {
                swalNotif.error(res.response.data.message);
            }).finally(function () {
                vm.disabled = false;
                vm.globalLoader.show = false;
            });
        }
    },
    mounted() {
        const vm = this;
        this.loading = false;
        setTimeout(() => {
            this.get_user_manager();
            this.getAditionalData();
            $("#tableUserManager").on('click', '.btnEdit', function () {
                const id = this.id;
                vm.getUserManagerById(id);
                vm.isEdit = true;
            });
            $("#tableUserManager").on('click', '.btnView', function () {
                const id = this.id;
                vm.getUserManagerById(id);
                vm.isEdit = false;
            });
            $("#tableUserManager").on('click', '.btnDelete', function () {
                const id = this.id;
                const ctx = $(this);
                vm.confimDelete(id, ctx);
            });
        }, 1);
    }
}
</script>
