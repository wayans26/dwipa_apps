<?php

namespace App\Exports;

use App\Models\company_profile;
use App\Models\stok_taking;
use App\Models\stok_taking_barcode;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class stok_taking_export implements FromView, WithEvents, WithTitle
{
    use RegistersEventListeners;

    protected $stok_taking;
    protected $lengthRecord = 0;

    function __construct($stok_taking)
    {
        $this->stok_taking = $stok_taking;
    }


    public function view(): View
    {
        $stok_taking_barcode = stok_taking_barcode::join('asset_barcodes', 'stok_taking_barcodes.asset_barcode_id', '=', 'asset_barcodes.id')
            ->join('assets', 'asset_barcodes.asset_id', '=', 'assets.id')
            ->join('asset_groups', 'assets.asset_group_id', '=', 'asset_groups.id')
            ->join('asset_locations', 'asset_barcodes.asset_location_id', '=', 'asset_locations.id')
            ->where('stok_taking_barcodes.stok_taking_id', $this->stok_taking->id)
            ->select(
                'asset_barcodes.code_barcode as barcode',
                'assets.id as asset_id',
                'assets.name as asset_name',
                'asset_groups.name as group_name',
                'asset_locations.name as location_name',
                'stok_taking_barcodes.status',
                'stok_taking_barcodes.updated_at'
            )
            ->orderBy('stok_taking_barcodes.status', 'desc')
            ->get();

        $company_profile = company_profile::first();

        $this->lengthRecord = sizeof($stok_taking_barcode);
        return view('Excel.stock_taking', [
            'company_profile'           => $company_profile,
            'stok_taking'               => $this->stok_taking,
            'stok_taking_barcode'       => $stok_taking_barcode
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
        $sheet->getStyle('B13:K' . ($this->lengthRecord + 13))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);
        $sheet->getStyle('A1:K1')->getFont()->setSize(16);
        $sheet->getStyle('A2:K2')->getFont()->setSize(24);
        $sheet->getStyle('A1:K2')->getAlignment()->applyFromArray([
            'horizontal'    => Alignment::HORIZONTAL_CENTER,
            'vertical'      => Alignment::VERTICAL_CENTER
        ]);
        $sheet->getStyle('A12:K12')->getAlignment()->applyFromArray([
            'horizontal'    => Alignment::HORIZONTAL_CENTER,
            'vertical'      => Alignment::VERTICAL_CENTER
        ]);
        $sheet->getStyle('B13:B' . ($this->lengthRecord + 13))->getAlignment()->applyFromArray([
            'horizontal'    => Alignment::HORIZONTAL_CENTER,
            'vertical'      => Alignment::VERTICAL_CENTER
        ]);
        $sheet->setShowGridlines(false);
    }

    public function title(): string
    {
        return 'Stok Taking';
    }
}
