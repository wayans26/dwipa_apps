<?php

namespace App\Exports;

use App\Models\asset_check_out_in_barcode;
use App\Models\company_profile;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class asset_checkout implements FromView, WithEvents, WithTitle
{
    use RegistersEventListeners;

    protected $asset_checkout;
    protected $lengthRecord = 0;

    function __construct($asset_checkout)
    {
        $this->asset_checkout = $asset_checkout;
    }


    public function view(): View
    {
        $asset_checkout_barcode = asset_check_out_in_barcode::join('asset_barcodes', 'asset_barcodes.id', '=', 'asset_check_out_in_barcodes.asset_barcode_id')
            ->join('assets', 'asset_barcodes.asset_id', '=', 'assets.id')
            ->join('asset_groups', 'asset_groups.id', '=', 'assets.asset_group_id')
            ->join('asset_locations', 'asset_barcodes.asset_location_id', '=', 'asset_locations.id')
            ->leftJoin('employees', 'asset_check_out_in_barcodes.employee_id', '=', 'employees.id')
            ->where('asset_check_out_in_barcodes.asset_check_out_in_id', $this->asset_checkout->id)
            ->select(
                'asset_barcodes.code_barcode as barcode',
                'assets.id as asset_id',
                'assets.name as asset_name',
                'asset_groups.name as group_name',
                'asset_locations.name as location_name',
                'asset_check_out_in_barcodes.note as note',
                'employees.name as check_in_by',
                'asset_check_out_in_barcodes.check_in_date'
            )
            ->get();

        $company_profile = company_profile::first();

        $this->lengthRecord = sizeof($asset_checkout_barcode);
        return view('Excel.asset_checkout', [
            'company_profile'           => $company_profile,
            'asset_checkout'            => $this->asset_checkout,
            'asset_checkout_barcode'    => $asset_checkout_barcode
        ]);
    }

    public function afterSheet(AfterSheet $event)
    {
        $sheet = $event->sheet->getDelegate();
        $sheet->getStyle('B3:L3')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);
        $sheet->getStyle('B12:L' . ($this->lengthRecord + 12))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);
        $sheet->getStyle('A1:L1')->getFont()->setSize(16);
        $sheet->getStyle('A2:L2')->getFont()->setSize(24);
        $sheet->getStyle('A1:L2')->getAlignment()->applyFromArray([
            'horizontal'    => Alignment::HORIZONTAL_CENTER,
            'vertical'      => Alignment::VERTICAL_CENTER
        ]);
        $sheet->getStyle('A12:L12')->getAlignment()->applyFromArray([
            'horizontal'    => Alignment::HORIZONTAL_CENTER,
            'vertical'      => Alignment::VERTICAL_CENTER
        ]);
        $sheet->getStyle('B12:B' . ($this->lengthRecord + 12))->getAlignment()->applyFromArray([
            'horizontal'    => Alignment::HORIZONTAL_CENTER,
            'vertical'      => Alignment::VERTICAL_CENTER
        ]);
        $sheet->setShowGridlines(false);
    }

    public function title(): string
    {
        return 'Asset Check Out';
    }
}
