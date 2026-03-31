<?php

namespace App\Http\Utils;

class responseMessage
{

    public static function responseMessage($status, $message, $code)
    {
        return response([
            'status'    => $status,
            'message'   => $message
        ], $code);
    }

    public static function responseMessageWithData($status, $message, $code, $data)
    {
        return response([
            'status'    => $status,
            'message'   => $message,
            'data'      => $data
        ], $code);
    }

    public static function responseValidateError($error)
    {
        return response([
            'status'    => 0,
            'message'   => "Error Validate",
            'data'      => $error->errors()->first()
        ], 200);
    }


    public static function responseBerhasilTambah($code)
    {
        return response([
            'status'    => 1,
            'message'   => "Data Berhasil Ditambahkan"
        ], $code);
    }

    public static function responseGagalTambah($code)
    {
        return response([
            'status'    => 1,
            'message'   => "Data Gagal Ditambahkan"
        ], $code);
    }

    public static function responseBerhasilUpdate($code)
    {
        return response([
            'status'    => 1,
            'message'   => "Data Berhasil Diupdate"
        ], $code);
    }

    public static function responseGagalUpdate($code)
    {
        return response([
            'status'    => 0,
            'message'   => "Data Gagal Diupdate"
        ], $code);
    }

    public static function responseItemTidakTersedia($code)
    {
        return response([
            'status'    => 0,
            'message'   => "Item Tidak Tersedia"
        ], $code);
    }
}
