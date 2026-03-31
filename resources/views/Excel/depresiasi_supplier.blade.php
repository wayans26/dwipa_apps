<table>
    <tr>
        <td colspan="13">DEPRECIATION GROUP BY SUPPLIER</td>
    </tr>
    <tr>
        <td colspan="13"></td>
    </tr>
    <tr>
        <td colspan="13"></td>
    </tr>

    <tr>
        <td colspan="4">Business Unit</td>
        <td style="width: 15px">:</td>
        <td>{{ $business_unit }}</td>
    </tr>
    <tr>
        <td colspan="4">Periode</td>
        <td style="width: 15px">:</td>
        <td>{{ $periode }}</td>
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
        <td style="color: black;width: 35px"></td>
        <td style="color: black;width: 175px" colspan="4"></td>
        <td style="color: black;width: 280px"></td>
        <td style="color: black;width: 140px"></td>
        <td style="color: black;width: 140px"></td>
        <td style="color: black;width: 140px"></td>
        <td style="color: black;width: 140px"></td>
        <td style="color: black;width: 140px"></td>
        <td style="color: black;width: 140px"></td>
        <td style="color: black;width: 140px"></td>
        <td style="color: black;width: 140px"></td>
    </tr>

    @foreach ($data as $key => $item)
        <tr>
            <td colspan="4">SUPPLIER</td>
            <td>:</td>
            <td>{{ $item['supplier_name'] }}</td>
        </tr>
        <tr>
            <td style="color: black;width: 35px">No</td>
            <td style="color: black;width: 175px" colspan="4">Asset Code</td>
            <td style="color: black;width: 280px">Asset Name</td>
            <td style="color: black;width: 140px">Depreciation Date</td>
            <td style="color: black;width: 140px">Benefit</td>
            <td style="color: black;width: 140px">Quantity</td>
            <td style="color: black;width: 140px">Total Cost</td>
            <td style="color: black;width: 140px">Balance B/F</td>
            <td style="color: black;width: 140px">Current Depn</td>
            <td style="color: black;width: 140px">Accum Depn</td>
            <td style="color: black;width: 140px">Net Book Value</td>
        </tr>
        @foreach ($item['assets'] as $key => $asset)
            <tr>
                <td style="color: black;width: 35px">{{ $key + 1 }}</td>
                <td style="color: black;width: 175px" colspan="4">'{{ $asset->id }}</td>
                <td style="color: black;width: 280px">{{ $asset->asset_name }}</td>
                <td style="color: black;width: 140px">{{ $asset->depreciation_date }}</td>
                <td style="color: black;width: 140px">
                    {{ $type === 'commercial' ? $asset->commecial_period : $asset->fiscal_period }}</td>
                <td style="color: black;width: 140px">{{ $asset->qty }}</td>
                <td style="color: black;width: 140px">{{ $asset->total_cost }}</td>
                <td style="color: black;width: 140px">{{ $asset->balance }}</td>
                <td style="color: black;width: 140px">{{ $asset->monthly_depreciation }}</td>
                <td style="color: black;width: 140px">{{ $asset->accumulated_depreciation }}</td>
                <td style="color: black;width: 140px">{{ $asset->nilai_saldo }}</td>
            </tr>
        @endforeach
        <tr>
            <td style="color: black;width: 35px"></td>
            <td style="color: black;width: 175px" colspan="4">SUB TOTAL ASSET GROUP</td>
            <td style="color: black;width: 280px"></td>
            <td style="color: black;width: 140px"></td>
            <td style="color: black;width: 140px"></td>
            <td style="color: black;width: 140px"></td>
            <td style="color: black;width: 140px"></td>
            <td style="color: black;width: 140px"></td>
            <td style="color: black;width: 140px"></td>
            <td style="color: black;width: 140px"></td>
            <td style="color: black;width: 140px"></td>
        </tr>
        <tr>
            <td style="color: black;width: 35px"></td>
            <td style="color: black;width: 175px" colspan="4"></td>
            <td style="color: black;width: 280px"></td>
            <td style="color: black;width: 140px"></td>
            <td style="color: black;width: 140px"></td>
            <td style="color: black;width: 140px"></td>
            <td style="color: black;width: 140px"></td>
            <td style="color: black;width: 140px"></td>
            <td style="color: black;width: 140px"></td>
            <td style="color: black;width: 140px"></td>
            <td style="color: black;width: 140px"></td>
        </tr>
    @endforeach
    <tr>
        <td style="color: black;width: 35px"></td>
        <td style="color: black;width: 175px" colspan="4">GRAND TOTAL ASSET GROUP</td>
        <td style="color: black;width: 280px"></td>
        <td style="color: black;width: 140px"></td>
        <td style="color: black;width: 140px"></td>
        <td style="color: black;width: 140px"></td>
        <td style="color: black;width: 140px"></td>
        <td style="color: black;width: 140px"></td>
        <td style="color: black;width: 140px"></td>
        <td style="color: black;width: 140px"></td>
        <td style="color: black;width: 140px"></td>
    </tr>
</table>
