<template>
    <div class="card">
        <div class="card-header">
            <h5>Visitor Print</h5>
        </div>
        <div class="card-body">
            <form @submit.prevent="testPrint">
                <input ref="barcode_field" name="barcode" type="text" class="form-control" placeholder="Barcode *"
                    v-model="barcode" autofocus :disabled="disabled || !connected">
                </input>
                <br>
                <p class="text-mute">{{ status }} | {{ printer_name }}</p>
                <p class="text-muted mb-2">
                    Scanner: {{ scannerStatus }}
                    <span v-if="scannerError"> | {{ scannerError }}</span>
                </p>
                <button type="button" class="btn btn-primary ml-1" @click="launchQzTray" v-show="!connected">Launch
                    Printer</button>
                <button type="button" class="btn btn-primary ml-1" @click="connectQzTray" v-show="!connected">Connect
                    Printer</button>
                <button type="button" class="btn btn-primary ml-1" @click="connectScanner"
                    v-show="!scannerConnected">Connect Scanner</button>
                <!-- <button type="button" class="btn btn-primary ml-1" v-show="connected" :disabled="disabled">Test
                    Print</button> -->
            </form>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import swalNotif from '../Utils/swalNotif.js';
import qz from "qz-tray";
import notification from '../Utils/notification.js';
import Swal from 'sweetalert2';

