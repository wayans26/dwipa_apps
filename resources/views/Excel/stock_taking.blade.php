<table>
    <tr>
        <td colspan="11"><b>Stock Taking Document</b></td>
    </tr>
    <tr>
        <td colspan="11"><b>{{ $company_profile->company_name }}</b></td>
    </tr>
    <tr>
        <td style="height: 1px" colspan="11"></td>
    </tr>
    <tr>
        <td style="height: 20px" colspan="11"></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2">No. Stock Taking</td>
        <td>:</td>
        <td colspan="2">{{ $stok_taking->code }}</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2">Taking Date</td>
        <td>:</td>
        <td colspan="2">{{ $stok_taking->stok_taking_date }}</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2">Reponsibility Person</td>
        <td>:</td>
        <td colspan="2">{{ $stok_taking->employee_name }}</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2">Note</td>
        <td>:</td>
        <td colspan="2">{{ $stok_taking->note === null ? '-' : $stok_taking->note }}</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2">Reason</td>
        <td>:</td>
        <td colspan="2">{{ $stok_taking->reason === null ? '-' : $stok_taking->reason }}</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2">Status</td>
        <td>:</td>
        <td colspan="2">
            {{ $stok_taking->status === 0 ? 'Draft' : 'Completed' }}
        </td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2">Progress</td>
        <td>:</td>
        <td colspan="2">
            {{ number_format($stok_taking->total_verified, 0, ',', '.') }} of
            {{ number_format($stok_taking->total_barcode, 0, ',', '.') }}
            ({{ number_format(($stok_taking->total_verified / $stok_taking->total_barcode) * 100, 0, ',', '.') }}%)
        </td>
    </tr>
    <tr>
        <td style="color: black;width: 15px"></td>
        <td style="color: black;width: 35px"><b></b></td>
        <td style="color: black;width: 245px" colspan="3"><b></b></td>
        <td style="color: black;width: 175px"><b></b></td>
        <td style="color: black;width: 280px"><b></b></td>
        <td style="color: black;width: 280px"><b></b></td>
        <td style="color: black;width: 210px"><b></b></td>
        <td style="color: black;width: 210px"><b></b></td>
        <td style="color: black;width: 210px"><b></b></td>
        <td style="color: black;width: 210px"><b></b></td>
    </tr>
    <tr>
        <td style="color: black;width: 15px"></td>
        <td style="color: black;width: 35px"><b>NO</b></td>
        <td style="color: black;width: 245px" colspan="3"><b>barcode</b></td>
        <td style="color: black;width: 175px"><b>Asset Code</b></td>
        <td style="color: black;width: 280px"><b>Asset Name</b></td>
        <td style="color: black;width: 280px"><b>Group</b></td>
        <td style="color: black;width: 210px"><b>Location</b></td>
        <td style="color: black;width: 210px"><b>Status</b></td>
        <td style="color: black;width: 210px"><b>Verified At</b></td>
    </tr>
    @foreach ($stok_taking_barcode as $key => $item)
        <tr>
            <td></td>
            <td>{{ $key + 1 }}</td>
            <td colspan="3">'{{ $item->barcode }}</td>
            <td>'{{ $item->asset_id }}</td>
            <td>{{ $item->asset_name }}</td>
            <td>{{ $item->group_name }}</td>
            <td>{{ $item->location_name }}</td>
            <td>{{ $item->status === 1 ? 'Verified' : 'Not Verified' }}</td>
            <td>{{ $item->status === 1 ? \Carbon\Carbon::parse($item->created_at)->format('Y-m-d H:i:s') : 'N/A' }}
            </td>
    @endforeach
</table>
