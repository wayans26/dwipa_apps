<?php

namespace App\Http\Controllers;

use App\Http\Utils\makeid;
use App\Http\Utils\responseMessage;
use App\Models\menu_sub_group;
use App\Models\role;
use App\Models\user;
use App\Models\user_has_menu_sub_group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use DB;

class roleController extends Controller
{
    //
    function getRole(Request $req)
    {
        $role = role::get();

        return DataTables::of($role)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '<button type="button" id="' . $row->id . '" class="btn btn-info btn-sm btnEdit"><i class="zmdi zmdi-edit"></i></button> ';
                $btn .= '<button type="button" id="' . $row->id . '" class="btn btn-danger btn-sm btnDelete"><i class="fa fa-trash"></i></button> ';
                return $btn;
            })
            ->addColumn('name', function ($row) {
                return '<a href="javascript:void(0)" id="' . $row->id . '" class="btnView">' . $row->role_name . '</a>';
            })
            ->rawColumns(['action', 'name'])
            ->make(true);
    }

    function getRoleMenu(Request $req)
    {
        $subMenu = menu_sub_group::join('menu_groups', 'menu_groups.id', '=', 'menu_sub_groups.menu_group_id')
            ->select('menu_sub_groups.id', 'menu_sub_groups.name', 'menu_groups.name as menu_group')
            ->orderBy('menu_groups.order_no', 'asc')
            ->orderBy('menu_sub_groups.order_no', 'asc')->get();
        return responseMessage::responseMessageWithData(1, "Success", 200, $subMenu);
    }

    function addRole(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'role_name'     => 'required',
            'permission'    => 'required|array',
        ]);

        if ($validate->fails()) {
            return responseMessage::responseMessage(0, $validate->errors()->first(), 200);
        }
        // dd($req->all());

        try {
            DB::transaction(function () use ($req) {
                $roleId = makeid::createId(16);
                role::create([
                    'id'            => $roleId,
                    'role_name'     => $req->role_name,
                ]);

                foreach ($req->permission as $key => $value) {
                    user_has_menu_sub_group::create([
                        'role_id'           => $roleId,
                        'menu_sub_group_id' => $value['id'],
                        'allow_view'        => $value['allow_view'] == 'true' ? 1 : 0,
                        'allow_create'      => $value['allow_create'] == 'true' ? 1 : 0,
                        'allow_update'      => $value['allow_update'] == 'true' ? 1 : 0,
                        'allow_delete'      => $value['allow_delete'] == 'true' ? 1 : 0,
                        'allow_print'       => $value['allow_print'] == 'true' ? 1 : 0,
                    ]);
                }
            });
            return responseMessage::responseMessage(1, "Success", 200);
        } catch (\Throwable $th) {
            return responseMessage::responseMessage(0, "Failed", 200);
        }
    }
    function editRole(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'role_name'     => 'required',
            'permission'    => 'required|array',
            'id'            => 'required',
        ]);

        if ($validate->fails()) {
            return responseMessage::responseMessage(0, $validate->errors()->first(), 200);
        }

        $role = role::find($req->id);

        if (empty($role)) {
            return responseMessage::responseMessage(0, "Role not found", 200);
        }

        try {
            DB::transaction(function () use ($req, $role) {

                foreach ($req->permission as $key => $value) {
                    user_has_menu_sub_group::updateOrCreate([
                        'menu_sub_group_id' => $value['id'],
                        'role_id'           => $role->id,
                    ], [
                        'allow_view'        => $value['allow_view'] == 'true' ? 1 : 0,
                        'allow_create'      => $value['allow_create'] == 'true' ? 1 : 0,
                        'allow_update'      => $value['allow_update'] == 'true' ? 1 : 0,
                        'allow_delete'      => $value['allow_delete'] == 'true' ? 1 : 0,
                        'allow_print'       => $value['allow_print'] == 'true' ? 1 : 0,
                    ]);
                }
                $role->update([
                    'role_name' => $req->role_name
                ]);
            });
            return responseMessage::responseMessage(1, "Success", 200);
        } catch (\Throwable $th) {
            return responseMessage::responseMessage(0, "Failed", 200);
        }
    }

    function getRoleMenuById(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'id'    => 'required'
        ]);

        if ($validate->fails()) {
            return responseMessage::responseMessage(0, $validate->errors()->first(), 200);
        }

        $role = role::find($req->id);

        if (empty($role)) {
            return responseMessage::responseMessage(0, "Role not found", 200);
        }

        $permission = menu_sub_group::leftJoin('user_has_menu_sub_groups as permission', function ($join) use ($role) {
            $join->on('menu_sub_groups.id', '=', 'permission.menu_sub_group_id')->where('permission.role_id', $role->id);
        })
            ->join('menu_groups', 'menu_groups.id', '=', 'menu_sub_groups.menu_group_id')
            ->select('menu_sub_groups.id', 'menu_sub_groups.name', 'menu_groups.name as menu_group', 'permission.allow_view', 'permission.allow_create', 'permission.allow_update', 'permission.allow_delete', 'permission.allow_print')
            ->orderBy('menu_groups.order_no', 'asc')
            ->orderBy('menu_sub_groups.order_no', 'asc')
            ->get();

        return responseMessage::responseMessageWithData(1, "Success", 200, array(
            'role'      => $role,
            'permission'    => $permission
        ));
    }

    function deleteRole(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'id'    => 'required'
        ]);

        if ($validate->fails()) {
            return responseMessage::responseMessage(0, $validate->errors()->first(), 200);
        }

        $role = role::find($req->id);

        if (empty($role)) {
            return responseMessage::responseMessage(0, "Role not found", 200);
        }

        $user = user::where('role_id', $role->id)->first();

        if (!empty($user)) {
            return responseMessage::responseMessage(0, "Cant Delete Role, User Still Using This Role", 200);
        }

        try {
            DB::transaction(function () use ($role, $req) {
                user_has_menu_sub_group::where('role_id', $role->id)->delete();
                $role->delete();
            });
            return responseMessage::responseMessage(1, "Success", 200);
        } catch (\Throwable $th) {
            //throw $th;
            return responseMessage::responseMessage(0, "Failed", 200);
        }
    }
}
