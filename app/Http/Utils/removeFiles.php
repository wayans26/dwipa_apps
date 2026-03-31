<?php

namespace App\Http\Utils;

use App\Models\report_file;
use Illuminate\Support\Facades\File;

class removeFiles
{

    public static function removeJobTempFiles()
    {
        // if (!report_file::where('status', 0)->exists()) {
        //     $image_path = public_path('temp/image');
        //     if (File::isDirectory($image_path)) {
        //         $file = File::files($image_path);
        //         foreach ($file as $key => $value) {
        //             File::delete($value);
        //         }
        //     }
        //     $barcode_path = public_path('temp/barcode');
        //     if (File::isDirectory($barcode_path)) {
        //         $file_barcode = File::files($barcode_path);
        //         foreach ($file_barcode as $key => $value) {
        //             File::delete($value);
        //         }
        //     }
        // }
    }
}
