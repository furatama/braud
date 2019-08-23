<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Produk;
use App\Kredit;
use App\Order;
use App\OrderDetail;
use App\Customer;
use DB;

class Report extends Model
{
    public static function envelop($result) {
        return DB::table(DB::raw("({$result->toSql()}) tbl"))->addBinding($result->getBindings());
    }

    public static function customer($method,$from,$to) {
        $order = Order::query();
        $customer = Customer::select('id','nama');
        $detail = Order::select('id',DB::raw('SUM((100-diskon)/100*qty*harga) as nilai'))
            ->joinModel(OrderDetail::select('id_order','qty','diskon','harga'),'ordtl','ordtl.id_order','order.id')
            ->groupBy('order.id');
        $kredit = Kredit::select('id_order',DB::raw('SUM(tunai) as terbayar'))->groupBy('id_order');

        $result = $order->joinModel($customer,'cust','cust.id','order.id_customer')
            ->leftJoinModel($detail,'dtl','dtl.id','order.id')
            ->leftJoinModel($kredit,'krdt','krdt.id_order','order.id');

        if ($method === 'y') {
            $result = $result->groupBy('date','id_customer','nama')->orderBy('date')->orderBy('nama');
            $tanggal = "CONCAT(YEAR(tanggal),'-01-01')";
        } else if ($method === 'm') {
            $result = $result->groupBy('date','id_customer','nama')->orderBy('date')->orderBy('nama');
            $tanggal = "CONCAT(DATE_FORMAT(tanggal, '%Y-%m'),'-01')";
        } else if ($method === 'd') {
            $result = $result->groupBy('date','id_customer','nama')->orderBy('date')->orderBy('nama');
            $tanggal = "order.tanggal";
        } else if ($method === 'a') {
            $result = $result->groupBy('date','id_customer','nama')->orderBy('nama');
            $tanggal = "2000";
        }

        $result = $result->select(DB::raw($tanggal . ' as date'),'order.id_customer','cust.nama',
                    DB::raw('SUM(terbayar) as terbayar'),DB::raw('SUM(nilai) as nilai'));
        
        if ($from != null) $result = $result->whereDate('tanggal','>=',$from);
        if ($to != null) $result = $result->whereDate('tanggal','<=',$to);

        // dd($result->get()->toArray());
        return $result;
    }

    public static function produk($method,$from,$to) {
        $order = Order::query();
        $produk = Produk::select('id','kode','nama');
        $detail = OrderDetail::select('id_order','id_produk','qty','diskon','harga');

        $result = $order->joinModel($detail,'od','od.id_order','order.id')
            ->joinModel($produk,'prod','prod.id','od.id_produk');

        if ($method === 'y') {
            $result = $result->groupBy('date','id_produk','nama')->orderBy('date')->orderBy('nama');
            $tanggal = "CONCAT(YEAR(tanggal),'-01-01')";
        } else if ($method === 'm') {
            $result = $result->groupBy('date','id_produk','nama')->orderBy('date')->orderBy('nama');
            $tanggal = "CONCAT(DATE_FORMAT(tanggal, '%Y-%m'),'-01')";
        } else if ($method === 'd') {
            $result = $result->groupBy('date','id_produk','nama')->orderBy('date')->orderBy('nama');
            $tanggal = "order.tanggal";
        } else if ($method === 'a') {
            $result = $result->groupBy('date','id_produk','nama')->orderBy('nama');
            $tanggal = "2000";
        }

        $result = $result->select(DB::raw($tanggal . ' as date'),'id_produk','nama',
                    DB::raw('SUM(qty) as qty'),DB::raw('SUM(qty*harga) as total'),
                    DB::raw('SUM((100-diskon)/100*qty*harga) as net')
                );
        
        if ($from != null) $result = $result->whereDate('tanggal','>=',$from);
        if ($to != null) $result = $result->whereDate('tanggal','<=',$to);

        // dd($result->get()->toArray());
        return $result;
    }
}

