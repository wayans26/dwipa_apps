<style scoped>
.tableAsset :deep(.dataTable td),
.tableAsset :deep(.dataTable th) {
    white-space: normal;
    word-break: normal;
}
</style>
<template>
    <div class="card h-100 w-100">
        <div class="card-header">
            <h5>Asset By Group</h5>
            <p class="text-mute">Total Qty : {{ dataSummery.asset_unit }} Unit</p>
        </div>
        <div class="card-body">
            <apex-chart type="pie" height="350" :options="chartOptios" :series="dataChart.series" />

            <div class="card mt-5">
                <div class="card-body">
                    <div class="tableAsset" id="table_container">
                        <table class="table table-bordered text-dark" style="width: 100%" id="tableAssetGroup"
                            v-if="!loading">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-left">Asset Group</th>
                                    <th class="text-center">Unit Count</th>
                                    <th class="text-center">Unit Precentage</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
import apexChart from "vue3-apexcharts";

export default {
    props: ['dataChart', 'dataSummery'],
    components: {
        apexChart
    },
    data() {
        return {
            loading: false,
            tableAssetGroup: null,
            chartOptios: {
                chart: {
                    type: 'pie',
                },
                labels: this.dataChart.labels,
                responsive: [
                    {
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 300
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                ]
            }
        }
    },
    methods: {
        refresh_table() {
            const vm = this;
            this.tableAssetGroup = $("#tableAssetGroup").DataTable({
                data: vm.dataChart.raw,
                columns: [
                    {
                        data: 'name'
                    },
                    {
                        data: 'quantity'
                    },
                    {
                        data: 'quantity',
                        render: function (qty) {
                            const percentage = ((Number(qty) / Number(vm.dataSummery.asset_unit)) * 100).toLocaleString('id-ID', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                            return `<span class="badge badge-pill bg-light-success text-success"
                                            style="font-size: 1.15rem;">${percentage}%</span></th>`;
                        }
                    }
                ],
                columnDefs: [
                    {
                        targets: 1,
                        className: 'text-left'
                    },
                    {
                        targets: 2,
                        className: 'text-right'
                    },
                ],
                ordering: false,
                lengthChange: false,
                searching: false,
                autoWidth: false,
                scrollX: false,
                responsive: true
            });
        }
    },
    mounted() {
        const vm = this;
        this.loading = false;
        setTimeout(() => {
            vm.refresh_table();
        }, 1);
    }
}
</script>
