<table>
    <tr>
        <td colspan="11"><b>Check-Out Document</b></td>
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
        <td colspan="2">Trn. Code</td>
        <td>:</td>
        <td colspan="2">{{ $asset_checkout->code }}</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2">Register Date</td>
        <td>:</td>
        <td colspan="2">{{ $asset_checkout->register_date }}</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2">Check Out By</td>
        <td>:</td>
        <td colspan="2">{{ $asset_checkout->employee_name }}</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2">Check Out Date</td>
        <td>:</td>
        <td colspan="2">{{ $asset_checkout->check_out_date === null ? '-' : $asset_checkout->check_out_date }}</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2">Note</td>
        <td>:</td>
        <td colspan="2">{{ $asset_checkout->note }}</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2">Status</td>
        <td>:</td>
        <td colspan="2">
            {{ $asset_checkout->status === 1 ? 'Check Out Verified' : ($asset_checkout->status === 2 ? 'Check Out Completed' : 'Draft') }}
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
        <td style="color: black;width: 210px"><b>Check In At</b></td>
        <td style="color: black;width: 210px"><b>Check In By</b></td>
        <td style="color: black;width: 210px"><b>Note</b></td>
    </tr>
    @foreach ($asset_checkout_barcode as $key => $item)
        <tr>
            <td></td>
            <td>{{ $key + 1 }}</td>
            <td colspan="3">'{{ $item->barcode }}</td>
            <td>'{{ $item->asset_id }}</td>
            <td>{{ $item->asset_name }}</td>
            <td>{{ $item->group_name }}</td>
            <td>{{ $item->location_name }}</td>
            <td>{{ $item->check_in_date }}</td>
            <td>{{ $item->check_in_by }}</td>
            <td>{{ $item->note }}</td>
    @endforeach
</table>
