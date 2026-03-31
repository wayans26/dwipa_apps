<table>
    <tr>
        <td colspan="9"><b>Asset Disposal Document</b></td>
    </tr>
    <tr>
        <td colspan="9"><b>{{ $company_profile->company_name }}</b></td>
    </tr>
    <tr>
        <td style="height: 1px" colspan="9"></td>
    </tr>
    <tr>
        <td style="height: 20px" colspan="9"></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2">No. Disposal</td>
        <td>:</td>
        <td colspan="2">{{ $asset_disposal->code }}</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2">Diposal Date</td>
        <td>:</td>
        <td colspan="2">{{ $asset_disposal->date_string }}</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2">Responsibility Person</td>
        <td>:</td>
        <td colspan="2">{{ $asset_disposal->employee_name }}</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2">Note</td>
        <td>:</td>
        <td colspan="2">{{ $asset_disposal->note === null ? '-' : $asset_disposal->note }}</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2">Status</td>
        <td>:</td>
        <td colspan="2">{{ $asset_disposal->status === 1 ? 'Complete' : 'Draft' }}</td>
    </tr>
    <tr>
        <td style="color: black;width: 15px"></td>
        <td style="color: black;width: 35px"><b></b></td>
        <td style="color: black;width: 245px" colspan="3"><b></b></td>
        <td style="color: black;width: 175px"><b></b></td>
        <td style="color: black;width: 280px"><b></b></td>
        <td style="color: black;width: 280px"><b></b></td>
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
    </tr>
    @foreach ($asset_barcode as $key => $item)
        <tr>
            <td></td>
            <td>{{ $key + 1 }}</td>
            <td colspan="3">'{{ $item->barcode }}</td>
            <td>'{{ $item->asset_id }}</td>
            <td>{{ $item->asset_name }}</td>
            <td>{{ $item->group_name }}</td>
            <td>{{ $item->location_name }}</td>
    @endforeach
</table>
