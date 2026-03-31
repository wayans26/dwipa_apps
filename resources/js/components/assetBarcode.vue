<style>
/* Example: treat 1024px and up as "desktop" */
@media (min-width: 1024px) {

    .modal-xxl {
        max-width: 90vw !important;
    }
}
</style>
<template>
    <div class="card">
        <div class="card-header">
            <h5>List Of Barcode</h5>
            <span class="text-mute">{{ status }} <span v-show="printer_name !== ''" class="text-mute">| {{ printer_name
            }}</span> </span>
            <div style="float: right;">
                <div class="btn-group m-1" role="group">
                    <button type="button" class="btn btn-primary waves-effect waves-light dropdown-toggle"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Print Barcode
                    </button>
                    <div class="dropdown-menu">
                        <a href="javaScript:void(0);" class="dropdown-item" @click="print_barcode('barcode')">Barcode
                            Normal</a>
                        <a href="javaScript:void(0);" class="dropdown-item" @click="print_barcode('qrcode')">QR Code</a>
                    </div>
                </div>
                <div class="btn-group m-1" role="group">
                    <button type="button" class="btn btn-primary waves-effect waves-light dropdown-toggle"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        More Action
                    </button>
                    <div class="dropdown-menu">
                        <a href="javaScript:void(0);" class="dropdown-item" @click="launchQzTray">Launch Printer</a>
                        <a href="javaScript:void(0);" class="dropdown-item" @click="connectQzTray">Connect to
                            Printer</a>
                        <a href="javaScript:void(0);" class="dropdown-item" @click="safeDiconnect">Disconnect
                            Printer</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive" id="table_container">
                <table class="table table-bordered" style="width: 100%" id="tableAssetBarcode" v-if="!loading">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll" class="checkbox-custom" /></th>
                            <th>#</th>
                            <th>Barcode</th>
                            <th>Location</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div class="card-footer"></div>
    </div>

    <div class="modal fade" id="createBarcode">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Generate Barcode</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="initValue">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form @submit="generate_barcode">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="input-1">Location *</label>
                            <v-select class="form-control" placeholder="Select an Location *" :options="listLocation"
                                label="label" :reduce="option => option.value" v-model="location_id"></v-select>
                        </div>
                        <div class="form-group">
                            <label for="input-1">Note *</label>
                            <Field name="note" as="textarea" type="text" class="form-control" id="input-1"
                                placeholder="Note *" v-model="note" rows="4"></Field>
                            <ErrorMessage style="color: red;" name="note" />
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

    <div class="modal fade" id="editBarcode">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Barcode</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="initValue">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form @submit="edit_barcode">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="input-1">Barcode *</label>
                            <Field name="code_barcode" type="text" class="form-control" id="input-1" :disabled="!isEdit"
                                placeholder="Barcode *" v-model="code_barcode" rows="4"></Field>
                            <ErrorMessage style="color: red;" name="code_barcode" />
                        </div>
                        <div class="form-group">
                            <label for="input-1">Location *</label>
                            <v-select class="form-control" placeholder="Select an Location *" :options="listLocation"
                                :disabled="!isEdit" label="label" :reduce="option => option.value"
                                v-model="location_id"></v-select>
                        </div>
                        <div class="form-group">
                            <label for="input-1">Note *</label>
                            <Field name="note" as="textarea" type="text" class="form-control" id="input-1"
                                :disabled="!isEdit" placeholder="Note *" v-model="note" rows="4"></Field>
                            <ErrorMessage style="color: red;" name="note" />
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

    <div class="modal fade" id="viewHistory">
        <div class="modal-dialog modal-lg modal-xxl" style="max-width: 90vw;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">History</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive" id="table_container">
                        <table class="table table-bordered table-striped" style="width: 100%" id="tableHistory">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Barcode</th>
                                    <th>Old Location</th>
                                    <th>New Location</th>
                                    <th>Notes</th>
                                    <th>Status</th>
                                    <th>References</th>
                                    <th>Action By</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-if="historyBarcode.length === 0">
                                    <tr>
                                        <td colspan="8" style="text-align: center;">History Not Found</td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr v-for="(item, index) in historyBarcode" :key="index">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ item.new_barcode === item.old_barcode ? item.new_barcode :
                                            item.old_barcode + ' -> ' + item.new_barcode }}</td>
                                        <td>{{ item.old_location_name }}</td>
                                        <td>{{ item.new_location_name }}</td>
                                        <td>{{ item.new_barcode_note }}</td>
                                        <td>{{ item.action_type }}</td>
                                        <td>{{ item.reference }}</td>
                                        <td>{{ item.action_by }} <br> {{ item.action_date }}</td>
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
                </div>
            </div>
        </div>
    </div>

    <button type="button" ref="hiddenPrintBtn" v-print="'#barcode-print'" style="display: none">
        Hidden Print
    </button>

    <div id="barcode-print" v-show="isPrint">
        <print-barcode v-if="type === 'barcode'" :list-barcode="listBarcode"></print-barcode>
        <print-qrcode v-if="type === 'qrcode'" :list-barcode="listBarcode"></print-qrcode>
    </div>
