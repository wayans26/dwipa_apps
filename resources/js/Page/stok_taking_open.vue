<template>
    <div class="card">
        <div class="card-header">
            <bread-crumb></bread-crumb>
            <h5>Stock Taking Detail
                <div style="float: right;">
                    <router-link :to="{ name: 'stok_taking' }"
                        class="btn btn-outline-dark btn-sm waves-effect waves-light"> <i class="fa fa-arrow-left"></i>
                        Back</router-link>
                    <div class="btn-group ml-1" role="group">
                        <button type="button"
                            class="btn btn-outline-primary btn-sm waves-effect waves-light dropdown-toggle"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More Action</button>
                        <div class="dropdown-menu">
                            <a href="javascript:void(0);" class="dropdown-item" @click="printDocument">Print
                                Document</a>
                            <a href="javascript:void(0);" class="dropdown-item" @click="confirm_delete_stok_taking"
                                v-if="status === 0">Delete</a>
                            <a href="javascript:void(0);" class="dropdown-item" @click="confirm_stok_taking"
                                v-if="status === 0">Complete
                                Proses</a>
                        </div>
                    </div>
                </div>
            </h5>
            <h5 class="badge badge-info" style="font-size: 1rem;">{{ total_verified.toLocaleString() }} / {{
                total_barcode.toLocaleString() }}</h5>
        </div>
        <div class="card-header">
            <h5 v-if="status === 0"><i class="fa fa-edit" style="color: blue;font-size: 1.5rem;"></i> Draft </h5>
            <h5 v-else-if="status === 1"><i class="fa fa-check-circle" style="color: blue;font-size: 1.5rem;"></i>
                Complete </h5>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="input-1">Business Unit *</label>
                <Field name="businessUnit" type="text" class="form-control" id="input-1" v-model="business_unit_name"
                    disabled>
                </Field>
            </div>
            <div class="form-group">
                <label for="input-1">Trn. Code *</label>
                <Field name="trn_code" type="text" class="form-control" id="input-1" v-model="trn_code" disabled>
                </Field>
            </div>
            <div class="form-group">
                <label for="input-1">Stock-Taking Date *</label>
                <Field name="register_date" type="text" class="form-control" id="input-1" v-model="stock_taking_date"
                    disabled>
                </Field>
            </div>
            <div class="form-group">
                <label for="input-1">Responsibility Person *</label>
                <Field name="employee" type="text" class="form-control" id="input-1" v-model="employee_name" disabled>
                </Field>
            </div>
            <div class="form-group">
                <label for="input-1">Note *</label>
                <Field name="note" as="textarea" type="text" class="form-control" id="input-1" v-model="note" disabled>
                </Field>
            </div>
            <div class="form-group">
                <label for="input-1">Reason *</label>
                <Field name="reason" as="textarea" type="text" class="form-control" id="input-1" v-model="reason"
                    disabled>
                </Field>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h5>List Scanned Barcode</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive" id="table_container">
                <table class="table table-bordered" style="width: 100%" id="tableBarcode" v-if="!loading">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Barcode</th>
                            <th>Asset Code</th>
                            <th>Asset Name</th>
                            <th>Group</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</template>
<script>
import axios from 'axios';
import swalNotif from '../Utils/swalNotif';
import { Form, Field, ErrorMessage } from 'vee-validate';
import Swal from 'sweetalert2';
import fileDownload from 'js-file-download';

