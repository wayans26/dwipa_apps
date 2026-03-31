<style>
    @page {
        size: 330mm 210mm;
        margin: 10mm;
    }

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 9px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    th,
    td {
        border: 1px solid #000;
        padding: 4px;
        word-break: break-word;
    }

    thead {
        display: table-header-group;
    }
</style>
<table>
    <tr>
        <td colspan="13">Barcode Catalog</td>
    </tr>
    <tr>
        <td colspan="13"></td>
    </tr>
    <tr>
        <td colspan="13"></td>
    </tr>

    <tr>
        <td colspan="4">Company Name</td>
        <td style="width: 15px">:</td>
        <td>{{ $business_unit }}</td>
    </tr>
    <tr>
        <td colspan="4">Asset Group</td>
        <td style="width: 15px">:</td>
        <td>{{ $asset_group }}</td>
    </tr>
    <tr>
        <td colspan="4">Asset Sub Group</td>
        <td style="width: 15px">:</td>
        <td>{{ $asset_sub_group }}</td>
    </tr>
    <tr>
        <td colspan="4">Asset Location</td>
        <td style="width: 15px">:</td>
        <td>{{ $asset_location }}</td>
    </tr>
    <tr>
        <td colspan="4">Vendor</td>
        <td style="width: 15px">:</td>
        <td>{{ $supplier }}</td>
    </tr>
    <tr>
        <td style="color: black;width: 35px"></td>
        <td style="color: black;width: 175px" colspan="4"></td>
        <td style="color: black;width: 350px"></td>
        <td style="color: black;width: 140px"></td>
        <td style="color: black;width: 350px"></td>
        <td style="color: black;width: 140px"></td>
        <td style="color: black;width: 140px"></td>
        <td style="color: black;width: 140px"></td>
        <td style="color: black;width: 350px"></td>
        <td style="color: black;width: 230px"></td>
    </tr>
    <tr>
        <td style="color: black;width: 35px">No</td>
        <td style="color: black;width: 175px" colspan="4">Asset Code</td>
        <td style="color: black;width: 350px">Asset Name</td>
        <td style="color: black;width: 140px">Register Date</td>
        <td style="color: black;width: 350px">Barcode</td>
        <td style="color: black;width: 210px">Asset Group</td>
        <td style="color: black;width: 210px">Asset Sub Group</td>
        <td style="color: black;width: 210px">Location</td>
        <td style="color: black;width: 210px">Description</td>
        <td style="color: black;width: 230px">Image</td>
    </tr>
    @foreach ($data as $key => $item)
        <tr>
            <td style="color: black;width: 35px">{{ $key + 1 }}</td>
            <td style="color: black;width: 175px" colspan="4">'{{ $item->asset_id }}</td>
            <td style="color: black;width: 350px">{{ $item->asset_name }}</td>
            <td style="color: black;width: 140px">{{ $item->register_date }}</td>
            <td style="color: black;width: 350px">{{ $item->barcode }}</td>
            <td style="color: black;width: 210px">{{ $item->group_name }}</td>
            <td style="color: black;width: 210px">{{ $item->sub_group_name }}</td>
            <td style="color: black;width: 210px">{{ $item->location_name }}</td>
            <td style="color: black;width: 210px">
                {{ 'Purchase No : ' . $item->purchase_no }}
                <br style="mso-data-placement:same-cell;" />
                {{ 'Purchase Date : ' . $item->purchase_date }}
                <br style="mso-data-placement:same-cell;" />
                {{ 'Supplier : ' . $item->supplier_name }}
                <br style="mso-data-placement:same-cell;" />
                {{ 'Invoice No : ' . $item->invoice_no }}
            </td>
        </tr>
    @endforeach
</table>
