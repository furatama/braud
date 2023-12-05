<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    use \App\BDSM\ModelHelper;

    protected $table = 'order';

    protected $hidden = ['deleted_at', 'created_at','updated_at'];

    protected $fillable = ['no','id_customer','metode','tanggal','due','faktur','tunai','keterangan','id_user','tgl_kirim','tax'];

    use \App\BDSM\ModelDetailHelper;
    protected $detailModelClass = "App\\OrderDetail";
    protected $detailForeignKey = "id_order";

    public function getTaxAttribute() {
        return number_format($this->attributes['tax'] ?? 0);
    }

    public static function listing() {
        $order = Order::select('order.id','no','metode','tanggal','tax');
        $customer = \App\Customer::select('id','nama as customer');
        $detail = \App\OrderDetail::select('id_order',\DB::raw('SUM((100-diskon)/100*qty*harga) as total'))->groupBy('id_order');
        $date = date('Y-m-d');

        $data = $order->joinModel($customer, 'cust' , 'cust.id' , 'order.id_customer')
                ->joinModel($detail, 'od' , 'od.id_order' , 'order.id')
                ->select('order.*', 'customer',
                    \DB::raw('od.total * ((100+ifnull(tax,0))/100) as total'),
                    \DB::raw("IF(order.tunai < (od.total * ((100+ifnull(tax,0))/100)), IF(date('{$date}')>date(order.due),2,0),1) AS lunas"));
        
        $data = \DB::table(\DB::raw("({$data->toSql()}) tbl"))->orderBy('tanggal','DESC')->orderBy('id','DESC');

        
        // dd($data->toSql());

        return $data;

        
//     SELECT `order`.id,`no` AS noOrder, metode, tanggal, nama AS customer, tunai, total,IF(tunai<total,0,1) AS lunas 
// FROM `order` INNER JOIN customer ON `order`.id_customer = customer.id
// INNER JOIN (SELECT id_order,SUM((100-diskon)/100*qty*harga) AS total 
// FROM order_detail GROUP BY id_order) od ON od.id_order = `order`.id
    }

    public static function custListing($idCust) {
        $order = Order::select('order.id','no','metode','tanggal','tax');
        $customer = \App\Customer::select('id','nama as customer');
        $detail = \App\OrderDetail::select('id_order',\DB::raw('SUM((100-diskon)/100*qty*harga) as total'))->groupBy('id_order');
        $date = date('Y-m-d');

        $data = $order->joinModel($customer, 'cust' , 'cust.id' , 'order.id_customer')
                ->joinModel($detail, 'od' , 'od.id_order' , 'order.id')
                ->select('order.*', 'customer',
                    \DB::raw('od.total * ((100+ifnull(tax,0))/100) as total'),
                    \DB::raw("IF(order.tunai < (od.total * ((100+ifnull(tax,0))/100)), IF(date('{$date}')>date(order.due),2,0),1) AS lunas"));
        
        $data = \DB::table(\DB::raw("({$data->toSql()}) tbl"))
            ->where('id_customer',$idCust)
            ->where('metode','credit')
            ->orderBy('tanggal','DESC')
            ->orderBy('id','DESC');
            // ->get();
        $dari = request('dari');
        $sampai = request('sampai');
        if ($dari) {
            $data = $data->whereDate('tanggal','>=', $dari);
        }
        if ($sampai) {
            $data = $data->whereDate('tanggal','<=', $sampai);
        }
        
        // dd($data->toSql());

        return $data;

        
//     SELECT `order`.id,`no` AS noOrder, metode, tanggal, nama AS customer, tunai, total,IF(tunai<total,0,1) AS lunas 
// FROM `order` INNER JOIN customer ON `order`.id_customer = customer.id
// INNER JOIN (SELECT id_order,SUM((100-diskon)/100*qty*harga) AS total 
// FROM order_detail GROUP BY id_order) od ON od.id_order = `order`.id
    }
    
}
