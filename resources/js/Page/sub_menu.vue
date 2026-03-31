<template>
    <div class="card">
        <div class="card-header">
            <bread-crumb></bread-crumb>
            <h5>Menu Editor <button type="button" class="btn btn-primary" data-toggle="modal"
                    data-target="#addSubMenuGroup" data-backdrop="static" data-keyboard="false">Add
                    New Data</button> </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive" id="table_container">
                <table class="table table-bordered" style="width: 100%" id="tableSubMenu" v-if="!loading">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Order</th>
                            <th>Page</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addSubMenuGroup">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Menu Editor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form :validation-schema="validateRole" @submit="add_sub_menu">
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
                            <label for="input-1">Page Name</label>
                            <v-select class="form-control" placeholder="Select an Icon" :options="listPageName"
                                label="label" :reduce="option => option.value" v-model="page_name"></v-select>
                            <ErrorMessage style="color: red;" name="page_name" />
                        </div>
                        <div class="form-group">
                            <label for="input-1">Menu Group</label>
                            <v-select class="form-control" placeholder="Select an Icon" :options="listMenuGroup"
                                label="label" :reduce="option => option.value" v-model="selectedMenuGroup"></v-select>
                            <ErrorMessage style="color: red;" name="selectedMenuGroup" />
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
    <div class="modal fade" id="editSubMenuGroup">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Menu Editor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="initValue">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form :validation-schema="validateRole" @submit="edit_sub_menu">
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
                            <label for="input-1">Page Name</label>
                            <v-select class="form-control" placeholder="Select an Icon" :options="listPageName"
                                label="label" :reduce="option => option.value" v-model="page_name"></v-select>
                            <ErrorMessage style="color: red;" name="page_name" />
                        </div>
                        <div class="form-group">
                            <label for="input-1">Menu Group</label>
                            <v-select class="form-control" placeholder="Select an Icon" :options="listMenuGroup"
                                label="label" :reduce="option => option.value" v-model="selectedMenuGroup"></v-select>
                            <ErrorMessage style="color: red;" name="selectedMenuGroup" />
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
import { Form, Field, ErrorMessage } from 'vee-validate';
import * as yup from 'yup';
import axios from 'axios';
import swalNotif from '../Utils/swalNotif.js';
import Swal from 'sweetalert2';
import listPageName from '../Utils/listPageName.js';

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
            tableSubMenuGroup: "",
            name: null,
            order_no: null,
            listMenuGroup: [],
            selectedMenuGroup: "",
            listPageName,
            page_name: null,
            id: "",
            validateRole: yup.object({
                name: yup.string().required("Name is required"),
                order_no: yup.number().required("Order Number is required"),
            })
        }
    },
    methods: {
        get_sub_menu() {
            const vm = this;
            this.tableSubMenuGroup = $("#tableSubMenu").DataTable(
                {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "/api/v1/web/menu/editor/get",
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
                        "targets": 4
                    }],
                    rowGroup: {
                        dataSrc: "menu_group",
                        startRender: function (rows, group) {
                            return $('<tr/>')
                                .append(
                                    $('<td/>')
                                        .attr('colspan', 6)
                                        .css({
                                            'background-color': '#f1f1f4',
                                            'font-weight': '600',
                                            'color': '#000'
                                        })
                                        .text(group)
                                );
                        }
                    },
                    columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    }, {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'order_no',
                        name: 'order_no'
                    },
                    {
                        data: 'page_name',
                        name: 'page_name'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                    {
                        data: 'menu_group',
                        name: 'menu_group',
                        visible: false
                    },
                    ]
                }
            );
        },
        getMenuGroup() {
            axios.post("/api/v1/web/menu/editor/group/get", {}, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    this.listMenuGroup = res.data.data.map(item => ({
                        label: item.name,
                        value: item.id
                    }));

                } else {
                    swalNotif.info(res.data.message);
                }
            }).catch(res => {
                swalNotif.error("Terjadi Kesalahan");
            });
        },
        add_sub_menu() {
            if (!this.selectedMenuGroup) {
                swalNotif.error("Menu Group Cant Be Empty!");
                return;
            }
            if (!this.page_name) {
                swalNotif.error("Page Name Cant Be Empty!");
                return;
            }
            this.disabled = true;
            this.globalLoader.show = true;
            const vm = this;
            axios.post("/api/v1/web/menu/editor/add", {
                name: this.name,
                order_no: this.order_no,
                menu_group_id: this.selectedMenuGroup,
                page_name: this.page_name
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    vm.refresh_table();
                    vm.initValue();
                    $("#addSubMenuGroup").modal("hide");
                    swalNotif.success(res.data.message);
                } else {
                    swalNotif.error(res.data.message);
                }
            }).catch(res => {
                console.log(res);

                swalNotif.error("Error Adding Menu Editor!");

            }).finally(function () {
                vm.disabled = false;
                vm.globalLoader.show = false;
            });
        },
        refresh_table() {
            this.tableSubMenuGroup.ajax.reload();
        },
        initValue() {
            this.name = null;
            this.order_no = null;
            this.selectedMenuGroup = "";
            this.id = "";
            this.page_name = "";
        },
        getSubMenuById(id) {
            const vm = this;
            this.globalLoader.show = true;

            axios.post("/api/v1/web/menu/editor/get/id", {
                id: id
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    vm.id = res.data.data.id;
                    vm.name = res.data.data.name;
                    vm.order_no = res.data.data.order_no;
                    vm.selectedMenuGroup = res.data.data.menu_group_id;
                    vm.page_name = res.data.data.page_name;

                    $("#editSubMenuGroup").modal({ backdrop: 'static', keyboard: false });
                }
                else {
                    swalNotif.info(res.data.message);
                }
            }).catch(res => {
                swalNotif.error("Terjadi Kesalahan");
            }).finally(function () {
                vm.globalLoader.show = false;
            });
        },
        edit_sub_menu() {
            if (!this.page_name) {
                swalNotif.error("Page Name Cant Be Empty!");
                return;
            }
            if (!this.selectedMenuGroup) {
                swalNotif.error("Menu Group Cant Be Empty!");
                return;
            }
            this.disabled = true;
            this.globalLoader.show = true;
            const vm = this;
            axios.post("/api/v1/web/menu/editor/edit", {
                id: this.id,
                name: this.name,
                order_no: this.order_no,
                menu_group_id: this.selectedMenuGroup,
                page_name: this.page_name,
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    $("#editSubMenuGroup").modal("hide");
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
        delete_sub_menu(id, ctx) {
            const vm = this;
            ctx.attr('disabled', true);
            axios.post("/api/v1/web/menu/editor/delete", {
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
                text: "This Action Will Delete All Data Related To This Sub Menu!",
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
                    vm.delete_sub_menu(id, ctx);
                }
            });
        }
    },
    mounted() {
        const vm = this;
        this.loading = false;
        setTimeout(() => {
            this.get_sub_menu();
            this.getMenuGroup();

            $("#tableSubMenu").on('click', '.btnEdit', function () {
                const id = this.id;
                vm.getSubMenuById(id);
            });
            $("#tableSubMenu").on('click', '.btnDelete', function () {
                const id = this.id;
                const ctx = $(this);
                vm.confimDelete(id, ctx);
            });
        }, 1);
    }
}
</script>
