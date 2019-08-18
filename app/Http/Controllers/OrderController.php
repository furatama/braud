<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;

class OrderController extends Controller
{
    public function index()
    {
        $data = Order::select('*');
        $tableData = (new Order)->getTableProperties();
        $data = $data->searchAllFields($tableData);
        return bd_json($data);
    }

    public function store(Request $request)
    {
        $data = (new Order)->record($request); 
        $data = $data->recordDetail($request->detail);     
        return bd_json($data);
    }

    public function show($id)
    {
        $data = Order::find($id);
        return bd_json($data);
    }

    public function update(Request $request, $id)
    {
        $data = Order::find($id)->record($request);
        $data = $data->deleteDetail()->recordDetail($request->detail);
        return bd_json($data);
    }

    public function destroy($id)
    {
        $data = Order::find($id);
        if ($data) {
            $data->deleteDetail();
            $data->delete();
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

}
