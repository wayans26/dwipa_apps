<template>
    <div class="card">
        <div class="card-header">
            <h5>Visitor Registration Manual</h5>
        </div>
        <div class="card-body" v-show="!hasExhibitions">
            <h5>Exhibitions Closed, Pelase Contact Admin to Register Visitor</h5>
        </div>
        <div class="card-body" v-show="hasExhibitions">
            <Form :validation-schema="validate" @submit="registrasi">
                <div v-show="connected">
                    <div class="form-group">
                        <label for="input-1">Exhibitions</label>
                        <v-select class="form-control" placeholder="Select an Exhibitions" :options="list_exhibitions"
                            label="label" :reduce="option => option.value" v-model="exhibitions"
                            @option:selected="getSubExhibitions" :clearable="false"></v-select>
                    </div>
                    <div class="form-group">
                        <label for="input-1">Sub Exhibitions</label>
                        <v-select class="form-control" placeholder="Select an Sub Exhibitions"
                            :options="list_sub_exhibitions" label="label" :reduce="option => option.value"
                            v-model="sub_exhibitions" :clearable="false"></v-select>
                    </div>
                    <div class="form-group">
                        <label for="name">Name *</label>
                        <Field name="name" v-slot="{ field }" v-model="name">
                            <input v-bind="field" ref="name_visitor" class="form-control" id="name"
                                placeholder="Name *" />
                        </Field>
                        <ErrorMessage style="color: red;" name="name" />
                    </div>
                    <div class="form-group">
                        <label for="title">Title *</label>
                        <Field name="title" type="text" class="form-control" id="title" placeholder="Title *"
                            v-model="title">
                        </Field>
                        <ErrorMessage style="color: red;" name="title" />
                    </div>
                    <div class="form-group">
                        <label for="company">Company *</label>
                        <Field name="company" type="text" class="form-control" id="company" placeholder="Company *"
                            v-model="company">
                        </Field>
                        <ErrorMessage style="color: red;" name="company" />
                    </div>
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <Field name="email" type="text" class="form-control" id="email" placeholder="Email *"
                            v-model="email">
                        </Field>
                        <ErrorMessage style="color: red;" name="email" />
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone *</label>
                        <Field name="phone" type="text" class="form-control" id="phone" placeholder="Phone *"
                            v-model="phone">
                        </Field>
                        <ErrorMessage style="color: red;" name="phone" />
                    </div>
                    <div class="form-group">
                        <label for="input-1">Country</label>
                        <v-select class="form-control" placeholder="Select an Country" :options="list_country"
                            label="label" :reduce="option => option.value" v-model="country"
                            :clearable="false"></v-select>
                    </div>
                </div>
                <p class="text-mute">{{ status }} | {{ printer_name }}</p>
                <button type="button" class="btn btn-primary ml-1" @click="launchQzTray" v-show="!connected">Launch
                    Printer</button>
                <button type="button" class="btn btn-primary ml-1" @click="connectQzTray" v-show="!connected">Connect
                    Printer</button>
                <button type="submit" class="btn btn-primary ml-1" @click="connectQzTray"
                    v-show="connected">Register</button>
            </form>
        </div>
    </div>
</template>

<script>
import * as yup from 'yup';
import { Form, Field, ErrorMessage } from 'vee-validate';
import axios from 'axios';
import swalNotif from '../Utils/swalNotif.js';
import qz from "qz-tray";
import notification from '../Utils/notification.js';

export default {
    components: {
        Form,
        Field,
        ErrorMessage
    },
    data() {
        return {
            disabled: false,
            hasExhibitions: true,
            loading: true,
            barcode: "",
            name: "",
            title: "",
            company: "",
            email: "",
            phone: "",
            country: "ID",
            exhibitions: "",
            sub_exhibitions: "",
            list_exhibitions: [],
            list_sub_exhibitions: [],
            list_country: [],

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
                perSpool: 1,
            },
            validate: yup.object({
                name: yup.string().required("Name is required"),
                title: yup.string().required("Title is required"),
                company: yup.string().required("Company is required"),
                email: yup.string().required("Email is required").email("Email is invalid"),
                phone: yup.string().required("Phone is required"),
            }),
        }
    },
    methods: {
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
                    // this.printer_name = "Argox CP-2140 PPLB"
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
                notification.notif_info("Please Launch Printer First");
                return;
            }
            this.status = `Printing on ${this.printer_name}`;
            if (!this.data_print) {
                notification.notif_info("Data Not Found");
                return;
            }
            if (this.cfg == null) {
                notification.notif_info("Please Install Your Printer Driver");
                return;
            }
            await qz.print(this.cfg, [{ type: "raw", format: "plain", data: this.data_print }]);
            this.status = `Printed Successfully`;
            this.initValue();
            notification.notif_success("Printed Successfully");
            this.$nextTick(() => {
                this.$refs.name_visitor.focus();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
            this.globalLoader.show = false;
            this.barcode = "";
            this.disabled = false;
        },
        initValue() {
            this.name = "";
            this.title = "";
            this.company = "";
            this.email = "";
            this.phone = "";
        },
        getRegisterData() {
            this.globalLoader.show = true;
            const vm = this;
            axios.post("/api/v1/web/register/data/get", {
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    if (res.data.data.exhibitions.length == 0) {
                        swalNotif.info("Exhibitions Not Found, Please Contact Admin!");
                        vm.hasExhibitions = false;
                        return;
                    }
                    vm.list_country = res.data.data.country.map(item => ({
                        label: item.country_name,
                        value: item.idcountry
                    }));
                    vm.list_exhibitions = res.data.data.exhibitions.map(item => ({
                        label: item.name,
                        value: item.idexhibitions
                    }));
                    vm.exhibitions = res.data.data.exhibitions[0].idexhibitions;
                    vm.getSubExhibitions();
                } else {
                    swalNotif.error(res.data.message);
                }
            }).catch(res => {
                swalNotif.error("Error Get Data Exhibitions!");
            }).finally(function () {
                vm.globalLoader.show = false;
            });
        },
        getSubExhibitions() {
            this.globalLoader.show = true;
            const vm = this;
            axios.post("/api/v1/web/register/sub/exhibitions/get", {
                idexhibitions: vm.exhibitions
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(res => {
                if (res.data.status == 1) {
                    vm.list_sub_exhibitions = res.data.data.map(item => ({
                        label: item.nama,
                        value: item.idsubexhibitions
                    }));
                    vm.sub_exhibitions = res.data.data[0].idsubexhibitions;
                } else {
                    swalNotif.error(res.data.message);
                }
            }).catch(res => {
                swalNotif.error("Error Get Data!");
            }).finally(function () {
                vm.globalLoader.show = false;
            });
        },
        registrasi() {
            this.globalLoader.show = true;
            const vm = this;
            axios.post("/api/v1/web/register/add", {
                exhibitions: vm.exhibitions,
                sub_exhibitions: vm.sub_exhibitions,
                name: vm.name,
                title: vm.title,
                company: vm.company,
                email: vm.email,
                phone: vm.phone,
                country: vm.country,
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(async res => {
                if (res.data.status == 1) {
                    vm.data_print = res.data.data;
                    await vm.print();
                } else {
                    swalNotif.error(res.data.message);
                }
            }).catch(res => {
                swalNotif.error("Error Registrasi!");
            }).finally(function () {
                vm.globalLoader.show = false;
            });
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
        this.getRegisterData();
    },
    beforeUnmount() {
        this.safeDiconnect();

    }
}
</script>
