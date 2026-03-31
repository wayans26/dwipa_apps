<?php

namespace App\Exports;

use App\Models\asset_movement_barcode;
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
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class asset_movement implements FromView, WithEvents, WithTitle
{
    use RegistersEventListeners;

    protected $asset_movement;
    protected $lengthRecord = 0;

    function __construct($asset_movement)
    {
        $this->asset_movement = $asset_movement;
    }


    public function view(): View
    {
        $asset_movement_barcode = asset_movement_barcode::join('asset_barcodes', 'asset_barcodes.id', '=', 'asset_movement_barcodes.asset_barcode_id')
            ->join('assets', 'assets.id', '=', 'asset_barcodes.asset_id')
            ->join('asset_groups', 'asset_groups.id', '=', 'assets.asset_group_id')
            ->join('asset_locations as old_location', 'old_location.id', '=', 'asset_movement_barcodes.asset_location_old_id')
            ->join('asset_locations as new_location', 'new_location.id', '=', 'asset_movement_barcodes.asset_location_new_id')
            ->select('asset_barcodes.code_barcode as barcode', 'assets.id as asset_id', 'assets.name as asset_name', 'asset_groups.name as group_name', 'old_location.name as old_location_name', 'new_location.name as new_location_name', 'asset_movement_barcodes.note as note')
            ->where('asset_movement_barcodes.asset_movement_id', $this->asset_movement->id)->get();

        $company_profile = company_profile::first();
        $this->asset_movement->date_string = Carbon::createFromFormat('Y-m-d', $this->asset_movement->movement_date)->format('d F Y');
        $this->lengthRecord = sizeof($asset_movement_barcode);
        return view('Excel.asset_movement', [
            'asset_barcode'     => $asset_movement_barcode,
            'asset_movement'    => $this->asset_movement,
            'company_profile'   => $company_profile
        ]);
    }

    public function afterSheet(AfterSheet $event)
    {
        $sheet = $event->sheet->getDelegate();
        $sheet->getStyle('B3:K3')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);
        $sheet->getStyle('B10:K' . ($this->lengthRecord + 10))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);
        $sheet->getStyle('A1:K1')->getFont()->setSize(16);
        $sheet->getStyle('A2:K2')->getFont()->setSize(24);
        $sheet->getStyle('A1:k2')->getAlignment()->applyFromArray([
            'horizontal'    => Alignment::HORIZONTAL_CENTER,
            'vertical'      => Alignment::VERTICAL_CENTER
        ]);
        $sheet->getStyle('A10:K10')->getAlignment()->applyFromArray([
            'horizontal'    => Alignment::HORIZONTAL_CENTER,
            'vertical'      => Alignment::VERTICAL_CENTER
        ]);
        $sheet->getStyle('B10:B' . ($this->lengthRecord + 10))->getAlignment()->applyFromArray([
            'horizontal'    => Alignment::HORIZONTAL_CENTER,
            'vertical'      => Alignment::VERTICAL_CENTER
        ]);
        $sheet->setShowGridlines(false);
    }

    public function title(): string
    {
        return 'Asset Movement';
    }
}
