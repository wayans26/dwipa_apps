<template>
    <div class="card">
        <div class="card-header">
            <bread-crumb></bread-crumb>
            <h5>Role <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addRole"
                    data-backdrop="static" data-keyboard="false">Add
                    New Data</button> </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive" id="table_container">
                <table class="table table-bordered" style="width: 100%" id="tableRole" v-if="!loading">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Role Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addRole">
        <div class="modal-dialog modal-lg" style="max-width: 95%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Role</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form :validation-schema="validateRole" @submit="addRole">
                    <div class="modal-body" style="max-height: 75vh;overflow-y: scroll;">
                        <div class="form-group">
                            <label for="input-1">Role Name *</label>
                            <Field name="role_name" type="text" class="form-control" id="input-1"
                                placeholder="Role Name *" v-model="role_name"></Field>
                            <ErrorMessage style="color: red;" name="role_name" />
                        </div>
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
                                    <template v-for="(item, index) in list_sub_menu" :key="index">
                                        <tr v-if="index == 0 || list_sub_menu[index - 1].menu_group != item.menu_group"
                                            style="background-color: #eafff0;color: black;">
                                            <td colspan="7">{{ item.menu_group }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ index + 1 }}
                                            </td>
                                            <td>{{ item.name }}</td>
                                            <td style="text-align: center;">
                                                <input type="checkbox" :id="item.id" :value="item.allow_view"
                                                    class="checkbox-custom" v-model="list_sub_menu[index].allow_view" />
                                            </td>
                                            <td style="text-align: center;">
                                                <input type="checkbox" :id="item.id" :value="item.allow_create"
                                                    class="checkbox-custom"
                                                    v-model="list_sub_menu[index].allow_create" />
                                            </td>
                                            <td style="text-align: center;">
                                                <input type="checkbox" :id="item.id" :value="item.allow_update"
                                                    class="checkbox-custom"
                                                    v-model="list_sub_menu[index].allow_update" />
                                            </td>
                                            <td style="text-align: center;">
                                                <input type="checkbox" :id="item.id" :value="item.allow_delete"
                                                    class="checkbox-custom"
                                                    v-model="list_sub_menu[index].allow_delete" />
                                            </td>
                                            <td style="text-align: center;">
                                                <input type="checkbox" :id="item.id" :value="item.allow_print"
                                                    class="checkbox-custom"
                                                    v-model="list_sub_menu[index].allow_print" />
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-inverse-primary" data-dismiss="modal"><i
                                class="fa fa-times"></i>
                            Close</button>
                        <button type="submit" class="btn btn-primary" :disabled="disabled"><i :class="{
                            'fa fa-spinner fa-spin': disabled,
                            'fa fa-plus': !disabled,
                        }"></i>
                            Add</button>
                    </div>
                </Form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editRole">
        <div class="modal-dialog modal-lg" style="max-width: 95%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Role</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="initValue">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form :validation-schema="validateRole" @submit="editRole">
                    <div class="modal-body" style="max-height: 75vh;overflow-y: scroll;">
                        <div class="form-group">
                            <label for="input-1">Role Name *</label>
                            <Field :disabled="isRoleDisable" name="role_name" type="text" class="form-control"
                                id="input-1" placeholder="Role Name *" v-model="role_name"></Field>
                            <ErrorMessage style="color: red;" name="role_name" />
                        </div>
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
                                    <template v-for="(item, index) in list_sub_menu" :key="index">
                                        <tr v-if="index == 0 || list_sub_menu[index - 1].menu_group != item.menu_group"
                                            style="background-color: #eafff0;color: black;">
                                            <td colspan="7">{{ item.menu_group }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ index + 1 }}
                                            </td>
                                            <td>{{ item.name }}</td>
                                            <td style="text-align: center;">
                                                <input :disabled="isRoleDisable" type="checkbox" :id="item.id"
                                                    class="checkbox-custom" :value="item.allow_view"
                                                    v-model="list_sub_menu[index].allow_view" />
                                            </td>
                                            <td style="text-align: center;">
                                                <input :disabled="isRoleDisable" type="checkbox" :id="item.id"
                                                    class="checkbox-custom" :value="item.allow_create"
                                                    v-model="list_sub_menu[index].allow_create" />
                                            </td>
                                            <td style="text-align: center;">
                                                <input :disabled="isRoleDisable" type="checkbox" :id="item.id"
                                                    class="checkbox-custom" :value="item.allow_update"
                                                    v-model="list_sub_menu[index].allow_update" />
                                            </td>
                                            <td style="text-align: center;">
                                                <input :disabled="isRoleDisable" type="checkbox" :id="item.id"
                                                    class="checkbox-custom" :value="item.allow_delete"
                                                    v-model="list_sub_menu[index].allow_delete" />
                                            </td>
                                            <td style="text-align: center;">
                                                <input :disabled="isRoleDisable" type="checkbox" :id="item.id"
                                                    class="checkbox-custom" :value="item.allow_print"
                                                    v-model="list_sub_menu[index].allow_print" />
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-inverse-primary" data-dismiss="modal" @click="initValue"><i
                                class="fa fa-times"></i>
                            Close</button>
                        <button v-if="!isRoleDisable" type="submit" class="btn btn-primary" :disabled="disabled"><i
                                :class="{
                                    'fa fa-spinner fa-spin': disabled,
                                    'fa fa-edit': !disabled,
                                }"></i>
                            Save</button>
                    </div>
                </Form>
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
            role_name: null,
            isRoleDisable: false,
            list_sub_menu: [],
            tableRole: "",
            id: "",
            validateRole: yup.object({
                role_name: yup.string().required("Role Name is required"),
            })
        }
    },
    methods: {
        getRole() {
            const vm = this;
            this.tableRole = $("#tableRole").DataTable(
                {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "/api/v1/web/role/get",
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
                        "targets": 2
                    }],
                    columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    }, {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    }
                    ]
                }
            );
        },
        addRole() {
            this.disabled = true;
            this.globalLoader.show = true;
            const vm = this;
            axios.post("/api/v1/web/role/add", {
                role_name: this.role_name,
                permission: this.list_sub_menu
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    vm.refresh_table();
                    vm.initValue();
                    $("#addRole").modal("hide");
                    swalNotif.success(res.data.message);
                } else {
                    swalNotif.error(res.data.message);
                }
            }).catch(res => {
                swalNotif.error("Error Adding Role!");
            }).finally(function () {
                vm.disabled = false;
                vm.globalLoader.show = false;
            });
        },
        refresh_table() {
            this.tableRole.ajax.reload();
        },
        initValue() {
            this.role_name = null;
            this.list_sub_menu.forEach((item, index) => {
                this.list_sub_menu[index].allow_view = "false";
                this.list_sub_menu[index].allow_create = "false";
                this.list_sub_menu[index].allow_update = "false";
                this.list_sub_menu[index].allow_delete = "false";
                this.list_sub_menu[index].allow_print = "false";
            });
            this.lst_group = "";
            this.id = "";
            this.isRoleDisable = false;
        },
        getListSubMenu() {
            const vm = this;
            this.globalLoader.show = true;

            axios.post("/api/v1/web/role/menu/get", {
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    vm.list_sub_menu = res.data.data.map(item => ({
                        id: item.id,
                        name: item.name,
                        allow_view: "false",
                        allow_create: "false",
                        allow_update: "false",
                        allow_delete: "false",
                        allow_print: "false",
                        menu_group: item.menu_group
                    }));
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
        getRoleById(id) {
            const vm = this;
            this.globalLoader.show = true;

            axios.post("/api/v1/web/role/menu/get/id", {
                id: id
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    vm.id = res.data.data.role.id;
                    vm.role_name = res.data.data.role.role_name;
                    vm.list_sub_menu = res.data.data.permission.map(item => ({
                        id: item.id,
                        name: item.name,
                        allow_view: item.allow_view === 1 ? "true" : "false",
                        allow_create: item.allow_create === 1 ? "true" : "false",
                        allow_update: item.allow_update === 1 ? "true" : "false",
                        allow_delete: item.allow_delete === 1 ? "true" : "false",
                        allow_print: item.allow_print === 1 ? "true" : "false",
                        menu_group: item.menu_group
                    }));

                    $("#editRole").modal({ backdrop: 'static', keyboard: false });
                }
                else {
                    notification.notif_error(res.data.message);
                }
            }).catch(res => {
                notification.notif_error("Terjadi Kesalahan");
            }).finally(function () {
                vm.globalLoader.show = false;
            });
        },
        editRole() {
            this.disabled = true;
            this.globalLoader.show = true;
            const vm = this;
            axios.post("/api/v1/web/role/edit", {
                id: this.id,
                role_name: this.role_name,
                permission: this.list_sub_menu
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    $("#editRole").modal("hide");
                    vm.refresh_table();
                    vm.initValue();
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
        },
        deleteRole(id, ctx) {
            const vm = this;
            ctx.attr('disabled', true);
            axios.post("/api/v1/web/role/delete", {
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
                text: "This Action Will Delete Role!",
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
                    vm.deleteRole(id, ctx);
                }
            });
        },
    },
    mounted() {
        const vm = this;
        this.loading = false;
        setTimeout(() => {
            this.getRole();
            this.getListSubMenu();

            $("#tableRole").on('click', '.btnEdit', function () {
                const id = this.id;
                vm.getRoleById(id);
            });
            $("#tableRole").on('click', '.btnDelete', function () {
                const id = this.id;
                const ctx = $(this);
                vm.confimDelete(id, ctx);
            });
            $("#tableRole").on('click', '.btnView', function () {
                const id = this.id;
                const ctx = $(this);
                vm.getRoleById(id);
                vm.isRoleDisable = true;

            });
        }, 1);
    }
}
</script>
