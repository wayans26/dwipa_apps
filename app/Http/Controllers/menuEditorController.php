<?php

namespace App\Http\Controllers;

use App\Http\Utils\makeid;
use App\Http\Utils\responseMessage;
use App\Models\menu_group;
use App\Models\menu_sub_group;
use App\Models\user_has_menu_sub_group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class menuEditorController extends Controller
{
    //

    function getMenuEditor(Request $req)
    {
        $menuEditor = menu_sub_group::join('menu_groups', 'menu_groups.id', '=', 'menu_sub_groups.menu_group_id')
            ->select('menu_sub_groups.*', 'menu_groups.name as menu_group')
            ->orderBy('menu_groups.order_no', 'asc')
            ->orderBy('menu_sub_groups.order_no', 'asc')
            ->get();

        return DataTables::of($menuEditor)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '<button type="button" id="' . $row->id . '" class="btn btn-info btn-sm btnEdit"><i class="zmdi zmdi-edit"></i></button> ';
                $btn .= '<button type="button" id="' . $row->id . '" class="btn btn-danger btn-sm btnDelete"><i class="fa fa-trash"></i></button> ';

                return $btn;
            })
            ->make(true);
    }

    function getMenuGroup(Request $req)
    {
        return responseMessage::responseMessageWithData(1, "Success", 200, menu_group::get());
    }

    function addMenuEditor(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'name'          => 'required',
            'order_no'      => 'required|integer',
            'menu_group_id' => 'required',
            'page_name'     => 'required',
        ]);

        if ($validate->fails()) {
            return responseMessage::responseMessage(0, $validate->errors()->first(), 200);
        }

        $menuGroup = menu_group::find($req->menu_group_id);

        if (empty($menuGroup)) {
            return responseMessage::responseMessage(0, "Menu Group Not Found", 200);
        }

        $menuId = makeid::createId(16);
        $menuEditor = menu_sub_group::create([
            'id'            => $menuId,
            'name'          => $req->name,
            'order_no'      => $req->order_no,
            'menu_group_id' => $req->menu_group_id,
            'page_name'     => $req->page_name
        ]);

        return responseMessage::responseMessage(1, "Success", 200);
    }

    function getMenuEditorById(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'id' => 'required'
        ]);

        if ($validate->fails()) {
            return responseMessage::responseMessage(0, $validate->errors()->first(), 200);
        }

        $menuEditor = menu_sub_group::find($req->id);

        if (empty($menuEditor)) {
            return responseMessage::responseMessage(0, "Menu Editor Not Found", 200);
        }

        return responseMessage::responseMessageWithData(1, "Success", 200, $menuEditor);
    }

    function editMenuEditor(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'id'            => 'required',
            'name'          => 'required',
            'order_no'      => 'required|integer',
            'menu_group_id' => 'required',
            'page_name'     => 'required',
        ]);

        if ($validate->fails()) {
            return responseMessage::responseMessage(0, $validate->errors()->first(), 200);
        }

        $menuEditor = menu_sub_group::find($req->id);

        if (empty($menuEditor)) {
            return responseMessage::responseMessage(0, "Menu Editor Not Found", 200);
        }

        $menuGroup = menu_group::find($req->menu_group_id);

        if (empty($menuGroup)) {
            return responseMessage::responseMessage(0, "Menu Group Not Found", 200);
        }

        $menuEditor->update([
            'name'          => $req->name,
            'order_no'      => $req->order_no,
            'menu_group_id' => $req->menu_group_id,
            'page_name'     => $req->page_name
        ]);


        return responseMessage::responseMessage(1, "Success", 200);
    }

    function deleteMenuEditor(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'id' => 'required'
        ]);

        if ($validate->fails()) {
            return responseMessage::responseMessage(0, $validate->errors()->first(), 200);
        }

        $menuEditor = menu_sub_group::find($req->id);

        if (empty($menuEditor)) {
            return responseMessage::responseMessage(0, "Menu Editor Not Found", 200);
        }

        $hasPermission = user_has_menu_sub_group::where('menu_sub_group_id', $menuEditor->id)->delete();

        $menuEditor->delete();

        return responseMessage::responseMessage(1, "Success", 200);
    }
}
