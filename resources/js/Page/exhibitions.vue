<template>
    <div class="card">
        <div class="card-header">
            <h5>Exhibitions</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive" id="table_container">
                <table class="table table-bordered" style="width: 100%" id="tableExhibitions" v-if="!loading">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>name</th>
                            <th>status</th>
                            <th>Show</th>
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

export default {
    data() {
        return {
            disabled: false,
            loading: true,
            location_id: this.locationId,
            note: "",
            tableExhibitions: null,
        }
    },
    methods: {
        get_exhibitions() {
            const vm = this;
            this.tableExhibitions = $("#tableExhibitions").DataTable(
                {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        type: "GET",
                        url: "/api/v1/web/exhibitions/get",
                        headers: {
                            token: localStorage.getItem('token')
                        }
                    },
                    pageLength: 25,
                    "columnDefs": [{
                        "width": "2%",
                        "targets": 0
                    }, {
                        "width": "2%",
                        "targets": 4
                    }],
                    columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    }, {
                        data: 'is_show',
                        name: 'is_show'
                    }, {
                        data: 'action',
                        name: 'action'
                    },
                    ]
                }
            );
        },
        refresh_table() {
            const vm = this;
            vm.globalLoader.show = true;
            this.tableExhibitions.ajax.reload(() => {
                vm.globalLoader.show = false;
            });
        },
        change_show_status(idexhibitions, isShow) {
            const vm = this;
            vm.globalLoader.show = true;
            axios.post("/api/v1/web/exhibitions/show/change", {
                'idexhibitions': idexhibitions,
                'cmd': isShow
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    vm.refresh_table();
                    swalNotif.success(res.data.message);
                }
                else {
                    swalNotif.error(res.data.message);
                }
            }).catch(err => {
                swalNotif.error(err.response.data.message);
            }).finally(function () {
                vm.globalLoader.show = false;
            });
        },
    },
    mounted() {
        const vm = this;
        this.loading = false;
        setTimeout(() => {
            vm.get_exhibitions();

            $("#tableExhibitions").on('click', '.btnDisable', function () {
                const id = this.id;
                vm.change_show_status(id, '0')
            });
            $("#tableExhibitions").on('click', '.btnEnable', function () {
                const id = this.id;
                vm.change_show_status(id, '1')
            });

        }, 1);
    },
}
</script>
