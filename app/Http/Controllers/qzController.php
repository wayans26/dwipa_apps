<?php

namespace App\Http\Controllers;

use App\Http\Utils\responseMessage;
use App\Models\asset_barcode;
use App\Models\company_profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class qzController extends Controller
{
    //
    function cert(Request $req)
    {
        $pem = Storage::get('qz/certificate.pem');

        return responseMessage::responseMessageWithData(1, "Success", 200, $pem);
    }

    function sign(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'toSign'    => 'required|string'
        ]);

        if ($validate->fails()) {
            return responseMessage::responseMessage(0, $validate->errors()->first(), 200);
        }

        $toSign = $req->toSign;

        $privateKey = Storage::get('qz/private-key.pem');
        $pkey = openssl_pkey_get_private($privateKey);

        if (!$pkey) {
            return responseMessage::responseMessage(0, "Failed to get private key", 200);
        }

        $signature = '';
        $ok = openssl_sign($toSign, $signature, $pkey, OPENSSL_ALGO_SHA256);
        openssl_free_key($pkey);
        if (!$ok) {
            return responseMessage::responseMessage(0, "Failed to sign", 200);
        }

        return responseMessage::responseMessageWithData(1, "Success", 200, base64_encode($signature));
    }

    function generateDataPrint(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'barcode'   => 'required|array',
            'code_type' => 'required|in:barcode,qrcode'
        ]);

        if ($validate->fails()) {
            return responseMessage::responseMessage(0, $validate->errors()->first(), 200);
        }

        $print_type = env('PRINTER_CONFIG', 'zpl');

        $company = company_profile::first();
        $company_name = empty($company) ? "Default" : ($company->company_name === null ? "Default" : $company->company_name);
        $data_barcode = asset_barcode::join('assets', 'asset_barcodes.asset_id', '=', 'assets.id')
            ->join('bussines_units', 'assets.business_unit_id', '=', 'bussines_units.id')
            ->select('assets.name', 'asset_barcodes.code_barcode', 'bussines_units.name as business_unit_name')
            ->whereIn('asset_barcodes.code_barcode', $req->barcode)
            ->get();

        if ($print_type === 'zpl') {

            $qrcode = '^XA ~TA000 ~JSN ^LT0 ^MNW ^MTT ^PON ^PMN ^LH0,0 ^JMA ^PR6,6 ~SD15 ^JUS ^LRN ^CI27 ^PA0,1,1,0 ^XZ ^XA ^MMT ^PW559 ^LL248 ^LS0 ^FT64,216^BQN,2,8 ^FH\^FDLA,';
            $business_unit = '^FS ^FT244,49^A0N,25,25^FH\^CI28^FD';
            $plain_qrcode = '^FS^CI27 ^FT244,85^A0N,25,25^FH\^CI28^FD';
            $product_name_qrcode = '^FS^CI27 ^FT244,123^A0N,23,23^FH\^CI28^FD';
            $end_qrcode = '^FS^CI27 ^FT244,155^A0N,23,23^FH\^CI28^FD^FS^CI27 ^FT244,187^A0N,23,23^FH\^CI28^FD^FS^CI27 ^PQ1,0,1,Y ^XZ';

            $hotel_name = '^XA ~TA000 ~JSN ^LT0 ^MNW ^MTT ^PON ^PMN ^LH0,0 ^JMA ^PR6,6 ~SD15 ^JUS ^LRN ^CI27 ^PA0,1,1,0 ^XZ ^XA ^MMT ^PW496 ^LL216 ^LS0 ^FT28,48^A0N,28,28^FH\^CI28^FD';
            $barcode = '^FS ^BY3,3,60^FT28,121^BCN,,N,N ^FH\^FD>;';
            $plain_barcode = '^FS ^FT28,154^A0N,28,28^FH\^CI28^FD';
            $product_name_barcode = '^FS ^FT28,186^A0N,28,28^FH\^CI28^FD';
            $end_barcode = '^FS^CI27 ^PQ1,0,1,Y ^XZ';


            $data_print = "";
            foreach ($data_barcode as $key => $value) {
                if ($req->code_type === 'barcode') {
                    $data_print .= $hotel_name . $company_name . $barcode . $value->code_barcode . $plain_barcode . $value->code_barcode . $product_name_barcode . $value->name . $end_barcode . " ";
                } else {
                    $data_print .= $qrcode . $value->code_barcode . $business_unit . $value->business_unit_name . $plain_qrcode . $value->code_barcode . $product_name_qrcode . $value->name . $end_qrcode . " ";
                }
            }
            return responseMessage::responseMessageWithData(1, "Print In Progress", 200, $data_print);
        }
        return responseMessage::responseMessage(0, "Printer Not Found, Please Check Config", 200);
    }
}
