<?php

namespace App\Http\Controllers;

use App\Http\Utils\responseMessage;
use App\Models\transaksi_tengki;
use App\Models\transaksi_tengki_detail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class transaksiTengkiController extends Controller
{
    //
    function get_transaksi_tengki(Request $req)
    {
        $data = transaksi_tengki::query()
            ->withSum([
                'transaksi_tengki_detail as pengeluaran'    => function ($query) {
                    $query->whereIn('type', ['pengeluaran', 'gaji']);
                }
            ], 'total')
            ->withSum([
                'transaksi_tengki_detail as pemasukan'  => function ($query) {
                    $query->whereIn('type', ['pemasukan', 'init']);
                }
            ], 'total')
            ->orderBy('periode', 'desc');

        return DataTables::of($data)
            ->addIndexColumn()
            ->filterColumn('periode', function ($query, $keyword) {
                $query->where('periode', 'like', "{$keyword}%");
            })
            ->filterColumn('status', function ($query, $keyword) {
                $query->where('status', 'like', "{$keyword}%");
            })
            ->addColumn('pengeluaran', function ($row) {
                return number_format($row->pengeluaran, 0, ',', '.');
            })
            ->addColumn('pemasukan', function ($row) {
                return number_format($row->pemasukan, 0, ',', '.');
            })
            ->addColumn('total_penghasilan', function ($row) {
                return number_format($row->pemasukan - $row->pengeluaran, 0, ',', '.');
            })
            ->addColumn('status', function ($row) {
                return $row->status === 1 ? 'Close' : 'Open';
            })
            ->addColumn('action', function ($row) {
                $btn = '<button type="button" id="' . $row->id . '" class="btn btn-info btn-sm btnDetail"><i class="zmdi zmdi-eye"></i></button> ';
                if ($row->status == 0) {
                    $btn .= '<button type="button" id="' . $row->id . '" class="btn btn-info btn-sm btnClose"><i class="zmdi zmdi-close"></i></button> ';
                    $btn .= '<button type="button" id="' . $row->id . '" class="btn btn-danger btn-sm btnDelete"><i class="fa fa-trash"></i></button> ';
                }
                return $btn;
            })
            ->make(true);
    }

    function add_periode(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'periode'   => 'required|date_format:Y-m'
        ]);

        if ($validate->fails()) {
            return responseMessage::responseMessage(0, $validate->errors()->first(), 200);
        }
        if (transaksi_tengki::where('periode', $req->periode)->exists()) {
            return responseMessage::responseMessage(0, "Periode Sudah Ada", 200);
        }

        transaksi_tengki::create([
            'periode'   => $req->periode
        ]);

        return responseMessage::responseMessage(1, "Success", 200);
    }


    function get_transaksi_tengki_detail(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'id'    => 'required'
        ]);

        if ($validate->fails()) {
            return responseMessage::responseMessage(0, $validate->errors()->first(), 200);
        }

        $data = transaksi_tengki_detail::query()
            ->where('transaksi_tengki_id', $req->id)
            ->orderBy('tanggal', 'asc');

        return DataTables::of($data)
            ->addIndexColumn()
            ->filterColumn('tanggal', function ($query, $keyword) {
                $query->where('tanggal', 'like', "{$keyword}%");
            })
            ->filterColumn('type', function ($query, $keyword) {
                $query->where('type', 'like', "{$keyword}%");
            })
            ->filterColumn('keterangan', function ($query, $keyword) {
                $query->where('keterangan', 'like', "{$keyword}%");
            })
            ->addColumn('tanggal', function ($row) {
                return Carbon::parse($row->tanggal)->format('d M Y');
            })
            ->addColumn('harga', function ($row) {
                return number_format($row->harga, 0, ',', '.');
            })
            ->addColumn('total', function ($row) {
                return number_format($row->total, 0, ',', '.');
            })
            ->addColumn('action', function ($row) {
                $btn = '<button type="button" id="' . $row->id . '" class="btn btn-danger btn-sm btnDelete"><i class="fa fa-trash"></i></button> ';
                return $btn;
            })
            ->make(true);
    }

    function get_transaksi_tengki_id(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'id'    => 'required'
        ]);

        if ($validate->fails()) {
            return responseMessage::responseMessage(0, $validate->errors()->first(), 200);
        }

        $transaksi_tengki = transaksi_tengki::query()
            ->withSum([
                'transaksi_tengki_detail as total_pengeluaran' => function ($query) {
                    $query->whereIn('type', ['pengeluaran', 'gaji']);
                }
            ], 'total')
            ->withSum([
                'transaksi_tengki_detail as total_pemasukan' => function ($query) {
                    $query->whereIn('type', ['pemasukan', 'init']);
                }
            ], 'total')
            ->where('id', $req->id)
            ->first();
        $lastTrx = transaksi_tengki_detail::where('transaksi_tengki_id', $req->id)->orderBy('tanggal', 'desc')->first();
        $lastDate = Carbon::now();
        if (!empty($lastTrx)) {
            $lastDate = Carbon::parse($lastTrx->tanggal)->addDay();
        }

        $pengeluaran = $transaksi_tengki->total_pengeluaran === null ? 0 : $transaksi_tengki->total_pengeluaran;
        $pemasukan = $transaksi_tengki->total_pemasukan === null ? 0 : $transaksi_tengki->total_pemasukan;

        return responseMessage::responseMessageWithData(1, "Success", 200, array(
            'pengeluaran'   => $pengeluaran,
            'pemasukan'     => $pemasukan,
            'total'         => $pemasukan - $pengeluaran,
            'lastDate'      => $lastDate->format('Y-m-d')
        ));
    }

    function add_detail_transaksi(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'transaksi_tengki_id'   => 'required|integer',
            'tanggal'               => 'required|date_format:Y-m-d',
            'type'                  => 'required|in:pengeluaran,pemasukan,gaji',
            'harga'                 => 'required|integer',
            'jumlah_ret'            => 'required|integer'
        ]);
    }
}
