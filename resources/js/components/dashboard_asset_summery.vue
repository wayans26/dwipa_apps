<style>
.asset-summary {
    display: flex;
    flex-direction: column;
}

.asset-header {
    background: #1f86ff;
    border-radius: 12px;
    min-height: 260px;
}

.asset-body {
    border-radius: 12px;
    border: none;
    margin: -70px 25px 0 25px;
    /* bikin floating */
}

.icon-box {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: #f3f6fb;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c7aa0;
    font-size: 18px;
    flex-shrink: 0;
}

.icon-box i {
    color: #828282;
}
</style>

<template>
    <div class="asset-summary">

        <!-- HEADER BLUE -->
        <div class="asset-header text-white p-4">
            <h5 class="font-weight-bold text-white">Asset Summary</h5>

            <div class="text-center mt-4">
                <small>Total Asset Value (IDR)</small>
                <h1 class="font-weight-bold mt-2 text-white">{{ dataSummery.total_asset_price }}</h1>
            </div>
        </div>

        <div class="card flex-fill mb-0">
            <!-- FLOATING CARD -->
            <div class="card asset-body shadow-sm">
                <div class="card-body">

                    <!-- ITEM -->
                    <div class="d-flex py-3 align-items-start">
                        <div class="icon-box mr-3">
                            <i class="fa fa-compass"></i>
                        </div>

                        <div class="flex-grow-1">
                            <h5 class="mb-1">Asset Type Count</h5>
                            <small class="text-muted">
                                The total number of unique asset types or models recorded in the system.
                            </small>
                        </div>

                        <div class="text-right">
                            <h5 class="mb-0 font-weight-bold">{{ dataSummery.total_asset_type.toLocaleString() }} Items
                            </h5>
                            <i class="bi bi-arrow-left-short text-success"></i>
                        </div>
                    </div>

                    <hr class="m-0">

                    <!-- ITEM -->
                    <div class="d-flex py-3 align-items-start">
                        <div class="icon-box mr-3">
                            <i class="zmdi zmdi-view-dashboard"></i>
                        </div>

                        <div class="flex-grow-1">
                            <h5 class="mb-1">Asset Unit Count</h5>
                            <small class="text-muted">
                                The total number of asset units that have been assigned unique barcodes.
                            </small>
                        </div>

                        <div class="text-right">
                            <h5 class="mb-0 font-weight-bold">{{ dataSummery.asset_unit.toLocaleString() }} Unit</h5>
                            <i class="bi bi-arrow-left-short text-danger"></i>
                        </div>
                    </div>

                    <hr class="m-0">

                    <!-- ITEM -->
                    <div class="d-flex py-3 align-items-start">
                        <div class="icon-box mr-3">
                            <i class="fa fa-line-chart"></i>
                        </div>

                        <div class="flex-grow-1">
                            <h5 class="mb-1">Net Book Value</h5>
                        </div>

                        <div class="text-right">
                            <h5 class="mb-0 font-weight-bold">0</h5>
                            <i class="bi bi-arrow-left-short text-success"></i>
                        </div>
                    </div>

                    <hr class="m-0">

                    <!-- ITEM -->
                    <div class="d-flex py-3 align-items-start">
                        <div class="icon-box mr-3">
                            <i class="fa fa-file-o"></i>
                        </div>

                        <div class="flex-grow-1">
                            <h5 class="mb-1">Disposal</h5>
                        </div>

                        <div class="text-right">
                            <h5 class="mb-0 font-weight-bold">{{ dataSummery.total_disposal.toLocaleString() }} Unit
                            </h5>
                            <i class="bi bi-arrow-left-short text-danger"></i>
                        </div>
                    </div>

                </div>
            </div>

            <div class="row m-3 mt-5">
                <div class="col-lg-6 d-flex">
                    <div class="card w-100 h-100 mb-2">
                        <div class="card-header">
                            <h5>Barcode Progress</h5>
                            <p class="text-mute">Complete your barcode creation</p>
                        </div>
                        <div class="card-body">
                            <apex-chart :options="chartOptions" :series="dataSummery.barcode_progress"
                                type="radialBar"></apex-chart>
                            <p><span class="badge bg-light-danger text-danger">Notes :</span> This indicator displays
                                the percentage of assets that have been assigned barcodes.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 d-flex">
                    <div class="card w-100 h-100 mb-2">
                        <div class="card-header">
                            <h5>Physical Check Progress</h5>
                            <p class="text-mute">Complete the physical check process</p>
                        </div>
                        <div class="card-body">
                            <div class="card-body">
                                <apex-chart :options="chartOptions" :series="dataSummery.physical_progress"
                                    type="radialBar"></apex-chart>
                                <p><span class="badge bg-light-danger text-danger">Notes :</span> This indicator
                                    displays the percentage of assets that have been physically checked.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>


<script>
import apexChart from "vue3-apexcharts";
export default {
    props: ['dataSummery'],
    components: {
        apexChart
    },
    data() {
        return {
            series: [30],
            chartOptions: {
                chart: {
                    type: "radialBar"
                },
                plotOptions: {
                    radialBar: {
                        hollow: {
                            size: "65%"
                        },
                        dataLabels: {
                            value: {
                                fontSize: "28px",
                                fontWeight: "bold",
                                formatter: val => val + "%"
                            },
                            name: {
                                show: false
                            }
                        }
                    }
                }
            }
        }
    },
    methods: {
        get_supplier() {
            const vm = this;
            this.tableSupplier = $("#tableSupplier").DataTable(
                {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "/api/v1/web/supplier/get",
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
                        data: 'contact',
                        name: 'contact'
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
                        data: 'fax',
                        name: 'fax'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                    ]
                }
            );
        },
    },
    mounted() {
        const vm = this;
        this.loading = false;
        setTimeout(() => {

        }, 1);
    }
}
</script>
