<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OrderDetail;

class OrderDetailController extends Controller
{
    public function byIDOrder($id_order) {
        $data = OrderDetail::where('id_order',$id_order)->joinModel(
            \App\Produk::query(),'produk','order_detail.id_produk','produk.id'
        )->select('order_detail.*','produk.nama as produk');
        return bd_json($data);
    }
}