export default {
    data() {
        return {
            disabled: false,
            loading: true,
            barcode: "",
            // Audio
            audio_validation: {
                validate: new Audio('/sound/validation.mp3'),
                success: new Audio('/sound/validation_done.mp3'),
                fail: new Audio('/sound/validation_fail.mp3'),
            },

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
            // Scanner (Web Serial)
            serialSupported: typeof navigator !== "undefined" && "serial" in navigator,
            scannerConnected: false,
            scannerReading: false,
            scannerStatus: "Scanner Not Connected",
            scannerError: "",
            scannerBaudRate: 115200,

            scannerPort: null,
            scannerReader: null,
            scannerKeepReading: false,
            scannerBuffer: "",
            scannerFlushTimer: null,
        }
    },
    methods: {
        testPrint() {
            this.print_barcode();
        },
        esc(text = "") {
            return String(text).replace(/"/g, "'");
        },
        async print_barcode() {
            if (this.barcode.length < 1) {
                notification.notif_info("Please Input Barcode");
                return;
            }
            if (!this.connected) {
                await this.connectQzTray();
            }
            const vm = this;
            vm.globalLoader.show = true;
            vm.disabled = true;
            vm.audio_validation.validate.play();
            axios.post("/api/v1/web/visitor/print", {
                'barcode': vm.barcode,
            }, {
                headers: {
                    token: localStorage.getItem('token'),
                }
            }).then(async res => {
                if (res.data.status == 1) {
                    vm.data_print = res.data.data.data_print;
                    if (!vm.audio_validation.validate.paused && !vm.audio_validation.validate.ended) {
                        vm.audio_validation.validate.pause();
                        vm.audio_validation.validate.currentTime = 0;
                    }
                    vm.audio_validation.success.play();
                    if (res.data.data.isPrinted == 1) {
                        vm.globalLoader.show = false;
                        Swal.fire({
                            icon: "info",
                            title: "Information",
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            text: "Do You Want To Reprint?",
                            confirmButtonColor: "#3085d6",
                            confirmButtonText: "Yes, Print it!",
                            cancelButtonText: "No, cancel!",
                            showCancelButton: true,
                            didOpen: () => {
                                Swal.showLoading();
                                setTimeout(() => { Swal.hideLoading() }, 100)
                            }
                        }).then(async (result) => {
                            $(".confirm").attr('disabled', 'disabled');
                            if (result.isConfirmed) {
                                vm.globalLoader.show = true;
                                await vm.print();
                            }
                            else {
                                this.status = `Printed Cancelled`;
                                this.globalLoader.show = false;
                                this.barcode = "";
                                this.disabled = false;
                            }
                        });
                    }
                    else {
                        await vm.print();
                    }
                }
                else {
                    notification.notif_error(res.data.message);
                    vm.globalLoader.show = false;
                    vm.disabled = false;
                    vm.barcode = "";
                }
            }).catch(err => {
                notification.notif_error("Error Internal Server");
                vm.globalLoader.show = false;
                vm.disabled = false;
                vm.barcode = "";
                if (!vm.audio_validation.validate.paused && !vm.audio_validation.validate.ended) {
                    vm.audio_validation.validate.pause();
                    vm.audio_validation.validate.currentTime = 0;
                }
                vm.audio_validation.fail.play();
            }).finally(function () {

            });
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
            this.globalLoader.show = false;
            this.barcode = "";
            this.disabled = false;
        },
        focusBarcodeField() {
            this.$nextTick(() => {
                const el = this.$refs.barcode_field;
                if (el && !el.disabled) {
                    el.focus();
                }
            });
        },
        async connectScanner() {
            this.scannerError = "";

            if (!this.serialSupported) {
                this.scannerStatus = "Web Serial is not supported in this browser";
                return;
            }

            try {
                const port = await navigator.serial.requestPort();

                await this.openScannerPort(port);
            } catch (err) {
                this.scannerError = err && err.message ? err.message : String(err);
                this.scannerStatus = "Scanner connection failed";
                this.disconnectScanner();
            }
        },
        async autoReconnectScanner() {
            if (!this.serialSupported) return;

            try {
                const ports = await navigator.serial.getPorts();
                if (ports && ports.length > 0) {
                    await this.openScannerPort(ports[0], true);
                }
            } catch (err) {
                console.error("autoReconnectScanner error:", err);
            }
        },
        async openScannerPort(port, silent = false) {
            try {
                if (this.scannerConnected) {
                    await this.disconnectScanner();
                }

                this.scannerPort = port;

                await this.scannerPort.open({
                    baudRate: this.scannerBaudRate,
                    dataBits: 8,
                    stopBits: 1,
                    parity: "none",
                    flowControl: "none",
                });

                this.scannerConnected = true;
                this.scannerStatus = "Scanner Connected";
                this.scannerKeepReading = true;

                this.readScannerLoop();

                if (!silent) {
                    console.log("Scanner connected");
                }
            } catch (err) {
                this.scannerError = err && err.message ? err.message : String(err);
                this.scannerStatus = "Failed to open scanner port";
                console.error("openScannerPort error:", err);
            }
        },

        async readScannerLoop() {
            if (!this.scannerPort || !this.scannerPort.readable) return;

            this.scannerReading = true;
            const decoder = new TextDecoder();

            try {
                while (this.scannerPort && this.scannerPort.readable && this.scannerKeepReading) {
                    this.scannerReader = this.scannerPort.readable.getReader();

                    try {
                        while (this.scannerKeepReading) {
                            const { value, done } = await this.scannerReader.read();

                            if (done) break;

                            if (value) {
                                const text = decoder.decode(value, { stream: true });
                                if (text) {
                                    this.handleScannerChunk(text);
                                }
                            }
                        }
                    } finally {
                        if (this.scannerReader) {
                            this.scannerReader.releaseLock();
                            this.scannerReader = null;
                        }
                    }
                }

                const tail = decoder.decode();
                if (tail) {
                    this.handleScannerChunk(tail);
                }
            } catch (err) {
                if (this.scannerKeepReading) {
                    this.scannerError = err && err.message ? err.message : String(err);
                    this.scannerStatus = "Scanner read error";
                }
                console.error("readScannerLoop error:", err);
            } finally {
                this.scannerReading = false;
            }
        },

        handleScannerChunk(text) {
            this.scannerBuffer += text;

            const normalized = this.scannerBuffer
                .replace(/\r\n/g, "\n")
                .replace(/\r/g, "\n");

            const parts = normalized.split("\n");
            this.scannerBuffer = parts.pop() || "";

            for (const part of parts) {
                this.applyScannedBarcode(part);
            }

            this.scheduleScannerFlush();
        },

        scheduleScannerFlush() {
            if (this.scannerFlushTimer) {
                clearTimeout(this.scannerFlushTimer);
            }

            this.scannerFlushTimer = setTimeout(() => {
                const value = (this.scannerBuffer || "").trim();
                if (value) {
                    this.applyScannedBarcode(value);
                }
                this.scannerBuffer = "";
            }, 50);
        },
        applyScannedBarcode(raw) {
            const code = String(raw || "").trim();
            if (!code) return;

            this.barcode = code;

            this.$nextTick(() => {
                this.focusBarcodeField();

                // OPTIONAL:
                // Auto print right after successful scan
                // Uncomment if you want direct scan -> print

                if (this.connected && !this.disabled) {
                    this.print_barcode();
                }

            });
        },
        async disconnectScanner() {
            this.scannerKeepReading = false;

            if (this.scannerFlushTimer) {
                clearTimeout(this.scannerFlushTimer);
                this.scannerFlushTimer = null;
            }

            try {
                if (this.scannerReader) {
                    await this.scannerReader.cancel();
                }
            } catch (err) {
                console.error("scannerReader.cancel error:", err);
            }

            try {
                if (this.scannerPort) {
                    await this.scannerPort.close();
                }
            } catch (err) {
                console.error("scannerPort.close error:", err);
            }

            this.scannerReader = null;
            this.scannerPort = null;
            this.scannerBuffer = "";
            this.scannerConnected = false;
            this.scannerReading = false;
            this.scannerStatus = "Scanner Not Connected";
        },

    },
    mounted() {
        this.setupQzSecureOnce();
        this.autoReconnectScanner();
        if (qz.websocket.isActive()) {
            this.connected = true;
            this.connecting = false;
            this.status = "Printer Connected";
        }
        const vm = this;
        this.loading = false;
    },
    beforeUnmount() {
        this.safeDiconnect();
        this.disconnectScanner();
    }
}
</script>
