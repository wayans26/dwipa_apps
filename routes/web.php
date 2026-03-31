<?php

use App\Exports\catalog_group;
use App\Http\Controllers\imageController;
use App\Http\Controllers\reportController;
use App\Http\Utils\makeid;
use App\Mail\reset_password_mail;
use App\Models\license_permit;
use App\Models\personal_token;
use App\Models\report_file;
use App\Models\user;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use ZanySoft\Zip\Facades\Zip;


Route::get('/', function (Request $req) {
    // dd(Hash::make("admin"));
    if ($req->has('token')) {
        $token = personal_token::where('token', $req->token)->first();
        if (empty($token)) {
            return redirect()->route('auth', ['any' => 'login']);
        }
        $user = user::find($token->iduser);

        if (empty($user)) {
            return redirect()->route('auth', ['any' => 'login']);
        }
        return redirect()->route('user', ['any' => 'redirect']);
    }
    return redirect()->route('auth', ['any' => 'login']);
});
Route::get('/auth/{any}', function () {
    return view('index_login');
})->where("any", '.*')->name('auth');


Route::get('/user/{any}', function () {
    return view('index');
})->where("any", '.*')->name('user');
