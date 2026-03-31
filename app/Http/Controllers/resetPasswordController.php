<?php

namespace App\Http\Controllers;

use App\Http\Utils\makeid;
use App\Http\Utils\responseMessage;
use App\Mail\reset_password_mail;
use App\Models\reset_password;
use App\Models\user;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Hash;
use Illuminate\Support\Facades\Mail;
use DB;

class resetPasswordController extends Controller
{
    //
    function forgotPassword(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'email' => 'required|email'
        ]);

        if ($validate->fails()) {
            return responseMessage::responseMessage(0, $validate->errors()->first(), 200);
        }

        $user = user::where('email', $req->email)->first();

        if (empty($user)) {
            return responseMessage::responseMessage(1, "Thank you. If the email address you entered matched an existing account, please check your email for instructions on what to do next.", 200);
        }

        $token = base64_encode(Hash::make(makeid::createUuid()) . makeid::createId(126));

        $url = url('/auth/forgot/password/change' . "?token=" . urlencode($token));

        Mail::to($user->email)->queue(new reset_password_mail($url, $user->username));

        reset_password::create([
            'token_reset'   => $token,
            'iduser'        => $user->id,
            'expired_date'  => Carbon::now()->addHours(8)->format('Y-m-d H:i:s'),
        ]);

        return responseMessage::responseMessage(1, "Thank you. If the email address you entered matched an existing account, please check your email for instructions on what to do next.", 200);
    }

    function checkToken(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'token' => 'required'
        ]);

        if ($validate->fails()) {
            return responseMessage::responseMessage(0, "Token Expired", 200);
        }

        $check = reset_password::where('token_reset', $req->token)->first();
        if (empty($check)) {
            return responseMessage::responseMessage(0, "Token Expired", 200);
        }

        if (Carbon::now()->greaterThan($check->expired_date)) {
            return responseMessage::responseMessage(0, "Token Expired", 200);
        }

        if ($check->status == 1) {
            return responseMessage::responseMessage(0, "Token Expired", 200);
        }
        return responseMessage::responseMessage(1, "Success", 200);
    }

    function changePassword(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'token'             => 'required',
            'password'          => 'required',
            'confirm_password'  => 'required|same:password'
        ]);

        if ($validate->fails()) {
            return responseMessage::responseMessage(0, $validate->errors()->first(), 200);
        }

        $token = reset_password::where('token_reset', $req->token)->first();

        if (empty($token)) {
            return responseMessage::responseMessage(0, "Token Expired", 200);
        }

        if (Carbon::now()->greaterThan($token->expired_date)) {
            return responseMessage::responseMessage(0, "Token Expired", 200);
        }

        if ($token->status == 1) {
            return responseMessage::responseMessage(0, "Token Expired", 200);
        }

        $user = user::find($token->iduser);

        if (empty($user)) {
            return responseMessage::responseMessage(0, "User not found", 200);
        }

        try {
            DB::transaction(function () use ($user, $req, $token) {
                reset_password::where('iduser', $user->id)->update([
                    'status'    => 1
                ]);
                $user->update([
                    'password'  => Hash::make($req->password)
                ]);
            });
            return responseMessage::responseMessage(1, "Success", 200);
        } catch (\Throwable $th) {
            return responseMessage::responseMessage(0, "Failed", 200);
        }
    }
}
