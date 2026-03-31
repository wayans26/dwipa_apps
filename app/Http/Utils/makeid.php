<?php

namespace App\Http\Utils;

use Ramsey\Uuid\Uuid;
use Hash;

class makeid
{
    public static function createUuid()
    {
        return Uuid::uuid4()->getHex();
    }
    public static function createToken()
    {
        return Hash::make(Uuid::uuid4()->getHex());
    }

    public static function createId($length)
    {
        return str()->random($length);
    }
    public static function createNumber($length)
    {
        $number = "0123456789";
        $random = "";
        for ($i = 1; $i <= $length; $i++) {
            $random .= substr($number, rand(0, 10), 1);
        }
        return $random;
    }

    public static function esc($text)
    {
        return str_replace('"', "'", (string)$text);
    }
    public static function calculateCentreX($text, $size)
    {
        // $pitchMap/Ukuran = [1 => 20.0, 2 => 17.0, 3 => 14.5, 4 => 13.0, 5 => 5.6];
        $pitchMap = [
            '1' => 20.0,
            '2' => 17.0,
            '3' => 14.5,
            '4' => 13.0,
            '5' => 5.6
        ];
        $pithch = $pitchMap[$size] ?? 14.5;
        $dpi = 203;
        $hmul = 2;
        $labelWidth = 832;
        $charWithDots = ($dpi / $pithch) * $hmul;
        $textWithDots = strlen($text) * $charWithDots;
        return $labelWidth - max(0, (int) round(($labelWidth - $textWithDots) / 2));
    }
}
