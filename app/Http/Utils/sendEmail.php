<?php

namespace App\Http\Utils;

use App\Mail\email_registration;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class sendEmail
{
    public static function sendEmailRegistration($req, $barcode, $exhibitions, $subexhibitions)
    {
        $path = "Barcode/" . $req->name . "-" . $barcode . ".png";
        $imageContent = file_get_contents("https://api.qrserver.com/v1/create-qr-code/?size=200x200&qzone=4&data=" . $barcode);
        $save = File::put(public_path($path), $imageContent);
        $url = "https://" . $req->host() . '/' . $path;

        $type = $exhibitions->type === "CUSTOM" ? ($exhibitions->custom_tag === 'busworld' ? 'busworld' : 'custom') : $exhibitions->type;

        $dataToSend = array(
            'url'               => $url,
            'subject'           => $exhibitions->keterangan,
            'from'              => 'no.reply@reg-gemindonesia.net',
            'from_name'         => 'GEM Indonesia',
            'idexhibitions'     => $exhibitions->idexhibitions,
            'name'              => $req->name,
            'first_name'        => $req->name,
            'last_name'         => "",
            'keterangan'        => $exhibitions->keterangan,
            'company'           => $req->company,
            'job_title'         => $req->job_title,
            'city'              => $req->city,
            'country'           => $req->selectedcountry,
            'email'             => $req->email,
            'opening_hours'     => $exhibitions->opening_hours,
            'type'              => $type,
            'idsubexhibitions'  => $subexhibitions->idsubexhibitions
        );

        Mail::to($req->email)->queue(new email_registration($dataToSend));
        Mail::to("pt.globalexpomanagement@gmail.com")->queue(new email_registration($dataToSend));
    }
}
