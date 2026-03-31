<style>
/* Atur ukuran kertas & margin A4 */
@page {
    size: A4 portrait;
    margin: 1cm;
}

/* Style khusus saat print */
@media print {

    /* Hilangkan scroll & margin default browser */
    html,
    body {
        width: 210mm;
        margin: 0;
        padding: 0;
    }

    /* Area konten utama */
    .print-page {
        padding: 1cm;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        /* 🔥 3 kolom */
        grid-gap: 12px;
        /* jarak antar QR */
    }

    /* Sembunyikan elemen yang tidak perlu di print */
    .no-print,
    .no-print * {
        display: none !important;
    }

    /* Biar link tidak tampil sebagai URL panjang */
    a[href]:after {
        content: "";
    }

    /* Page break manual kalau dokumen lebih dari 1 halaman */
    .page-break {
        page-break-before: always;
        break-before: page;
    }

    .barcode-item {
        page-break-inside: avoid;
        break-inside: avoid;
    }

    .qrcode-wrapper {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 8px;
        margin: 8px;
        border: 1px solid #000;
        border-radius: 6px;
        width: fit-content;
        page-break-inside: avoid;
    }

    .qrcode-label {
        margin-top: 6px;
        font-size: 18px;
        font-weight: 600;
        text-align: center;
        word-break: break-all;
        /* kalau value panjang */
    }
}
</style>
<template>
    <div class="print-page">
        <div v-for="(item, index) in listBarcode" :key="item.id" class="qrcode-wrapper">
            <qrcode-vue :value="item.barcode" :size="240" level="M" :margin="2" />
            <div class="qrcode-label">
                {{ item.barcode }}
            </div>
        </div>
    </div>
</template>

<script>
import QrcodeVue from 'qrcode.vue'

export default {
    components: {
        QrcodeVue
    },
    props: ['listBarcode'],
    data() {
        return {
            options: {
                format: 'CODE128',    // jenis barcode
                lineColor: '#000000',
                width: 2,
                height: 60,
                displayValue: true
            }
        }
    },
}
</script>
