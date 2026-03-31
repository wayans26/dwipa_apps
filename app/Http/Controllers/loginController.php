<?php

namespace App\Http\Controllers;

use App\Http\Utils\makeid;
use App\Http\Utils\responseMessage;
use App\Models\department;
use App\Models\menu_group;
use App\Models\menu_sub_group;
use App\Models\personal_token;
use App\Models\user;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use stdClass;

class loginController extends Controller
{
    //
    function login(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'username'  => 'required',
            'password'  => 'required'
        ]);

        if ($validator->fails()) {
            return responseMessage::responseValidateError($validator);
        }

        $user = user::where('username', $req->username)->first();

        if (empty($user)) {
            return responseMessage::responseMessage(0, "Username Atau Password Incorrect", 200);
        }

        if ($user->status == 0) {
            return responseMessage::responseMessage(0, "Your Account Has Been Disabled, Please contact operation teams for further information", 200);
        }

        if (!Hash::check($req->password, $user->password)) {
            return responseMessage::responseMessage(0, "Username Atau Password Incorrect", 200);
        }

        $token = Hash::make(makeid::createUuid());

        if (personal_token::where('iduser', $user->id)->count() >= 5) {
            personal_token::where('iduser', $user->id)->orderBy('id', 'asc')->first()->delete();
        }

        personal_token::create([
            'iduser'    => $user->id,
            'token'     => $token,
            'device'    => $req->userAgent()
        ]);

        return responseMessage::responseMessageWithData(1, "Success", 200, array(
            'token' => $token
        ));
    }
    function login_mobile(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'username'  => 'required',
            'password'  => 'required'
        ]);

        if ($validator->fails()) {
            return responseMessage::responseValidateError($validator);
        }

        $user = user::leftJoin('employees', 'employees.id', '=', 'users.employee_id')
            ->where('username', $req->username)
            ->select('users.id', 'users.username', 'users.email', 'users.image', 'users.employee_id', 'employees.department_id', 'users.status', 'users.allow_mobile', 'users.password')
            ->first();

        if (empty($user)) {
            return responseMessage::responseMessageWithData(0, "Username Atau Password Incorrect", 200, new stdClass());
        }

        if ($user->status == 0) {
            return responseMessage::responseMessageWithData(0, "Your Account Has Been Disabled, Please contact operation teams for further information", 200, new stdClass());
        }

        if ($user->allow_mobile == 0) {
            return responseMessage::responseMessageWithData(0, "Your Account Cant Login Using Mobile", 200, new stdClass());
        }

        if (!Hash::check($req->password, $user->password)) {
            return responseMessage::responseMessageWithData(0, "Username Atau Password Incorrect", 200, new stdClass());
        }

        $token = Hash::make(makeid::createUuid());

        if (personal_token::where('iduser', $user->id)->count() >= 5) {
            personal_token::where('iduser', $user->id)->orderBy('id', 'asc')->first()->delete();
        }

        personal_token::create([
            'iduser'    => $user->id,
            'token'     => $token,
            'device'    => $req->userAgent()
        ]);

        $department = department::find($user->department_id);

        return responseMessage::responseMessageWithData(1, "Success", 200, array(
            'token'         => $token,
            'username'      => $user->username,
            'email'         => $user->email,
            'department'    => empty($department) ? "-" : $department->name,
            'employee_id'   => $user->employee_id === null ? "-" : $user->employee_id,
            'image'         => empty($user->image) ? $req->getScheme() . "://" . $req->host() . "/avatar.png" : $req->getScheme() . "://" . $req->host() . "/image/user/get/" . $user->image
        ));
    }

    function login_check(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'token' => 'required'
        ]);

        if ($validate->fails()) {
            return responseMessage::responseValidateError($validate);
        }

        $token = personal_token::where('token', $req->token)->first();

        if (empty($token)) {
            return responseMessage::responseMessage(401, "Unauthorized", 200);
        }

        $user = user::find($token->iduser);

        if (empty($user)) {
            return responseMessage::responseMessage(401, "Unauthorized", 200);
        }

        if ($user->status == 0) {
            personal_token::where('iduser', $user->id)->delete();
            return responseMessage::responseMessage(401, "Your Account Has Been Disabled, Please contact operation teams for further information", 200);
        }
        return responseMessage::responseMessage(1, "Authorized", 200);
    }
    function login_mobile_check(Request $req)
    {
        $token = request()->header('token');

        if (empty($token)) {
            return responseMessage::responseMessage(401, "Unauthorized", 401);
        }

        $personalToken = personal_token::where('token', $token)->first();

        if (empty($personalToken)) {
            return responseMessage::responseMessage(401, "Unauthorized", 401);
        }

        $user = user::find($personalToken->iduser);

        if (empty($user)) {
            return responseMessage::responseMessage(401, "Unauthorized", 401);
        }

        if ($user->status == 0 || $user->allow_mobile == 0) {
            $personalToken->delete();
            return responseMessage::responseMessage(401, "Your Account Has Been Disabled Or Cant Login Using Mobile, Please contact operation teams for further information", 401);
        }
        return responseMessage::responseMessage(1, "Authorized", 200);
    }

    function tokenCheck(Request $req)
    {
        $reqToken = request()->header('token');
        $page_name = $req->has('page') ? $req->page : "none";

        if (empty($reqToken)) {
            return responseMessage::responseMessage(401, "Unauthorized", 200);
        }

        $token = Cache::remember($reqToken, Carbon::now()->addMinutes(120), function () use ($reqToken) {
            return personal_token::where('token', $reqToken)->first();
        });

        $menu = Cache::remember($page_name, Carbon::now()->addMinutes(120), function () use ($page_name) {
            return menu_group::join('menu_sub_groups', 'menu_sub_groups.menu_group_id', '=', 'menu_groups.id')
                ->select('menu_groups.name as menu_group', 'menu_sub_groups.name as menu_sub')
                ->where('menu_sub_groups.page_name', $page_name)
                ->first();
        });

        if (empty($token)) {
            return responseMessage::responseMessage(401, "Unauthorized", 200);
        }
        return responseMessage::responseMessageWithData(1, "Authorized", 200, $menu);
    }

    function getNavigation(Request $req)
    {
        $token = request()->header('token');

        $menu = menu_group::join('menu_sub_groups', 'menu_sub_groups.menu_group_id', '=', 'menu_groups.id')->distinct()->select('menu_groups.*')->orderBy('menu_groups.order_no', 'asc')->get();

        $navigation = array();

        foreach ($menu as $key => $value) {
            $submenu = menu_sub_group::join('user_has_menu_sub_groups as permission', 'permission.menu_sub_group_id', '=', 'menu_sub_groups.id')
                ->join('users', 'users.role_id', '=', 'permission.role_id')
                ->join('personal_tokens', 'personal_tokens.iduser', '=', 'users.id')
                ->where([
                    'menu_group_id'         => $value->id,
                    'personal_tokens.token' => $token,
                    'permission.allow_view' => 1
                ])->select('name', 'page_name')->orderBy('order_no', 'asc')->get()->toArray();
            if (sizeof($submenu) > 0) {
                array_push($navigation, array(
                    'parent'    => $value->name,
                    'child'     => $submenu,
                    'icon'      => $value->icon
                ));
            }
        }
        return responseMessage::responseMessageWithData(1, "Success", 200, $navigation);
    }
}
