<template>
    <div class="card">
        <div class="card-header">
            <bread-crumb></bread-crumb>
            <h5>Stock Taking </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="input-1">Business Unit</label>
                        <v-select class="form-control" placeholder="Select an Business Unit" :options="listBusinessUnit"
                            label="label" :reduce="option => option.value" v-model="businessUnitId"
                            @option:selected="refresh_table"></v-select>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="input-1">Status</label>
                        <v-select class="form-control" placeholder="Select an Status" :options="listStatusFilter"
                            label="label" :reduce="option => option.value" v-model="statusFilter"
                            @option:selected="refresh_table"></v-select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive" id="table_container">
                <table class="table table-bordered" style="width: 100%" id="tableStokTaking" v-if="!loading">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Trn Code</th>
                            <th>Stock-Taking Date </th>
                            <th>Responsibility</th>
                            <th>Note</th>
                            <th>Scanned Unit</th>
                            <th>Progress</th>
                            <th>Action</th>
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
import swalNotif from '../Utils/swalNotif.js';
import Swal from 'sweetalert2';
import fileDownload from 'js-file-download';

export default {
    components: {
    },
    data() {
        return {
            disabled: false,
            loading: true,
            tableStokTaking: "",
            listBusinessUnit: [],
            businessUnitId: "all",
            listStatusFilter: [
                {
                    label: 'All',
                    value: 'all'
                },
                {
                    label: 'Draft',
                    value: '0'
                },
                {
                    label: 'Completed',
                    value: '1'
                },

            ],
            statusFilter: "all"
        }
    },
    methods: {
        get_stok_taking() {
            const vm = this;
            this.tableStokTaking = $("#tableStokTaking").DataTable(
                {
                    processing: true,
                    serverSide: true,
                    searchDelay: 400,
                    ajax: {
                        url: "/api/v1/web/stok/taking/get",
                        headers: {
                            token: localStorage.getItem('token')
                        },
                        data: function (d) {
                            d.business_unit_id = vm.businessUnitId;
                            d.status = vm.statusFilter;
                        }

                    },
                    language: {
                        processing: "Searching Stock Taking..."
                    },
                    // scrollY: '60vh',
                    // scrollCollapse: true,
                    // scrollX: true,
                    pageLength: 25,
                    "columnDefs": [{
                        "width": "2%",
                        "targets": 0
                    }, {
                        "width": "2%",
                        "targets": 1
                    }, {
                        "width": "2%",
                        "targets": 2
                    }, {
                        "width": "2%",
                        "targets": 3
                    }, {
                        "width": "2%",
                        "targets": 5
                    }, {
                        "width": "10%",
                        "targets": 6
                    }, {
                        "width": "2%",
                        "targets": 7
                    }],
                    columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    }, {
                        data: 'code',
                        name: 'code'
                    }, {
                        data: 'stok_taking_date',
                        name: 'stok_taking_date'
                    },
                    {
                        data: 'employee_name',
                        name: 'employee_name'
                    },
                    {
                        data: 'note',
                        name: 'note'
                    },
                    {
                        data: 'total_verified',
                        name: 'total_verified'
                    },
                    {
                        data: 'progress',
                        name: 'progress'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                    ]
                }
            );
        },
        get_business_unit() {
            const vm = this;
            vm.globalLoader.show = true;
            axios.post('/api/v1/web/stok/taking/business/unit/get', {

            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    this.listBusinessUnit = [
                        {
                            label: 'All',
                            value: 'all'
                        },
                        ...res.data.data.map(item => ({
                            label: item.name,
                            value: item.id
                        }))
                    ]
                    vm.businessUnitId = res.data.data[0].id;
                }
            }).catch(res => {
                swalNotif.error("Error Get Business Unit!");
            }).finally(function () {
                vm.get_stok_taking();
                vm.globalLoader.show = false;
            });
        },
        refresh_table() {
            this.tableStokTaking.ajax.reload();
        },
    },
    mounted() {
        const vm = this;
        this.loading = false;
        setTimeout(() => {
            this.get_business_unit();
            $("#tableStokTaking").on('click', '.btnView', function () {
                const id = this.id;
                vm.$router.push({ name: "stok_taking_open", params: { id: id } });
            });
        }, 1);
    }
}
</script>
