<?php

namespace App\Exports;

use App\Models\asset_disposal_barcode;
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

class asset_disposal_export implements FromView, WithEvents, WithTitle
{
    use RegistersEventListeners;

    protected $asset_disposal;
    protected $lengthRecord = 0;

    function __construct($asset_disposal)
    {
        $this->asset_disposal = $asset_disposal;
    }


    public function view(): View
    {
        $asset_diposal_barcode = asset_disposal_barcode::join('asset_barcodes', 'asset_barcodes.id', '=', 'asset_disposal_barcodes.asset_barcode_id')
            ->join('assets', 'asset_barcodes.asset_id', '=', 'assets.id')
            ->join('asset_groups', 'assets.asset_group_id', '=', 'asset_groups.id')
            ->join('asset_locations', 'asset_barcodes.asset_location_id', '=', 'asset_locations.id')
            ->select(
                'asset_barcodes.code_barcode as barcode',
                'assets.name as asset_name',
                'assets.id as asset_id',
                'asset_groups.name as group_name',
                'asset_locations.name as location_name',
            )
            ->where('asset_disposal_barcodes.asset_disposal_id', $this->asset_disposal->id)->get();

        $company_profile = company_profile::first();
        $this->asset_disposal->date_string = Carbon::createFromFormat('Y-m-d', $this->asset_disposal->disposal_date)->format('d F Y');
        $this->lengthRecord = sizeof($asset_diposal_barcode);
        return view('Excel.asset_disposal', [
            'asset_barcode'     => $asset_diposal_barcode,
            'asset_disposal'    => $this->asset_disposal,
            'company_profile'   => $company_profile
        ]);
    }

    public function afterSheet(AfterSheet $event)
    {
        $sheet = $event->sheet->getDelegate();
        $sheet->getStyle('B3:I3')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);
        $sheet->getStyle('B11:I' . ($this->lengthRecord + 11))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);
        $sheet->getStyle('A1:I1')->getFont()->setSize(16);
        $sheet->getStyle('A2:I2')->getFont()->setSize(24);
        $sheet->getStyle('A1:I2')->getAlignment()->applyFromArray([
            'horizontal'    => Alignment::HORIZONTAL_CENTER,
            'vertical'      => Alignment::VERTICAL_CENTER
        ]);
        $sheet->getStyle('A11:I11')->getAlignment()->applyFromArray([
            'horizontal'    => Alignment::HORIZONTAL_CENTER,
            'vertical'      => Alignment::VERTICAL_CENTER
        ]);
        $sheet->getStyle('B11:B' . ($this->lengthRecord + 11))->getAlignment()->applyFromArray([
            'horizontal'    => Alignment::HORIZONTAL_CENTER,
            'vertical'      => Alignment::VERTICAL_CENTER
        ]);
        $sheet->setShowGridlines(false);
    }

    public function title(): string
    {
        return 'Asset Disposal';
    }
}
