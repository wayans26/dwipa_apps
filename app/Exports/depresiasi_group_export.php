<?php

namespace App\Exports;

use App\Models\asset;
use App\Models\asset_group;
use App\Models\asset_location;
use App\Models\asset_sub_group;
use App\Models\bussines_unit;
use Carbon\Carbon;
use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use DB;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class depresiasi_group_export implements FromView, WithEvents, WithTitle
{
    use RegistersEventListeners;

    protected $lengthRecord = 0;
    protected $data;
    protected $groupedData;

    protected $filter;
    public function __construct($filter)
    {
        //
        $this->filter = $filter;
    }

    public function view(): View
    {
        $filter = $this->filter;
        $period = Carbon::createFromFormat('Y-m', $this->filter['period'])->format('m/Y');
        $this->data = asset::join('asset_groups', 'assets.asset_group_id', '=', 'asset_groups.id')
            ->leftJoin('asset_depresiasis', 'assets.id', '=', 'asset_depresiasis.asset_id')
            ->leftJoin('asset_depresiasi_details as add', function ($join) use ($period, $filter) {
                $join->on('asset_depresiasis.id', '=', 'add.asset_depresiasi_id')
                    ->where([
                        'add.periode' => $period,
                        'add.type' => $filter['type']
                    ]);
            })
            ->leftJoin('asset_purchases', 'assets.asset_purchase_id', '=', 'asset_purchases.id')
            ->when($filter['business_unit_id'] !== 'all', function ($q) use ($filter) {
                return $q->where('assets.business_unit_id', $filter['business_unit_id']);
            })
            ->when($filter['asset_group_id'] !== 'all', function ($q) use ($filter) {
                return $q->where('assets.asset_group_id', $filter['asset_group_id']);
            })
            ->when($filter['asset_sub_group_id'] !== 'all', function ($q) use ($filter) {
                $q->where('assets.asset_sub_group_id', $filter['asset_sub_group_id']);
            })
            ->when($filter['asset_location_id'] !== 'all', function ($q) use ($filter) {
                $q->where('assets.asset_location_id', $filter['asset_location_id']);
            })
            ->select(
                'assets.id',
                'assets.name as asset_name',
                'asset_depresiasis.depreciation_date',
                'asset_depresiasis.monthly_depreciation',
                'assets.qty',
                'asset_groups.name as group_name',
                DB::raw('COALESCE(add.nilai_saldo, 0) as nilai_saldo'),
                DB::raw('COALESCE(asset_purchases.purchase_price, 0) as purchase_price'),
                DB::raw('COALESCE(asset_depresiasis.salvage_value, 0) as salvage_value'),
                DB::raw('COALESCE(asset_depresiasis.commecial_period, 0) as commecial_period'),
                DB::raw('COALESCE(asset_depresiasis.fiscal_period, 0) as fiscal_period'),
            )->get();

        $this->groupedData = $this->data
            ->groupBy('group_name')
            ->map(function ($items, $group) use ($filter) {
                $assets = $items->map(function ($item) use ($filter) {
                    $total_cost = $item->qty * $item->purchase_price;
                    $devine_period = $filter['type'] === 'commercial' ? $item->commecial_period : $item->fiscal_period;
                    $balance_period_diff = (int) Carbon::parse($item->depreciation_date)->startOfMonth()->diffInMonths(Carbon::now()->subMonth()->endOfMonth());
                    $balance_period_diff  = $balance_period_diff > $devine_period ? $devine_period : $balance_period_diff;
                    $accum_period_diff = (int) Carbon::parse($item->depreciation_date)->startOfMonth()->diffInMonths(Carbon::now()->endOfMonth());
                    $accum_period_diff  = ($accum_period_diff + 1) > $devine_period ? $devine_period : $accum_period_diff + 1;

                    $item->id = $item->id;
                    $item->asset_name = $item->asset_name;
                    $item->depreciation_date = $item->depreciation_date;
                    $item->monthly_depreciation = $item->monthly_depreciation;
                    $item->commecial_period = $item->commecial_period;
                    $item->fiscal_period = $item->fiscal_period;
                    $item->qty = $item->qty;
                    $item->group_name = $item->group_name;
                    $item->nilai_saldo = $item->nilai_saldo;
                    $item->total_cost = $total_cost;
                    $item->balance = ($devine_period === 0 || $balance_period_diff < 0) ? 0 : floor(((($total_cost - $item->salvage_value) / $devine_period) * $balance_period_diff));
                    $item->accumulated_depreciation = $devine_period === 0 ? 0 : floor(((($total_cost - $item->salvage_value) / $devine_period) * $accum_period_diff));

                    return $item;
                });
                return [
                    'group_name' => $group,
                    'assets' => $assets->values()
                ];
            })
            ->values();

        $this->lengthRecord = 1;
        $business_unit = $filter['business_unit_id'] === 'all' ? null : bussines_unit::find($filter['business_unit_id']);
        $asset_group = $filter['asset_group_id'] === 'all' ? null : asset_group::find($filter['asset_group_id']);
        $asset_sub_group = $filter['asset_sub_group_id'] === 'all' ? null : asset_sub_group::find($filter['asset_sub_group_id']);
        $asset_location = $filter['asset_location_id'] === 'all' ? null : asset_location::find($filter['asset_location_id']);

        // dd($this->groupedData);

        return view('Excel.depresiasi_group', [
            'data'              => $this->groupedData,
            'business_unit'     => $filter['business_unit_id'] === 'all' ? $filter['business_unit_id'] : (empty($business_unit) ? "-" : $business_unit->name),
            'asset_group'       => $filter['asset_group_id'] === 'all' ? $filter['asset_group_id'] : (empty($asset_group) ? "-" : $asset_group->name),
            'asset_sub_group'   => $filter['asset_sub_group_id'] === 'all' ? $filter['asset_sub_group_id'] : (empty($asset_sub_group) ? "-" : $asset_sub_group->name),
            'asset_location'    => $filter['asset_location_id'] === 'all' ? $filter['asset_location_id'] : (empty($asset_location) ? "-" : $asset_location->name),
            'periode'           => Carbon::createFromFormat('Y-m', $filter['period'])->format('F Y'),
            'type'              => $filter['type']
        ]);
    }


    public function afterSheet(AfterSheet $event)
    {
        $sheet = $event->sheet->getDelegate();
        $first = 11;
        $last = 11;
        $extra = 1;
        $skip = 3;

        $qty = 0;
        $total_cost = 0;
        $balance = 0;
        $current_depend = 0;
        $accum_depend = 0;
        $net_value = 0;

        $sheet->getStyle('A1:N1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 22
            ],
            'alignment' => [
                'horizontal'    => Alignment::HORIZONTAL_CENTER,
                'vertical'      => Alignment::VERTICAL_CENTER
            ]
        ]);

        foreach ($this->groupedData as $key => $value) {
            $last += sizeof($value['assets']) + $extra;
            $sheet->getStyle('A' . $first . ':N' . $last)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ]
                ]
            ]);
            $sheet->getStyle('H' . ($first + 1) . ':N' . $last)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('A' . $first . ':A' . $last)->getAlignment()->applyFromArray([
                'horizontal'    => Alignment::HORIZONTAL_CENTER,
                'vertical'      => Alignment::VERTICAL_CENTER
            ]);

            $sheet->getStyle('B' . $first . ':G' . $last)->getAlignment()->applyFromArray([
                'horizontal'    => Alignment::HORIZONTAL_LEFT,
                'vertical'      => Alignment::VERTICAL_TOP
            ]);
            $sheet->getStyle('A' . ($first - 1) . ':F' . ($first - 1))->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal'    => Alignment::HORIZONTAL_LEFT,
                    'vertical'      => Alignment::VERTICAL_TOP
                ]
            ]);
            $sheet->getStyle('A' . ($last) . ':N' . $last)->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
            ]);
            // $sheet->setCellValue('I' . $last, '=SUM(I' . ($first + 1) . ':I' . ($last - 1) . ')');
            // $sheet->setCellValue('J' . $last, '=SUM(J' . ($first + 1) . ':J' . ($last - 1) . ')');
            // $sheet->setCellValue('K' . $last, '=SUM(K' . ($first + 1) . ':K' . ($last - 1) . ')');
            // $sheet->setCellValue('L' . $last, '=SUM(L' . ($first + 1) . ':L' . ($last - 1) . ')');
            // $sheet->setCellValue('M' . $last, '=SUM(M' . ($first + 1) . ':M' . ($last - 1) . ')');
            // $sheet->setCellValue('N' . $last, '=SUM(N' . ($first + 1) . ':N' . ($last - 1) . ')');

            $sub_qty = 0;
            $sub_total_cost = 0;
            $sub_balance = 0;
            $sub_current_depend = 0;
            $sub_accum_depend = 0;
            $sub_net_value = 0;

            foreach ($value['assets'] as $asset_key => $asset_value) {
                $qty += $asset_value->qty;
                $total_cost += $asset_value->total_cost;
                $balance += $asset_value->balance;
                $current_depend += $asset_value->monthly_depreciation;
                $accum_depend += $asset_value->accumulated_depreciation;
                $net_value += $asset_value->nilai_saldo;

                $sub_qty += $asset_value->qty;
                $sub_total_cost += $asset_value->total_cost;
                $sub_balance += $asset_value->balance;
                $sub_current_depend += $asset_value->monthly_depreciation;
                $sub_accum_depend += $asset_value->accumulated_depreciation;
                $sub_net_value += $asset_value->nilai_saldo;
            }

            $sheet->setCellValue('I' . $last, $sub_qty);
            $sheet->setCellValue('J' . $last, $sub_total_cost);
            $sheet->setCellValue('K' . $last, $sub_balance);
            $sheet->setCellValue('L' . $last, $sub_current_depend);
            $sheet->setCellValue('M' . $last, $sub_accum_depend);
            $sheet->setCellValue('N' . $last, $sub_net_value);

            $first = $last + $skip;
            $last += $skip;
        }

        $sheet->getStyle('H' . ($last - 1) . ':N' . ($last - 1))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('A' . ($last - 1) . ':N' . ($last - 1))->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);
        $sheet->getStyle('A' . ($last - 1) . ':N' . ($last - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ]
            ]
        ]);
        $sheet->setCellValue('I' . ($last - 1), $qty);
        $sheet->setCellValue('J' . ($last - 1), $total_cost);
        $sheet->setCellValue('K' . ($last - 1), $balance);
        $sheet->setCellValue('L' . ($last - 1), $current_depend);
        $sheet->setCellValue('M' . ($last - 1), $accum_depend);
        $sheet->setCellValue('N' . ($last - 1), $net_value);
        $sheet->setShowGridlines(false);
    }

    public function title(): string
    {
        return 'Depreciation By Group';
    }
}