export default {
    components: {
        Form,
        Field,
        ErrorMessage
    },
    data() {
        return {
            id: this.$route.params.id,
            loading: true,
            disabled: false,
            tableBarcode: null,
            business_unit_name: "",
            trn_code: "",
            stock_taking_date: "",
            note: "",
            reason: "",
            employee_name: "",
            status: 0,
            tableStokTaking: null,
            total_verified: 0,
            total_barcode: 0
        }
    },
    methods: {
        get_stock_taking() {
            const vm = this;
            vm.globalLoader.show = true;
            axios.post('/api/v1/web/stok/taking/get/id', {
                id: vm.id
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    vm.business_unit_name = res.data.data.business_unit_name;
                    vm.trn_code = res.data.data.code;
                    vm.stock_taking_date = res.data.data.stok_taking_date;
                    vm.note = res.data.data.note;
                    vm.employee_name = res.data.data.employee_name;
                    vm.status = res.data.data.status;
                    vm.total_barcode = res.data.data.total_barcode;
                    vm.total_verified = res.data.data.total_verified;
                    vm.reason = res.data.data.reason;
                    if (vm.tableBarcode === null) {
                        vm.get_barcode();
                    }
                }
                else {
                    swalNotif.error(res.data.message);
                    vm.$router.push({ name: "stok_taking" });
                }
            }).catch(res => {
                swalNotif.error("Error Get Check Out Chart!");
            }).finally(function () {
                vm.globalLoader.show = false;
            });
        },
        get_barcode() {
            const vm = this;
            this.tableBarcode = $("#tableBarcode").DataTable(
                {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "/api/v1/web/stok/taking/barcode/get",
                        headers: {
                            token: localStorage.getItem('token')
                        },
                        data: function (d) {
                            d.id = vm.id;
                        },
                    },
                    "columnDefs": [{
                        "width": "1%",
                        "targets": 0
                    },],
                    ordering: false,
                    lengthChange: false,
                    columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    }, {
                        data: 'barcode',
                        name: 'barcode'
                    }, {
                        data: 'asset_id',
                        name: 'asset_id'
                    }, {
                        data: 'asset_name',
                        name: 'asset_name'
                    }, {
                        data: 'group_name',
                        name: 'group_name'
                    }, {
                        data: 'location_name',
                        name: 'location_name'
                    },
                    ]
                }
            );
        },
        confirm_stok_taking() {
            const vm = this;
            Swal.fire({
                icon: "warning",
                title: "Warning",
                input: "text",
                inputLabel: "Reason / Note",
                inputPlaceholder: "Type your reason here...",
                preConfirm: (value) => {
                    if (!value) {
                        Swal.showValidationMessage("Reason is required");
                        return false;
                    }

                    if (value.length < 5) {
                        Swal.showValidationMessage(
                            `Reason must be at least 5 characters (currently ${value.length})`
                        );
                        return false;
                    }
                    return value;
                },
                allowOutsideClick: false,
                allowEscapeKey: false,
                text: "This Action Will Force Verification!",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "Yes, Proses it!",
                cancelButtonText: "No, cancel!",
                showCancelButton: true,
                didOpen: () => {
                    Swal.showLoading();
                    setTimeout(() => { Swal.hideLoading() }, 500)
                }
            }).then((result) => {
                $(".confirm").attr('disabled', 'disabled');
                if (result.isConfirmed) {
                    const reason = result.value;
                    vm.stok_taking_proses(reason);
                }
            });
        },
        stok_taking_proses(note) {
            const vm = this;
            vm.globalLoader.show = true;
            axios.post('/api/v1/web/stok/taking/force/verification', {
                id: vm.id,
                note: note
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    vm.get_stock_taking();
                    swalNotif.success(res.data.message);
                }
                else {
                    swalNotif.error(res.data.message);
                }
            }).catch(err => {
                swalNotif.error("Error Proses!");
            }).finally(function () {
                vm.globalLoader.show = false;
            });
        },

        delete_stok_taking() {
            const vm = this;
            vm.globalLoader.show = true;
            axios.post('/api/v1/web/stok/taking/delete', {
                id: vm.id,
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    swalNotif.success(res.data.message);
                    vm.$router.push({ name: 'stok_taking' });
                }
                else {
                    swalNotif.error(res.data.message);
                }
            }).catch(err => {
                swalNotif.error("Error Delete Stock Taking!");
            }).finally(function () {
                vm.globalLoader.show = false;
            });
        },
        confirm_delete_stok_taking() {
            const vm = this;
            Swal.fire({
                icon: "warning",
                title: "Warning",
                allowOutsideClick: false,
                allowEscapeKey: false,
                text: "This Action Will Delete Stok Taking!",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "Yes, Delete it!",
                cancelButtonText: "No, cancel!",
                showCancelButton: true,
                didOpen: () => {
                    Swal.showLoading();
                    setTimeout(() => { Swal.hideLoading() }, 500)
                }
            }).then((result) => {
                $(".confirm").attr('disabled', 'disabled');
                if (result.isConfirmed) {
                    vm.delete_stok_taking();
                }
            });
        },
        printDocument() {
            const vm = this;
            this.globalLoader.show = true;
            axios.post("/api/v1/web/stok/taking/print", {
                id: vm.id
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                },
                responseType: 'blob',
            }).then(async res => {
                const contentType = res.headers['content-type'];
                if (contentType && contentType.includes('application/json')) {
                    const text = await res.data.text();   // blob → text
                    const json = JSON.parse(text);

                    swalNotif.error(json.message || 'Export failed');
                    return;

                }
                const disposition = res.headers['content-disposition'];
                let filename = 'downloaded-file'; // default

                if (disposition && disposition.includes('filename=')) {
                    const filenameMatch = disposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                    if (filenameMatch && filenameMatch[1]) {
                        filename = filenameMatch[1].replace(/['"]/g, ''); // hilangkan tanda kutip
                    }
                }
                fileDownload(res.data, filename);
                swalNotif.success("Print" + filename + " Success");
            }).catch(err => {
                swalNotif.error(err.message);
            }).finally(function () {
                vm.globalLoader.show = false;
            });
        },
    },
    mounted() {
        const vm = this;
        this.loading = false;
        setTimeout(() => {
            this.get_stock_taking();
            $("#tableBarcode").on('click', '.btnCheckIn', function () {
                vm.checkin_data.status = 1;
                vm.checkin_data.barcode = vm.tableBarcode.row($(this).closest('tr')).data().code_barcode;
                vm.checkin_data.id = this.id;
                $("#checkinModal").modal({ keyboard: false, backdrop: 'static' });
            });
            $("#tableBarcode").on('click', '.btnCancel', function () {
                vm.checkin_data.status = 0;
                vm.checkin_data.barcode = vm.tableBarcode.row($(this).closest('tr')).data().code_barcode;
                vm.checkin_data.id = this.id;
                vm.confirmCancelCheckInBarcode();
            });
        }, 1);
    }
}
</script>
