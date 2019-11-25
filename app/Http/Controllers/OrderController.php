<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\OrderDetail;
use App\Kredit;

class OrderController extends Controller
{
    public function index()
    {
        $data = Order::listing();
        $data = $data->searchAllFields();
        // dd($data->toSql());
        
        return bd_json($data);
    }

    public function indexCust($id_cust)
    {
        $data = Order::custListing($id_cust);
        $data = $data->searchAllFields();
        // dd($data->toSql());
        
        return bd_json($data);
    }

    public function store(Request $request)
    {
        $request->request->add(["id_user" => auth()->user()->id]);
        $data = (new Order)->record($request); 
        $data = $data->recordDetail($request->detail);     

        $kreditParams = [
            "id_order" => $data->id,
            "tunai" => min($request->total,$request->tunai)
        ];
        if ($kreditParams['tunai'] > 0)
            $kredit = (new Kredit)->record($kreditParams);

        return bd_json($data);
    }

    public function show($id)
    {
        $data = Order::find($id);
        return bd_json($data);
    }

    public function update(Request $request, $id)
    {
        $request->request->add(["id_user" => auth()->user()->id]);
        $data = Order::find($id)->record($request);
        $data = $data->deleteDetail()->recordDetail($request->detail);

        Kredit::where('id_order',$id)->delete();
        $kreditParams = [
            "id_order" => $data->id,
            "tunai" => min($request->total,$request->tunai)
        ];
        if ($kreditParams['tunai'] > 0)
            $kredit = (new Kredit)->record($kreditParams);

        return bd_json($data);
    }

    public function destroy($id)
    {
        $data = Order::find($id);
        if ($data) {
            $data->deleteDetail();
            $data->delete();
            Kredit::where('id_order',$id)->delete();
        }
        return bd_json($data);
    }

    public function indexWithDetail() 
    {
        $parentData = Order::select('*');
        $detailData = OrderDetail::select('*');       
        $data = $parentData->joinModel($detailData, 'order_detail' , 'order.id' , 'order_detail.id_order');
        $parentTableData = (new Order)->getTableProperties(); 
        $detailTableData = (new OrderDetail)->getTableProperties(); 
        $data = $data->searchAllFields($parentTableData,$detailTableData); 
        return bd_json($data);
    }

    public function showWithDetail($id) 
    {
        $parentData = Order::select('*')->where('order.id','=',$id);
        $detailData = OrderDetail::select('*');
        $data = $parentData->joinModel($detailData, 'order_detail','order.id','order_detail.id_order');  
        return bd_json($data);
    }

    public function numOrder(Request $request) {
        $date = $request->tanggal;
        $metode = $request->metode;
        $data = \DB::table('order')->whereDate('tanggal',$date)->count();
        return bd_json(["count" => $data]);
    }

    public function invoice(Request $request) {
        $detail = $request->detail;
        $od = OrderDetail::select('id_order',\DB::raw('SUM((100-diskon)/100*qty*harga) as total'))->groupBy('id_order');
        Kredit::whereIn('id_order',$detail)->delete();
        $data = Order::whereIn('id',$detail)
            ->joinModel($od, 'od' , 'od.id_order' , 'order.id');
        $update = $data->update([
            'tunai' => \DB::raw("`total`")
        ]);
            
        $kredit = $data->get()->map(function($items) {
                $kreditParams = [
                    "id_order" => $items->id,
                    "tunai" => $items->total
                ];
                $kredit = (new Kredit)->record($kreditParams);
                return $items;
            });
        return bd_json($data);
    }

}
