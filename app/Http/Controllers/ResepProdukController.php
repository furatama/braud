<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ResepProduk;
use App\ResepDetail;
use App\Order;
use App\OrderDetail;

class ResepProdukController extends Controller
{
    public function index()
    {
        $data = ResepProduk::select('*');
        $data = $data->searchAllFields();
        return bd_json($data);
    }

    public function store(Request $request)
    {
        $data = (new ResepProduk)->record($request);      
        return bd_json($data);
    }

    public function show($id)
    {
        $data = ResepProduk::find($id);
        return bd_json($data);
    }

    public function update(Request $request, $id)
    {
        $data = ResepProduk::find($id)->record($request);
        return bd_json($data);
    }

    public function destroy($id)
    {
        $data = ResepProduk::find($id);
        if ($data) {
            $data->delete();
        }
        return bd_json($data);
    }

    public function options() {

        $data = ResepProduk::select('*')
            ->get()
            ->map(function ($items) {
                $arr = $items->toArray();
                $arr['detail'] = ResepDetail::select('*')
                ->where('id_resep',$arr['id'])
                ->orderBy('ratio')
                ->get()
                ->toArray();
                return $arr;
            });
        return bd_json($data);

    }

    public function proses(Request $request) {
        $tanggal = $request->tanggal;
        $order = Order::select('tanggal')->joinModel(
                OrderDetail::select('id_order','id_produk','qty')
                ,'od','od.id_order','order.id'
            )
            ->select('od.id_produk',\DB::raw('SUM(qty) as loaf'))
            ->groupBy('id_produk')
            ->whereDate('tanggal',$tanggal);
        $resep = ResepDetail::select('resep_detail.*','loaf')->leftJoinModel(
            $order, 'od','resep_detail.id_produk','od.id_produk'
        );
        // dump($resep->toSql());
        // dd($resep->get()->toArray());
        $data = ResepProduk::select('*')
            ->get()
            ->map(function ($items) use($resep) {
                $arr = $items->toArray();
                $arr['detail'] = (clone $resep)
                ->where('id_resep',$arr['id'])
                ->orderBy('ratio')
                ->get()
                ->toArray();
                return $arr;
            });
        return bd_json($data);

    }
}
