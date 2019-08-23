<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Kredit;
use App\Order;

class KreditController extends Controller
{
    public function index()
    {
        $data = Kredit::select('*');
        $tableData = (new Kredit)->getTableProperties();
        $data = $data->searchAllFields($tableData);
        return bd_json($data);
    }

    public function store(Request $request)
    {
        if ($request->tunai > 0) {
            $data = (new Kredit)->record($request);      
        }

        $kSum = Kredit::orderSum($request->id_order);
        $order = Order::find($request->id_order);
        $order->tunai = $kSum;
        $order->save();

        if ($request->tunai > 0) {
            return bd_json($data);
        }
        
        return;
    }

    public function show($id)
    {
        $data = Kredit::find($id);
        return bd_json($data);
    }

    public function update(Request $request, $id)
    {
        $data = Kredit::find($id)->record($request);
        return bd_json($data);
    }

    public function destroy($id)
    {
        $data = Kredit::find($id);
        if ($data) {
            $data->delete();
        }
        return bd_json($data);
    }

    public function showByIDOrder($id_order) {
        $sum = Kredit::orderSum($id_order);
        $detail = Kredit::where('id_order',$id_order)->get();
        return bd_json([
            "total" => $sum,
            "detail" => $detail
        ]);
    }

    public function destroyByIDOrder($id_order) {
        $data = Kredit::where('id_order',$id_order);
        if ($data)
            $data->delete();
        return bd_json($data);
    }
}
