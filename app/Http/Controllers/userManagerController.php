<?php

namespace App\Http\Controllers;

use App\Http\Utils\makeid;
use App\Http\Utils\responseMessage;
use App\Models\department;
use App\Models\employee;
use App\Models\file;
use App\Models\menu_sub_group;
use App\Models\personal_token;
use App\Models\role;
use App\Models\user;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class userManagerController extends Controller
{
    //
    function getUserManager(Request $req)
    {
        $user = user::join('roles', 'roles.id', '=', 'users.role_id')
            ->select('users.username', 'users.full_name', 'users.email', 'roles.role_name', 'users.id',)
            ->get();

        return DataTables::of($user)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '<button type="button" id="' . $row->id . '" class="btn btn-info btn-sm btnEdit"><i class="zmdi zmdi-edit"></i></button> ';
                $btn .= '<button type="button" id="' . $row->id . '" class="btn btn-danger btn-sm btnDelete"><i class="fa fa-trash"></i></button> ';
                return $btn;
            })
            ->addColumn('username', function ($row) {
                return '<a href="javascript:void(0)" id="' . $row->id . '" class="btnView">' . $row->username . '</a>';
            })
            ->rawColumns(['action', 'username'])
            ->make(true);
    }

    function addUserManager(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'username'      => 'required',
            'full_name'     => 'required',
            'phone'         => 'required',
            'email'         => 'required',
            'role'          => 'required',
        ]);

        if ($validator->fails()) {
            return responseMessage::responseMessage(0, $validator->errors()->first(), 200);
        }

        if (user::where('username', $req->username)->exists()) {
            return responseMessage::responseMessage(0, "Username Already Exist", 200);
        }

        if (user::where('email', $req->email)->exists()) {
            return responseMessage::responseMessage(0, "Email Already Exist", 200);
        }

        $role = role::find($req->role);

        if (empty($role)) {
            return responseMessage::responseMessage(0, "Role not found", 200);
        }

        $fileId = null;


        $userID = makeid::createId(16);
        $user = user::create([
            'id'            => $userID,
            'username'      => $req->username,
            'full_name'     => $req->full_name,
            'phone'         => $req->phone,
            'email'         => $req->email,
            'password'      => Hash::make("123456"),
            'role_id'       => $role->id
        ]);


        return responseMessage::responseMessageWithData(1, "Success", 200, user::find($userID));
    }

    function getUserManagerById(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'id'    => 'required'
        ]);

        if ($validate->fails()) {
            return responseMessage::responseMessage(0, $validate->errors()->first(), 200);
        }

        $user = user::find($req->id);

        if (empty($user)) {
            return responseMessage::responseMessage(0, "User not found", 200);
        }

        $permission = menu_sub_group::leftJoin('user_has_menu_sub_groups as permission', function ($join) use ($user) {
            $join->on('menu_sub_groups.id', '=', 'permission.menu_sub_group_id')->where('permission.role_id', $user->role_id);
        })
            ->select('menu_sub_groups.id', 'menu_sub_groups.name', 'permission.allow_view', 'permission.allow_create', 'permission.allow_update', 'permission.allow_delete', 'permission.allow_print')
            ->orderBy('menu_sub_groups.order_no', 'asc')->get();

        return responseMessage::responseMessageWithData(1, "Success", 200, array(
            'user' => $user,
            'role' => role::find($user->role_id),
            'permission' => $permission
        ));
    }

    function editUserManager(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'username'      => 'required',
            'full_name'     => 'required',
            'phone'         => 'required',
            'email'         => 'required',
            'role'          => 'required',
            'id'            => 'required'
        ]);

        if ($validator->fails()) {
            return responseMessage::responseMessage(0, $validator->errors()->first(), 200);
        }

        $user = user::find($req->id);

        if (empty($user)) {
            return responseMessage::responseMessage(0, "User not found", 200);
        }

        if ($req->username !== $user->username) {
            if (user::where('username', $req->username)->exists()) {
                return responseMessage::responseMessage(0, "Username Already Exist", 200);
            }
        }


        $role = role::find($req->role);

        if (empty($role)) {
            return responseMessage::responseMessage(0, "Role not found", 200);
        }

        $fileId = null;

        $user->update([
            'username'      => $req->username,
            'full_name'     => $req->full_name,
            'phone'         => $req->phone,
            'email'         => $req->email,
            'role_id'       => $role->id,
        ]);


        return responseMessage::responseMessageWithData(1, "Success", 200, $user);
    }

    function deleteUserManager(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'id'    => 'required'
        ]);

        if ($validate->fails()) {
            return responseMessage::responseMessage(0, $validate->errors()->first(), 200);
        }

        $user = user::find($req->id);

        if (empty($user)) {
            return responseMessage::responseMessage(0, "User not found", 200);
        }

        $user->delete();
        return responseMessage::responseMessage(1, "Success", 200);
    }

    function getDataUserManager(Request $req)
    {
        $role = role::get();

        return responseMessage::responseMessageWithData(1, "Success", 200, array(
            'role'          => $role
        ));
    }

    function changePasswordUserManager(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'password'          => 'required',
            'confirm_password'  => 'required|same:password',
            'id'                => 'required'
        ]);

        if ($validate->fails()) {
            return responseMessage::responseMessage(0, $validate->errors()->first(), 200);
        }

        $user = user::find($req->id);

        if (empty($user)) {
            return responseMessage::responseMessage(0, "User not found", 200);
        }

        $user->update([
            'password'  => Hash::make($req->password)
        ]);

        return responseMessage::responseMessage(1, "Success", 200);
    }
}