</template>

<script>
import { Form, Field, ErrorMessage } from 'vee-validate';
import axios from 'axios';
import swalNotif from '../Utils/swalNotif.js';
import printBarcode from './print_barcode.vue';
import printQrcode from './print_qrcode.vue';
import qz from "qz-tray";

export default {
    props: ['register_code', 'listLocation', 'locationId'],
    emits: ['refreshAsset'],
    components: {
        Form,
        Field,
        ErrorMessage,
        printBarcode,
        printQrcode
    },
    data() {
        return {
            disabled: false,
            loading: true,
            location_id: this.locationId,
            note: "",
            historyBarcode: [],
            isEdit: false,
            code_barcode: "",
            id: "",
            isPrint: false,
            tableAssetBarcode: "",
            barcode_chaked: [],
            listBarcode: [],
            type: "",
            mustRefresh: false,

            // Printer
            status: "Printer Not Connected",
            printer_name: "",
            connected: false,
            connecting: false,
            showLaunchHint: false,
            cfg: null,
            data_print: "",
            data_config: {
                colorType: "color",
                copies: 1,
                density: 0,
                duplex: false,
                fallbackDensity: null,
                interpolation: "bicubic",
                jobName: null,
                margins: 0,
                orientation: null,
                paperThickness: null,
                printerTray: null,
                rasterize: true,
                rotation: 0,
                scaleContent: true,
                size: null,
                units: "in",
                altPrinting: false,
                encoding: null,
                endOfDoc: null,
                perSpool: 1
            }
        }
    },
    methods: {
        get_asset_barcode() {
            const vm = this;
            this.tableAssetBarcode = $("#tableAssetBarcode").DataTable(
                {
                    processing: true,
                    serverSide: false,
                    ajax: {
                        type: "POST",
                        url: "/api/v1/web/asset/barcode/get",
                        headers: {
                            token: localStorage.getItem('token')
                        },
                        data: function (d) {
                            d.register_code = vm.register_code
                        }

                    },
                    fixedHeader: {
                        header: true,
                    },
                    scrollY: '60vh',
                    scrollCollapse: true,
                    scrollX: true,
                    "language": {
                        "emptyTable": '<button class="btn btn-primary btnCreateBarcode">Create Barcode</button>'
                    },
                    pageLength: 25,
                    "columnDefs": [{
                        "width": "2%",
                        "targets": 0
                    }, {
                        "width": "2%",
                        "targets": 1
                    },
                    {
                        "width": "2%",
                        "targets": 5
                    }],
                    columns: [{
                        data: 'select_barcode',
                        name: 'select_barcode',
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    }, {
                        data: 'barcode',
                        name: 'barcode'
                    },
                    {
                        data: 'location_name',
                        name: 'location_name'
                    }, {
                        data: 'note',
                        name: 'note'
                    }, {
                        data: 'action',
                        name: 'action'
                    },
                    {
                        data: 'code_barcode',
                        name: 'code_barcode',
                        visible: false
                    },
                    ]
                }
            );
        },
        refresh_table() {
            const vm = this;
            vm.globalLoader.show = true;
            this.tableAssetBarcode.ajax.reload(() => {
                vm.globalLoader.show = false;
                if (vm.mustRefresh) {
                    vm.$emit("refreshAsset");
                }
            });
        },
        generate_barcode() {
            const vm = this;
            vm.disabled = true;
            vm.globalLoader.show = true;
            axios.post("/api/v1/web/asset/barcode/generate", {
                register_code: vm.register_code,
                location_id: vm.location_id,
                note: vm.note,
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    $("#createBarcode").modal('hide');
                    swalNotif.success(res.data.message);
                    vm.mustRefresh = true;
                    vm.refresh_table();
                } else {
                    vm.globalLoader.show = false;
                    swalNotif.error(res.data.message);
                }
            }).catch(err => {
                vm.globalLoader.show = false;
                swalNotif.error("Error Generate Barcode!");
            }).finally(function () {
                vm.disabled = false;
            });
        },
        getHistory(id) {
            const vm = this;
            vm.globalLoader.show = true;
            axios.post("/api/v1/web/asset/barcode/history/get", {
                id: id,
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    vm.historyBarcode = res.data.data;
                }
                else {
                    vm.historyBarcode = [];
                }
                $("#viewHistory").modal({ backdrop: 'static', keyboard: false });
            }).catch(err => {
                swalNotif.error(err.response.data.message);
            }).finally(function () {
                vm.globalLoader.show = false;
            });
        },
        initValue() {
            this.location_id = this.locationId;
            this.note = "";
            this.code_barcode = "";
        },
        getBarcodeById(id) {
            const vm = this;
            vm.globalLoader.show = true;
            axios.post("/api/v1/web/asset/barcode/get/id", {
                id: id,
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    vm.location_id = res.data.data.asset_location_id;
                    vm.note = res.data.data.note;
                    vm.code_barcode = res.data.data.code_barcode;
                    vm.id = res.data.data.id;
                    $("#editBarcode").modal({ backdrop: 'static', keyboard: false });
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
        edit_barcode() {
            const vm = this;
            vm.disabled = true;
            vm.globalLoader.show = true;
            axios.post("/api/v1/web/asset/barcode/edit", {
                id: vm.id,
                location_id: vm.location_id,
                note: vm.note,
                code_barcode: vm.code_barcode
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    swalNotif.success(res.data.message);
                    vm.refresh_table();
                    $("#editBarcode").modal('hide');
                } else {
                    swalNotif.error(res.data.message);
                }
            }).catch(err => {
                swalNotif.error(err.response.data.message);
            }).finally(function () {
                vm.globalLoader.show = false;
                vm.disabled = false;
            });
        },
        async print_barcode(type) {
            if (this.barcode_chaked.length < 1) {
                swalNotif.info("Please Select Barcode");
                return;
            }
            if (!this.connected) {
                await this.connectQzTray();
            }
            const vm = this;
            vm.globalLoader.show = true;
            axios.post("/api/v1/web/asset/register/data/print", {
                'barcode': vm.barcode_chaked,
                'code_type': type
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(async res => {
                if (res.data.status == 1) {
                    vm.data_print = res.data.data;
                    swalNotif.success(res.data.message);
                    await vm.print();
                }
                else {
                    swalNotif.error(res.data.message);
                }
            }).catch(err => {
                swalNotif.error(err);
            }).finally(function () {
                vm.globalLoader.show = false;
            });
            // this.listBarcode = [];
            // this.isPrint = true;
            // this.type = type;
            // if (this.barcode_chaked.length > 0) {
            //     this.listBarcode = this.barcode_chaked;
            //     this.$nextTick(() => {
            //         this.$refs.hiddenPrintBtn.click();
            //     });
            //     setTimeout(() => {
            //         this.isPrint = false;
            //     }, 1);
            // }
            // else {
            //     this.getRawBarcode();
            // }
        },
        getRawBarcode() {
            const vm = this;
            vm.globalLoader.show = true;
            axios.post("/api/v1/web/asset/barcode/get/raw", {
                'register_code': vm.register_code
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    vm.listBarcode = res.data.data.map(item => ({
                        id: item.id,
                        barcode: item.code_barcode
                    }));
                    vm.$nextTick(() => {
                        vm.$refs.hiddenPrintBtn.click();
                    });
                    setTimeout(() => {
                        this.isPrint = false;
                    }, 1);
                }
                else {
                    vm.listBarcode = [];
                }
            }).catch(err => {
                swalNotif.error(err.response.data.message);
            }).finally(function () {
                vm.globalLoader.show = false;
            });
        },
        updatedConfigure() {
            this.cfg.reconfigure({
                colorType: this.data_config.colorType,
                copies: this.data_config.copies,
                density: this.data_config.density,
                duplex: this.data_config.duplex,
                fallbackDensity: this.data_config.fallbackDensity,
                interpolation: this.data_config.interpolation,
                jobName: this.data_config.jobName,
                margins: this.data_config.margins,
                orientation: this.data_config.orientation,
                paperThickness: this.data_config.paperThickness,
                printerTray: this.data_config.printerTray,
                rasterize: this.data_config.rasterize,
                rotation: this.data_config.rotation,
                scaleContent: this.data_config.scaleContent,
                size: this.data_config.size,
                units: this.data_config.units,
                altPrinting: this.data_config.altPrinting,
                encoding: this.data_config.encoding,
                endOfDoc: this.data_config.endOfDoc,
                perSpool: this.data_config.perSpool
            });
        },
        getUpdateConfigure() {
            if (this.cfg == null) {
                cfg = qz.configs.create(null);
            }
            this.updatedConfigure();
        },
        setupQzSecureOnce() {
            qz.security.setCertificatePromise(function (resolve, reject) {
                axios.get("/api/v1/web/qz/cert", {
                    headers: { token: localStorage.getItem("token") },
                })
                    .then((res) => {
                        const certPem = res.data.data;
                        if (typeof certPem !== "string" || !certPem.includes("BEGIN CERTIFICATE")) {
                            throw new Error("CERT is not valid PEM string");
                        }
                        resolve(certPem);
                    });
                // resolve(`-----BEGIN CERTIFICATE-----
                // MIIDrzCCApegAwIBAgIUOKzzKzUdFyRCMQtYTRsK4HZXmkQwDQYJKoZIhvcNAQEL
                // BQAwYTELMAkGA1UEBhMCSUQxDDAKBgNVBAgMA05UVDEPMA0GA1UEBwwGS3VwYW5n
                // MRIwEAYDVQQKDAlMb2NhbCBEZXYxCzAJBgNVBAsMAlFaMRIwEAYDVQQDDAlsb2Nh
                // bGhvc3QwHhcNMjYwMzA0MTUzMzI3WhcNMzYwMzAxMTUzMzI3WjBhMQswCQYDVQQG
                // EwJJRDEMMAoGA1UECAwDTlRUMQ8wDQYDVQQHDAZLdXBhbmcxEjAQBgNVBAoMCUxv
                // Y2FsIERldjELMAkGA1UECwwCUVoxEjAQBgNVBAMMCWxvY2FsaG9zdDCCASIwDQYJ
                // KoZIhvcNAQEBBQADggEPADCCAQoCggEBAPQljDbcgIomQvvJfLUur0bvVe4hJX6Y
                // ZlhGHpRXQ3Toer0d2EDOJUgOC/6kUM9cSv81puTSCGsKCs7Li2mcZCUUHv7NIxTn
                // c7fRf4uhUHyTlKEBYSIwV3Chke/PSEoJ3CLuP9pNSXptRNwrozmUpSURSoUfc8Kv
                // 3ZhkOzHao3ZLpN76p43Xq7edNX01wUFeydKOENaKSsdBwL77TRGrj7BVw5+Ma5B1
                // CqreaS4+ukVHobmT7W6u40plmvrFVRdVdRTp2SdP2uqWRG3ukdqTciLaS4IiO9FO
                // c7jgsDxFPZmIUeKwmJbE2A8D7LUxb4FBmqFSrkcDfMDBmYAgLRatMmsCAwEAAaNf
                // MF0wCwYDVR0PBAQDAgQwMBMGA1UdJQQMMAoGCCsGAQUFBwMBMBoGA1UdEQQTMBGC
                // CWxvY2FsaG9zdIcEfwAAATAdBgNVHQ4EFgQUzM8y/FosQPp4oi2snzBwZB3CYiIw
                // DQYJKoZIhvcNAQELBQADggEBAF/YOcCzM7fg2o0j1i/vZt0njbco/fY80XpNnFoE
                // WCU/Ki0J14ZR2BVyUUxhZwCEfaiuTFjvBV/oHaz3LhCUzhbswxlPW4ls4CjGeIkl
                // Q1x3TU3rXJRGJebLOSHYwPm3oGToyoq33Cr9b2ZRDni8EuL0E8RMYBb2tIVW48dY
                // 8s/gAAgdeRRIysPTyRUTU5M8kl0wP7NNqgSMR3FNHO4UfT9SvEdqAfvYdLBeewuD
                // j3XocEu43IDa7LcFfoOeEyVYFudh1AAal8kSsdpZATDa7gFzx1ZmvODwDdwrRRZb
                // 5+SWq4sh9qVU8OKs5O7VbRHDKq0RPWjXXQAk9tHpgWguiSY=
                // -----END CERTIFICATE-----
                // -----BEGIN CERTIFICATE-----
                // MIIFEjCCA/qgAwIBAgICEAAwDQYJKoZIhvcNAQELBQAwgawxCzAJBgNVBAYTAlVT
                // MQswCQYDVQQIDAJOWTESMBAGA1UEBwwJQ2FuYXN0b3RhMRswGQYDVQQKDBJRWiBJ
                // bmR1c3RyaWVzLCBMTEMxGzAZBgNVBAsMElFaIEluZHVzdHJpZXMsIExMQzEZMBcG
                // A1UEAwwQcXppbmR1c3RyaWVzLmNvbTEnMCUGCSqGSIb3DQEJARYYc3VwcG9ydEBx
                // emluZHVzdHJpZXMuY29tMB4XDTE1MDMwMjAwNTAxOFoXDTM1MDMwMjAwNTAxOFow
                // gZgxCzAJBgNVBAYTAlVTMQswCQYDVQQIDAJOWTEbMBkGA1UECgwSUVogSW5kdXN0
                // cmllcywgTExDMRswGQYDVQQLDBJRWiBJbmR1c3RyaWVzLCBMTEMxGTAXBgNVBAMM
                // EHF6aW5kdXN0cmllcy5jb20xJzAlBgkqhkiG9w0BCQEWGHN1cHBvcnRAcXppbmR1
                // c3RyaWVzLmNvbTCCAiIwDQYJKoZIhvcNAQEBBQADggIPADCCAgoCggIBANTDgNLU
                // iohl/rQoZ2bTMHVEk1mA020LYhgfWjO0+GsLlbg5SvWVFWkv4ZgffuVRXLHrwz1H
                // YpMyo+Zh8ksJF9ssJWCwQGO5ciM6dmoryyB0VZHGY1blewdMuxieXP7Kr6XD3GRM
                // GAhEwTxjUzI3ksuRunX4IcnRXKYkg5pjs4nLEhXtIZWDLiXPUsyUAEq1U1qdL1AH
                // EtdK/L3zLATnhPB6ZiM+HzNG4aAPynSA38fpeeZ4R0tINMpFThwNgGUsxYKsP9kh
                // 0gxGl8YHL6ZzC7BC8FXIB/0Wteng0+XLAVto56Pyxt7BdxtNVuVNNXgkCi9tMqVX
                // xOk3oIvODDt0UoQUZ/umUuoMuOLekYUpZVk4utCqXXlB4mVfS5/zWB6nVxFX8Io1
                // 9FOiDLTwZVtBmzmeikzb6o1QLp9F2TAvlf8+DIGDOo0DpPQUtOUyLPCh5hBaDGFE
                // ZhE56qPCBiQIc4T2klWX/80C5NZnd/tJNxjyUyk7bjdDzhzT10CGRAsqxAnsjvMD
                // 2KcMf3oXN4PNgyfpbfq2ipxJ1u777Gpbzyf0xoKwH9FYigmqfRH2N2pEdiYawKrX
                // 6pyXzGM4cvQ5X1Yxf2x/+xdTLdVaLnZgwrdqwFYmDejGAldXlYDl3jbBHVM1v+uY
                // 5ItGTjk+3vLrxmvGy5XFVG+8fF/xaVfo5TW5AgMBAAGjUDBOMB0GA1UdDgQWBBSQ
                // plC3hNS56l/yBYQTeEXoqXVUXDAfBgNVHSMEGDAWgBQDRcZNwPqOqQvagw9BpW0S
                // BkOpXjAMBgNVHRMEBTADAQH/MA0GCSqGSIb3DQEBCwUAA4IBAQAJIO8SiNr9jpLQ
                // eUsFUmbueoxyI5L+P5eV92ceVOJ2tAlBA13vzF1NWlpSlrMmQcVUE/K4D01qtr0k
                // gDs6LUHvj2XXLpyEogitbBgipkQpwCTJVfC9bWYBwEotC7Y8mVjjEV7uXAT71GKT
                // x8XlB9maf+BTZGgyoulA5pTYJ++7s/xX9gzSWCa+eXGcjguBtYYXaAjjAqFGRAvu
                // pz1yrDWcA6H94HeErJKUXBakS0Jm/V33JDuVXY+aZ8EQi2kV82aZbNdXll/R6iGw
                // 2ur4rDErnHsiphBgZB71C5FD4cdfSONTsYxmPmyUb5T+KLUouxZ9B0Wh28ucc1Lp
                // rbO7BnjW
                // -----END CERTIFICATE-----`);
            });
            qz.security.setSignatureAlgorithm("SHA256");
            qz.security.setSignaturePromise(function (toSign) {
                return function (resolve, reject) {
                    // resolve();
                    axios.post("/api/v1/web/qz/sign", {
                        toSign: toSign
                    }, {
                        headers: { token: localStorage.getItem("token") },
                    })
                        .then((res) => {
                            const certPem = res.data.data;
                            resolve(certPem);
                        });
                };
            });
        },
        launchQzTray() {
            if (this.connected) {
                swalNotif.info("Printer Already Connected");
                return;
            }
            if (!this.connecting) {
                window.location.href = "qz:launch";
                setTimeout(async () => {
                    await this.connectQzTray();
                }, 5000);
            }
            else {
                swalNotif.info("Connecting In Progress");
                return;
            }
        },
        async connectQzTray() {
            if (this.connecting) {
                swalNotif.info("Connecting In Progress");
                return;
            }
            this.connecting = true;
            if (qz.websocket.isActive()) {
                this.connected = true;
                this.connecting = false;
                this.status = "Printer Already Connected";
            }
            else {
                this.status = "Connectiong...";
                await qz.websocket.connect({ retries: 5, delay: 1 }).then(async () => {
                    this.connected = true;
                    this.status = "Printer Connected";
                    this.printer_name = await qz.printers.getDefault();
                    this.cfg = qz.configs.create(this.printer_name);
                    this.connecting = false;
                }).catch((err) => {
                    swalNotif.error("Please Launch Printer First");
                    this.status = "Printer Not Connected";
                    this.connecting = false;
                });
            }
        },
        async safeDiconnect() {
            try {
                if (qz.websocket.isActive()) {
                    this.status = "Disconnecting...";

                    await qz.websocket.disconnect();
                    this.status = "Printer Disconnected";
                    this.connected = false;
                    this.connecting = false;
                    this.printer_name = "";
                } else {
                    this.status = "Printer Not Connected";
                }
            } catch (err) {
                console.error("Failed to disconnect:", err);
            }
        },
        async print() {
            if (!this.connected) {
                swalNotif.error("Printer Not Connected");
                return;
            }
            this.status = `Printing on ${this.printer_name}`;
            if (!this.data_print) {
                swalNotif.error("Data Not Found");
                return;
            }
            if (this.cfg == null) {
                swalNotif.error("Please Your Printer Driver");
                return;
            }
            await qz.print(this.cfg, [{ type: "raw", format: "command", data: this.data_print }]);
            this.status = `Printed Successfully`;
        },

    },
    mounted() {
        this.setupQzSecureOnce();
        if (qz.websocket.isActive()) {
            this.connected = true;
            this.connecting = false;
            this.status = "Printer Connected";
        }
        const vm = this;
        this.loading = false;
        setTimeout(() => {
            // this.data_print = "^XA ~TA000 ~JSN ^LT0 ^MNW ^MTT ^PON ^PMN ^LH0,0 ^JMA ^PR6,6 ~SD15 ^JUS ^LRN ^CI27 ^PA0,1,1,0 ^XZ ^XA ^MMT ^PW496 ^LL216 ^LS0 ^FT28,48^A0N,28,28^FH\^CI28^FDYAN_AJAH^FS ^BY3,3,60^FT28,121^BCN,,N,N ^FH\^FD>;9910001022100000001^FS ^FT28,154^A0N,28,28^FH\^CI28^FD9910001022100000001^FS ^FT28,186^A0N,28,28^FH\^CI28^FDNAMA_ITEM^FS^CI27 ^PQ1,0,1,Y ^XZ";
            this.get_asset_barcode();

            $("#tableAssetBarcode").on('click', '.btnCreateBarcode', function () {
                $("#createBarcode").modal({ backdrop: 'static', keyboard: false });
            });
            $("#tableAssetBarcode").on('click', '.btnHistory', function () {
                const id = this.id;
                vm.getHistory(id)
            });

            $("#tableAssetBarcode").on('click', '.btnEdit', function () {
                const id = this.id;
                vm.getBarcodeById(id)
                vm.isEdit = true;
            });
            $("#tableAssetBarcode").on('click', '.btnView', function () {
                const id = this.id;
                vm.getBarcodeById(id)
                vm.isEdit = false;
            });
            $("#tableAssetBarcode").on('change', '.btnSelect', function () {
                const barcode = vm.tableAssetBarcode.row($(this).closest('tr')).data().code_barcode;
                if ($(this).prop('checked')) {
                    if (!vm.barcode_chaked.some(item => item === barcode)) {
                        vm.barcode_chaked.push(barcode);
                    }
                } else {
                    vm.barcode_chaked = vm.barcode_chaked.filter(item => item !== barcode);
                }
            });

            $("#selectAll").on('change', function () {
                if ($(this).prop("checked")) {
                    vm.barcode_chaked = [];
                    vm.tableAssetBarcode.rows().every(function () {
                        const node = this.node();
                        const data = this.data();
                        vm.barcode_chaked.push(data.code_barcode);
                        $(node).find('.btnSelect').prop('checked', true);
                    });
                } else {
                    vm.tableAssetBarcode.rows().every(function () {
                        const node = this.node();
                        $(node).find('.btnSelect').prop('checked', false);
                    });
                    vm.barcode_chaked = [];
                }
            });

        }, 1);
    },
    beforeUnmount() {
        this.safeDiconnect();
    }
}
</script>
