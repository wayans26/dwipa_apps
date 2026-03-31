<style>
/* Atur ukuran kertas & margin A4 */
@page {
    size: A4 portrait;
    /* ganti ke 'landscape' kalau mau mendatar */
    margin: 10mm 15mm;
    /* atas-bawah 10mm, kiri-kanan 15mm */
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
        grid-template-columns: repeat(2, 1fr);
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
}
</style>
<template>
    <div class="print-page">
        <div v-for="(item, index) in listBarcode" :key="item.id" class="barcode-item">
            <vue-barcode :key="item.id" :value="item.barcode"
                :options="{ displayValue: true, height: 60, width: 2, margin: 10 }" />
        </div>
    </div>
</template>

<script>
import VueBarcode from '@chenfengyuan/vue-barcode';

export default {
    components: {
        VueBarcode
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
