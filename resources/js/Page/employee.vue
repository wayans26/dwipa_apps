<template>
    <div class="card">
        <div class="card-header">
            <bread-crumb></bread-crumb>
            <h5>Employee <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addEmployee"
                    data-backdrop="static" data-keyboard="false">Add
                    New Data</button> </h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered" style="width: 100%" id="tableEmployee" v-if="!loading">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Job Title</th>
                        <th>Department</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="addEmployee">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Employee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form :validation-schema="validateRole" @submit="add_employee">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="input-1">Name *</label>
                            <Field name="name" type="text" class="form-control" id="input-1" placeholder="Name *"
                                v-model="name"></Field>
                            <ErrorMessage style="color: red;" name="name" />
                        </div>
                        <div class="form-group">
                            <label for="input-1">Address</label>
                            <Field name="address" type="text" class="form-control" id="input-1" placeholder="Address"
                                v-model="address"></Field>
                            <ErrorMessage style="color: red;" name="address" />
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="input-1">Phone</label>
                                    <Field name="phone" type="text" class="form-control" id="input-1"
                                        placeholder="Phone" v-model="phone"></Field>
                                    <ErrorMessage style="color: red;" name="phone" />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="input-1">Mobile </label>
                                    <Field name="mobile" type="text" class="form-control" id="input-1"
                                        placeholder="Mobile " v-model="mobile"></Field>
                                    <ErrorMessage style="color: red;" name="mobile" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="input-1">Email</label>
                            <Field name="email" type="text" class="form-control" id="input-1" placeholder="Email"
                                v-model="email"></Field>
                            <ErrorMessage style="color: red;" name="email" />
                        </div>
                        <div class="form-group">
                            <label for="input-1">Job Title</label>
                            <Field name="job_title" type="text" class="form-control" id="input-1"
                                placeholder="Job Title" v-model="job_title"></Field>
                            <ErrorMessage style="color: red;" name="job_title" />
                        </div>
                        <div class="form-group"">
                            <label for=" input-1">Department</label>
                            <v-select class="form-control" placeholder="Select an Department" :options="listDepartment"
                                :clearable="false" label="label" :reduce="option => option.value" v-model="department">
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

    <div class="modal fade" id="editEmployee">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Employee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="initValue">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form :validation-schema="validateRole" @submit="edit_employee">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="input-1">Name *</label>
                            <Field name="name" type="text" class="form-control" id="input-1" placeholder="Name *"
                                :disabled="!isEdit" v-model="name"></Field>
                            <ErrorMessage style="color: red;" name="name" />
                        </div>
                        <div class="form-group">
                            <label for="input-1">Address</label>
                            <Field name="address" type="text" class="form-control" id="input-1" placeholder="Address"
                                :disabled="!isEdit" v-model="address"></Field>
                            <ErrorMessage style="color: red;" name="address" />
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="input-1">Phone</label>
                                    <Field name="phone" type="text" class="form-control" id="input-1"
                                        placeholder="Phone" :disabled="!isEdit" v-model="phone"></Field>
                                    <ErrorMessage style="color: red;" name="phone" />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="input-1">Mobile </label>
                                    <Field name="mobile" type="text" class="form-control" id="input-1"
                                        placeholder="Mobile " :disabled="!isEdit" v-model="mobile"></Field>
                                    <ErrorMessage style="color: red;" name="mobile" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="input-1">Email</label>
                            <Field name="email" type="text" class="form-control" id="input-1" placeholder="Email"
                                :disabled="!isEdit" v-model="email"></Field>
                            <ErrorMessage style="color: red;" name="email" />
                        </div>
                        <div class="form-group">
                            <label for="input-1">Job Title</label>
                            <Field name="job_title" type="text" class="form-control" id="input-1"
                                placeholder="job Title" :disabled="!isEdit" v-model="job_title"></Field>
                            <ErrorMessage style="color: red;" name="job_title" />
                        </div>
                        <div class="form-group"">
                            <label for=" input-1">Department</label>
                            <v-select class="form-control" placeholder="Select an Department" :options="listDepartment"
                                label="label" :reduce="option => option.value" v-model="department" :disabled="!isEdit">
                            </v-select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-inverse-primary" data-dismiss="modal" @click="initValue"><i
                                class="fa fa-times"></i>
                            Close</button>
                        <button v-if="isEdit" type="submit" class="btn btn-primary" :disabled="disabled"><i :class="{
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
            tableEmployee: "",
            listDepartment: [],
            name: "",
            address: "",
            phone: "",
            mobile: "",
            email: "",
            job_title: "",
            department: "",
            id: "",
            isEdit: false,
            validateRole: yup.object({
                name: yup.string().required("Name is required"),
                address: yup.string().nullable(),
                phone: yup.string().nullable().matches("^[0-9+\\- ]*$", "Phone Number is invalid"),
                mobile: yup.string().nullable().matches("^[0-9+\\- ]*$", "Mobile Number is invalid"),
                email: yup.string().nullable().email("Email is invalid"),
                job_title: yup.string().nullable(),
            })
        }
    },
    methods: {
        get_employee() {
            const vm = this;
            this.tableEmployee = $("#tableEmployee").DataTable(
                {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "/api/v1/web/employee/get",
                        headers: {
                            token: localStorage.getItem('token')
                        },

                    },
                    fixedHeader: {
                        header: true,
                    },
                    scrollY: '60vh',
                    scrollCollapse: true,
                    scrollX: true,
                    pageLength: 25,
                    "columnDefs": [{
                        "width": "2%",
                        "targets": 0
                    }, {
                        "width": "2%",
                        "targets": 8
                    }],
                    columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'address',
                        name: 'address'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'mobile',
                        name: 'mobile'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'job_title',
                        name: 'job_title'
                    },
                    {
                        data: 'department_name',
                        name: 'department_name'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                    ]
                }
            );
        },
        add_employee() {
            const vm = this;
            if (!this.department) {
                swalNotif.error("Please select a department");
                return;
            }
            this.disabled = true;
            this.globalLoader.show = true;
            axios.post("/api/v1/web/employee/add", {
                name: this.name,
                address: this.address,
                phone: this.phone,
                mobile: this.mobile,
                email: this.email,
                job_title: this.job_title,
                department_id: this.department,
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    vm.refresh_table();
                    vm.initValue();
                    $("#addEmployee").modal("hide");
                    swalNotif.success(res.data.message);
                } else {
                    swalNotif.error(res.data.message);
                }
            }).catch(res => {
                swalNotif.error("Error Adding Employee!");

            }).finally(function () {
                vm.disabled = false;
                vm.globalLoader.show = false;
            });
        },
        get_department() {
            this.disabled = true;
            this.globalLoader.show = true;
            const vm = this;
            axios.post("/api/v1/web/employee/department/get", {}, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    vm.listDepartment = res.data.data.map(item => ({
                        label: item.name,
                        value: item.id
                    }));
                }
            }).catch(res => {
                swalNotif.error("Error Get Department!");

            }).finally(function () {
                vm.disabled = false;
                vm.globalLoader.show = false;
            });
        },
        refresh_table() {
            this.tableEmployee.ajax.reload();
        },
        initValue() {
            this.id = "";
            this.name = "";
            this.address = "";
            this.phone = "";
            this.mobile = "";
            this.contact = "";
            this.email = "";
            this.job_title = "";
            this.department = "";
            this.isEdit = false;
        },
        get_employee_by_id(id) {
            const vm = this;
            this.globalLoader.show = true;

            axios.post("/api/v1/web/employee/get/id", {
                id: id
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    vm.name = res.data.data.name;
                    vm.address = res.data.data.address;
                    vm.phone = res.data.data.phone;
                    vm.mobile = res.data.data.mobile;
                    vm.email = res.data.data.email;
                    vm.job_title = res.data.data.job_title;
                    vm.department = res.data.data.department_id;
                    vm.id = res.data.data.id;

                    $("#editEmployee").modal({ backdrop: 'static', keyboard: false });
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
        edit_employee() {
            this.disabled = true;
            this.globalLoader.show = true;
            const vm = this;
            axios.post("/api/v1/web/employee/edit", {
                name: this.name,
                address: this.address,
                phone: this.phone,
                mobile: this.mobile,
                email: this.email,
                job_title: this.job_title,
                department_id: this.department,
                id: this.id
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    $("#editEmployee").modal("hide");
                    vm.refresh_table();
                    vm.initValue();
                    swalNotif.success(res.data.message);
                } else {
                    swalNotif.error(res.data.message);
                }
            }).catch(res => {
                swalNotif.error("Error Editing Employee!");

            }).finally(function () {
                vm.disabled = false;
                vm.globalLoader.show = false;
            });
        },
        delete_employee(id, ctx) {
            const vm = this;
            ctx.attr('disabled', true);
            axios.post("/api/v1/web/employee/delete", {
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
                text: "This Action Will Delete Employee!",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                showCancelButton: true,
                didOpen: () => {
                    Swal.showLoading();
                    setTimeout(() => { Swal.hideLoading() }, 1000)
                }
            }).then((result) => {
                $(".confirm").attr('disabled', 'disabled');
                if (result.isConfirmed) {
                    vm.delete_employee(id, ctx);
                }
            });
        }
    },
    mounted() {
        const vm = this;
        this.loading = false;
        setTimeout(() => {
            this.get_employee();
            this.get_department();

            $("#tableEmployee").on('click', '.btnEdit', function () {
                const id = this.id;
                vm.get_employee_by_id(id);
                vm.isEdit = true;
            });

            $("#tableEmployee").on('click', '.btnView', function () {
                const id = this.id;
                vm.get_employee_by_id(id);
                vm.isEdit = false;
            });
            $("#tableEmployee").on('click', '.btnDelete', function () {
                const id = this.id;
                const ctx = $(this);
                vm.confimDelete(id, ctx);
            });
        }, 1);
    }
}
</script>
