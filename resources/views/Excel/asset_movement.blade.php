<table>
    <tr>
        <td colspan="11"><b>Asset Movement Document</b></td>
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
        <td colspan="2">{{ $asset_movement->code }}</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2">Movement Date</td>
        <td>:</td>
        <td colspan="2">{{ $asset_movement->date_string }}</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2">Description</td>
        <td>:</td>
        <td colspan="2">{{ $asset_movement->description }}</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2">Status</td>
        <td>:</td>
        <td colspan="2">{{ $asset_movement->status === 1 ? 'Complete' : 'Draft' }}</td>
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
    </tr>
    <tr>
        <td style="color: black;width: 15px"></td>
        <td style="color: black;width: 35px"><b>NO</b></td>
        <td style="color: black;width: 245px" colspan="3"><b>barcode</b></td>
        <td style="color: black;width: 175px"><b>Asset Code</b></td>
        <td style="color: black;width: 280px"><b>Asset Name</b></td>
        <td style="color: black;width: 280px"><b>Group</b></td>
        <td style="color: black;width: 210px"><b>Move From</b></td>
        <td style="color: black;width: 210px"><b>Move To</b></td>
        <td style="color: black;width: 210px"><b>Note</b></td>
    </tr>
    @foreach ($asset_barcode as $key => $item)
        <tr>
            <td></td>
            <td>{{ $key + 1 }}</td>
            <td colspan="3">'{{ $item->barcode }}</td>
            <td>'{{ $item->asset_id }}</td>
            <td>{{ $item->asset_name }}</td>
            <td>{{ $item->group_name }}</td>
            <td>{{ $item->old_location_name }}</td>
            <td>{{ $item->new_location_name }}</td>
            <td>{{ $item->note }}</td>
    @endforeach
</table>
