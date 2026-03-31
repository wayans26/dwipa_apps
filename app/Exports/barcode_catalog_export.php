<?php

namespace App\Exports;

use App\Models\asset_barcode;
use App\Models\asset_group;
use App\Models\asset_location;
use App\Models\asset_sub_group;
use App\Models\bussines_unit;
use App\Models\supplier;
use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Picqer\Barcode\BarcodeGeneratorPNG;

class barcode_catalog_export implements FromView, WithEvents, WithTitle
{
    use RegistersEventListeners;

    protected $lengthRecord = 0;
    protected $data;

    protected $filter;
    public function __construct($filter)
    {
        //
        $this->filter = $filter;
    }

    public function view(): View
    {
        $filter = $this->filter;
        $this->data = asset_barcode::join('assets', 'asset_barcodes.asset_id', '=', 'assets.id')
            ->join('asset_locations', 'asset_barcodes.asset_location_id', '=', 'asset_locations.id')
            ->join('asset_groups', 'assets.asset_group_id', '=', 'asset_groups.id')
            ->leftJoin('asset_sub_groups', 'assets.asset_sub_group_id', '=', 'asset_sub_groups.id')
            ->leftJoin('asset_purchases', 'assets.asset_purchase_id', '=', 'asset_purchases.id')
            ->leftJoin('suppliers', 'asset_purchases.supplier_id', '=', 'suppliers.id')
            ->leftJoin(DB::raw("
                (
                    SELECT ad.asset_id, ad.path
                    FROM asset_documents ad
                    INNER JOIN (
                        SELECT asset_id, MIN(created_at) AS min_created_at
                        FROM asset_documents
                        WHERE type = 'image'
                        GROUP BY asset_id
                    ) x ON x.asset_id = ad.asset_id
                    AND x.min_created_at = ad.created_at
                    WHERE ad.type = 'image'
                ) AS asset_documents
            "), 'asset_documents.asset_id', '=', 'assets.id')
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
            ->when($filter['supplier_id'] !== 'all', function ($q) use ($filter) {
                $q->where('asset_purchases.supplier_id', $filter['supplier_id']);
            })
            ->select(
                'assets.id as asset_id',
                'assets.name as asset_name',
                'assets.register_date',
                'asset_barcodes.code_barcode as barcode',
                'asset_groups.name as group_name',
                'asset_sub_groups.name as sub_group_name',
                'asset_locations.name as location_name',
                'asset_purchases.purchase_no',
                'asset_purchases.purchase_date',
                'suppliers.name as supplier_name',
                'asset_purchases.invoice_no',
                'asset_documents.path as image_path'
            )
            ->get();
        $this->lengthRecord = sizeof($this->data);
        $business_unit = $filter['business_unit_id'] === 'all' ? null : bussines_unit::find($filter['business_unit_id']);
        $asset_group = $filter['asset_group_id'] === 'all' ? null : asset_group::find($filter['asset_group_id']);
        $asset_sub_group = $filter['asset_sub_group_id'] === 'all' ? null : asset_sub_group::find($filter['asset_sub_group_id']);
        $asset_location = $filter['asset_location_id'] === 'all' ? null : asset_location::find($filter['asset_location_id']);
        $supplier = $filter['supplier_id'] === 'all' ? null : supplier::find($filter['supplier_id']);

        return view('Excel.report_asset_barcode_catalog', [
            'data'              => $this->data,
            'business_unit'     => $filter['business_unit_id'] === 'all' ? $filter['business_unit_id'] : (empty($business_unit) ? "-" : $business_unit->name),
            'asset_group'       => $filter['asset_group_id'] === 'all' ? $filter['asset_group_id'] : (empty($asset_group) ? "-" : $asset_group->name),
            'asset_sub_group'   => $filter['asset_sub_group_id'] === 'all' ? $filter['asset_sub_group_id'] : (empty($asset_sub_group) ? "-" : $asset_sub_group->name),
            'asset_location'    => $filter['asset_location_id'] === 'all' ? $filter['asset_location_id'] : (empty($asset_location) ? "-" : $asset_location->name),
            'supplier'          => $filter['supplier_id'] === 'all' ? $filter['supplier_id'] : (empty($supplier) ? "-" : $supplier->name),
        ]);
    }


    public function afterSheet(AfterSheet $event)
    {
        $sheet = $event->sheet->getDelegate();
        $sheet->getStyle('B10:M' . ($this->lengthRecord + 10))->getAlignment()->applyFromArray([
            'horizontal'    => Alignment::HORIZONTAL_LEFT,
            'vertical'      => Alignment::VERTICAL_TOP
        ]);
        $sheet->getStyle('A10:A' . ($this->lengthRecord + 10))->getAlignment()->applyFromArray([
            'horizontal'    => Alignment::HORIZONTAL_CENTER,
            'vertical'      => Alignment::VERTICAL_CENTER
        ]);
        $sheet->getStyle('A1:M1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 22
            ],
            'alignment' => [
                'horizontal'    => Alignment::HORIZONTAL_CENTER,
                'vertical'      => Alignment::VERTICAL_CENTER
            ]
        ]);
        $sheet->getStyle('L10:L' . ($this->lengthRecord + 10))->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getStyle('A10:M' . ($this->lengthRecord + 10))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ]
            ]
        ]);
        $sheet->setShowGridlines(false);
        $public_path = public_path('temp/image/');
        $barcode_path = public_path('temp/barcode/');
        File::ensureDirectoryExists($public_path);
        File::ensureDirectoryExists($barcode_path);
        if ($this->filter['show_image'] || $this->filter['show_barcode']) {
            $generateBarcode = new BarcodeGeneratorPNG();
            foreach ($this->data as $key => $value) {
                if ($this->filter['show_image']) {
                    if ($value->image_path !== null && Storage::exists($value->image_path)) {
                        $absPath = Storage::path($value->image_path);
                        $file_name = basename($value->image_path);
                        $destPath = $public_path . $file_name;
                        if (!file_exists($destPath)) {
                            copy($absPath, $destPath);
                        }
                        $drawing = new Drawing();
                        $drawing->setPath($destPath);
                        $drawing->setCoordinates('M' . ($key + 11));
                        $drawing->setHeight(120);
                        $drawing->setOffsetY(8);
                        $drawing->setOffsetX(5);
                        $drawing->setWorksheet($sheet);
                        $sheet->getRowDimension($key + 11)->setRowHeight(105);
                    }
                }
                if ($this->filter['show_barcode']) {
                    $barcode_des_path =  $barcode_path . $value->barcode . '.png';
                    if (!file_exists($barcode_des_path)) {
                        $barcode = $generateBarcode->getBarcode($value->barcode, $generateBarcode::TYPE_CODE_128, 2, 45);
                        file_put_contents($barcode_des_path, $barcode);
                    }
                    $drawing = new Drawing();
                    $drawing->setPath($barcode_des_path);
                    $drawing->setHeight(45);
                    $drawing->setWidth(325);
                    $drawing->setOffsetY(25);
                    $drawing->setOffsetX(5);
                    $drawing->setCoordinates('H' . ($key + 11));
                    $drawing->setWorksheet($sheet);
                    if (!$this->filter['show_image']) {
                        $sheet->getRowDimension($key + 11)->setRowHeight(105);
                    }
                }
            }
        }
    }

    public function title(): string
    {
        return 'Asset Barcode Catalog';
    }
}
