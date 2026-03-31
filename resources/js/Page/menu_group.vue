<template>
    <div class="card">
        <div class="card-header">
            <bread-crumb></bread-crumb>
            <h5>Menu Group <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addMenuGroup"
                    data-backdrop="static" data-keyboard="false">Add
                    New Data</button> </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive" id="table_container">
                <table class="table table-bordered" style="width: 100%" id="tableMenu" v-if="!loading">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Order</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addMenuGroup">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Menu Group</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form :validation-schema="validateRole" @submit="add_menu">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="input-1">Name *</label>
                            <Field name="name" type="text" class="form-control" id="input-1" placeholder="Name *"
                                v-model="name"></Field>
                            <ErrorMessage style="color: red;" name="name" />
                        </div>
                        <div class="form-group">
                            <label for="input-1">Order No *</label>
                            <Field name="order_no" type="number" class="form-control" id="input-1"
                                placeholder="Order No *" v-model="order_no"></Field>
                            <ErrorMessage style="color: red;" name="order_no" />
                        </div>
                        <div class="form-group">
                            <label for="input-1">Icon</label>
                            <v-select class="form-control" placeholder="Select an Icon" :options="listIcons"
                                label="label" :reduce="option => option.value" v-model="selectedIcon">
                                <template v-slot:option="{ label }">
                                    <div v-html="label" />
                                </template>

                                <!-- Template for the value displayed when selected -->
                                <template v-slot:selected-option="{ label }">
                                    <div v-html="label" />
                                </template>
                            </v-select>
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

    <div class="modal fade" id="editMenuGroup">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Menu Group</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="initValue">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form :validation-schema="validateRole" @submit="edit_menu">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="input-1">Name *</label>
                            <Field name="name" type="text" class="form-control" id="input-1" placeholder="Name *"
                                v-model="name"></Field>
                            <ErrorMessage style="color: red;" name="name" />
                        </div>
                        <div class="form-group">
                            <label for="input-1">Order No *</label>
                            <Field name="order_no" type="number" class="form-control" id="input-1"
                                placeholder="Order No *" v-model="order_no"></Field>
                            <ErrorMessage style="color: red;" name="order_no" />
                        </div>
                        <div class="form-group">
                            <label for="input-1">Icon</label>
                            <v-select class="form-control" placeholder="Select an Icon" :options="listIcons"
                                label="label" :reduce="option => option.value" v-model="selectedIcon">
                                <template v-slot:option="{ label }">
                                    <div v-html="label" />
                                </template>

                                <!-- Template for the value displayed when selected -->
                                <template v-slot:selected-option="{ label }">
                                    <div v-html="label" />
                                </template>
                            </v-select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-inverse-primary" data-dismiss="modal" @click="initValue"><i
                                class="fa fa-times"></i>
                            Close</button>
                        <button type="submit" class="btn btn-primary" :disabled="disabled"><i :class="{
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
import icons from '../Utils/all-icons.js';
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
            tableMenuGroup: "",
            icons,
            listIcons: [{
                label: "SELECT AN ICON",
                value: ""
            },
            ...icons.map(item => ({
                label: `<i class="${item.class}"></i>   ${item.name}`,
                value: item.class
            }))],
            name: null,
            order_no: null,
            selectedIcon: "",
            id: "",
            validateRole: yup.object({
                name: yup.string().required("Name is required"),
                order_no: yup.number().required("Order Number is required"),
            })
        }
    },
    methods: {
        get_menu() {
            const vm = this;
            this.tableMenuGroup = $("#tableMenu").DataTable(
                {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "/api/v1/web/menu/group/get",
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
                        "targets": 3
                    }],
                    columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    }, {
                        data: 'menuName',
                        name: 'menuName'
                    },
                    {
                        data: 'order_no',
                        name: 'order_no'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                    ]
                }
            );
        },
        add_menu() {
            if (!this.selectedIcon) {
                swalNotif.error("Icon Cant Be Empty!");
                return;
            }
            this.disabled = true;
            this.globalLoader.show = true;
            const vm = this;
            axios.post("/api/v1/web/menu/group/add", {
                name: this.name,
                order_no: this.order_no,
                icon: this.selectedIcon
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    vm.refresh_table();
                    vm.initValue();
                    $("#addMenuGroup").modal("hide");
                    swalNotif.success(res.data.message);
                } else {
                    swalNotif.error(res.data.message);
                }
            }).catch(res => {
                swalNotif.error("Error Adding Menu Group!");

            }).finally(function () {
                vm.disabled = false;
                vm.globalLoader.show = false;
            });
        },
        refresh_table() {
            this.tableMenuGroup.ajax.reload();
        },
        initValue() {
            this.icons = "";
            this.name = null;
            this.order_no = null;
            this.id = "";
            this.selectedIcon = "";
        },
        getMenuById(id) {
            const vm = this;
            this.globalLoader.show = true;

            axios.post("/api/v1/web/menu/group/get/id", {
                id: id
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    vm.name = res.data.data.name;
                    vm.order_no = res.data.data.order_no;
                    vm.selectedIcon = res.data.data.icon;
                    vm.id = res.data.data.id;

                    $("#editMenuGroup").modal({ backdrop: 'static', keyboard: false });
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
        edit_menu() {
            if (!this.selectedIcon) {
                swalNotif.error("Icon Cant Be Empty!");
                return;
            }
            this.disabled = true;
            this.globalLoader.show = true;
            const vm = this;
            axios.post("/api/v1/web/menu/group/edit", {
                name: this.name,
                order_no: this.order_no,
                icon: this.selectedIcon,
                id: this.id
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    $("#editMenuGroup").modal("hide");
                    vm.refresh_table();
                    vm.initValue();
                    swalNotif.success(res.data.message);
                } else {
                    swalNotif.error(res.data.message);
                }
            }).catch(res => {
                swalNotif.error("Error Adding Menu Group!");

            }).finally(function () {
                vm.disabled = false;
                vm.globalLoader.show = false;
            });
        },
        delete_menu(id, ctx) {
            const vm = this;
            ctx.attr('disabled', true);
            axios.post("/api/v1/web/menu/group/delete", {
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
                text: "This Action Will Delete All Data Related To This Menu!",
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
                    vm.delete_menu(id, ctx);
                }
            });
        }
    },
    mounted() {
        const vm = this;
        this.loading = false;
        setTimeout(() => {
            this.get_menu();

            $("#tableMenu").on('click', '.btnEdit', function () {
                const id = this.id;
                vm.getMenuById(id);
            });
            $("#tableMenu").on('click', '.btnDelete', function () {
                const id = this.id;
                const ctx = $(this);
                vm.confimDelete(id, ctx);
            });
        }, 1);
    }
}
</script>
