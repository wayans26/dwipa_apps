<template>
    <div class="card">
        <div class="card-header">
            <bread-crumb></bread-crumb>
            <h5>Transaksi <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addTransaksi"
                    data-backdrop="static" data-keyboard="false">Add
                    New Data</button> </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive" id="table_container">
                <table class="table table-bordered" style="width: 100%" id="tableTransaksi" v-if="!loading">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th>Pemasukan</th>
                            <th>Pengeluaran</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addTransaksi">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Transaksi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form @submit="add_transaksi">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="periode">Periode * </label>
                            <VueDatePicker id="periode" v-model="periode" month-picker
                                :input-attrs="{ clearable: false }" format="yyyy-MM"
                                :time-config="{ enableTimePicker: false }" model-type="yyyy-MM" />
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
            table_transaksi: "",
            periode: `${new Date().getFullYear()}-${(new Date().getMonth() + 1).toString().padStart(2, "0")}`,
        }
    },
    methods: {
        get_transaksi() {
            const vm = this;
            this.table_transaksi = $("#tableTransaksi").DataTable(
                {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "/api/v1/web/transaksi/tengki/get",
                        headers: {
                            token: localStorage.getItem('token')
                        },

                    },
                    pageLength: 25,
                    "columnDefs": [{
                        "width": "2%",
                        "targets": 0
                    },
                    {
                        "width": "2%",
                        "targets": 1
                    }, {
                        "width": "2%",
                        "targets": 2
                    }, {
                        "width": "2%",
                        "targets": 6
                    }],
                    columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'periode',
                        name: 'periode'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'pemasukan',
                        name: 'pemasukan',
                        searchable: false
                    },
                    {
                        data: 'pengeluaran',
                        name: 'pengeluaran',
                        searchable: false
                    },
                    {
                        data: 'total_penghasilan',
                        name: 'total_penghasilan',
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        searchable: false
                    },
                    ]
                }
            );
        },
        add_transaksi() {
            this.globalLoader.show = true;
            const vm = this;
            axios.post("/api/v1/web/transaksi/tengki/add", {
                periode: vm.periode
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    vm.refresh_table();
                    $("#addTransaksi").modal("hide");
                    swalNotif.success(res.data.message);
                } else {
                    swalNotif.error(res.data.message);
                }
            }).catch(res => {
                swalNotif.error("Error Adding Transaksi!");

            }).finally(function () {
                vm.disabled = false;
                vm.globalLoader.show = false;
            });
        },
        refresh_table() {
            this.table_transaksi.ajax.reload();
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
            this.get_transaksi();

            $("#tableTransaksi").on('click', '.btnDetail', function () {
                const id = this.id;
                vm.$router.push({ name: 'transaksi_tengki_detail', params: { id: id } });
            });
            $("#tableTransaksi").on('click', '.btnDelete', function () {
                const id = this.id;
                const ctx = $(this);
            });
        }, 1);
    }
}
</script>
