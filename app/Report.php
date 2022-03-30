<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Produk;
use App\Kredit;
use App\Order;
use App\OrderDetail;
use App\Customer;
use App\BDSM\ExcelHelper;
use DB;

class Report extends Model
{
    public static function envelop($result) {
        return DB::table(DB::raw("({$result->toSql()}) tbl"))->addBinding($result->getBindings());
    }

    public static function customer($method,$from,$to,$fCust = null) {

        $terlarisBy = [];
        if ($from != null) $terlarisBy[] = "o.tanggal >= '" . $from . "'";
        if ($to != null) $terlarisBy[] = "o.tanggal <= '" . $to . "'";
        $terlarisBy = implode(" AND ", $terlarisBy);
        if (empty($terlarisBy)) $terlarisBy = "true";

        $customerTerlaris = <<<TERLARIS
(select nama_produk from	
(select o.id_customer,
od.id_produk,
p.nama as nama_produk,
sum(qty) as total
from `order` o 
inner join order_detail od on o.id = od.id_order
inner join produk p on p.id = od.id_produk
where %s
group by o.id_customer, od.id_produk, p.nama
order by sum(qty) desc) x 
where customer.id = x.id_customer
limit 1) as terlaris
TERLARIS;
        $customerTerlaris = sprintf($customerTerlaris, $terlarisBy);

        $order = Order::query();
        $customer = Customer::select('id','nama', DB::raw($customerTerlaris));
        $detail = Order::select('id',DB::raw('SUM(qty) as qty'),DB::raw('SUM((100-diskon)/100*qty*harga) as nilai'))
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

        $result = $result->select(DB::raw($tanggal . ' as date'),'order.id_customer','cust.nama', 'cust.terlaris',
                                DB::raw('SUM(qty) as qty'),DB::raw('SUM(terbayar) as terbayar'),
                                DB::raw('SUM(nilai) as nilai'));
        
        if ($from != null) $result = $result->whereDate('tanggal','>=',$from);
        if ($to != null) $result = $result->whereDate('tanggal','<=',$to);
        if ($fCust != null) $result = $result->where('id_customer',$fCust);

        // dd($result->get()->toArray());
        return $result;
    }

    public static function customerproduk($method,$from,$to,$fCust = null) {
        $order = Order::query();
        $customer = Customer::select('id','nama');
        $detail = Order::select('id','id_produk','prod.nama as nama_produk',DB::raw('SUM(qty) as qty'),DB::raw('SUM((100-diskon)/100*qty*harga) as nilai'))
            ->joinModel(OrderDetail::select('id_order','id_produk','qty','diskon','harga'),'ordtl','ordtl.id_order','order.id')
            ->joinModel(Produk::select('nama','id as idp'),'prod','ordtl.id_produk','prod.idp')
            ->groupBy('order.id')
            ->groupBy('ordtl.id_produk')
            ->groupBy('prod.nama');

        $result = $order->joinModel($customer,'cust','cust.id','order.id_customer')
            ->leftJoinModel($detail,'dtl','dtl.id','order.id');

        if ($method === 'y') {
            $result = $result->groupBy('date','id_customer','nama','id_produk','nama_produk')->orderBy('date')->orderBy('nama');
            $tanggal = "CONCAT(YEAR(tanggal),'-01-01')";
        } else if ($method === 'm') {
            $result = $result->groupBy('date','id_customer','nama','id_produk','nama_produk')->orderBy('date')->orderBy('nama');
            $tanggal = "CONCAT(DATE_FORMAT(tanggal, '%Y-%m'),'-01')";
        } else if ($method === 'd') {
            $result = $result->groupBy('date','id_customer','nama','id_produk','nama_produk')->orderBy('date')->orderBy('nama');
            $tanggal = "order.tanggal";
        } else if ($method === 'a') {
            $result = $result->groupBy('date','id_customer','nama','id_produk','nama_produk')->orderBy('nama');
            $tanggal = "2000";
        }

        $result = $result->select(
            DB::raw($tanggal . ' as date'),
            'order.id_customer',
            'cust.nama',
            'dtl.id_produk',
            'dtl.nama_produk',
            DB::raw('SUM(qty) as qty'),
            DB::raw('SUM(nilai) as nilai')
        );
        
        if ($from != null) $result = $result->whereDate('tanggal','>=',$from);
        if ($to != null) $result = $result->whereDate('tanggal','<=',$to);
        if ($fCust != null) $result = $result->where('id_customer',$fCust);

        // dd($result->get()->toArray());
        return $result;
    }


    public static function produk($method,$from,$to,$fProd = null) {
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
        if ($fProd != null) $result = $result->where('id_produk',$fProd);

        // dd($result->get()->toArray());
        return $result;
    }

    public static function order($method,$from,$to) {
        $order = Order::query();
        $detail = OrderDetail::select('id_order','id_produk','qty','diskon','harga');

        $result = $order->joinModel($detail,'od','od.id_order','order.id');

        if ($method === 'y') {
            $result = $result->groupBy('date')->orderBy('date');
            $tanggal = "CONCAT(YEAR(tanggal),'-01-01')";
        } else if ($method === 'm') {
            $result = $result->groupBy('date')->orderBy('date');
            $tanggal = "CONCAT(DATE_FORMAT(tanggal, '%Y-%m'),'-01')";
        } else if ($method === 'd') {
            $result = $result->groupBy('date')->orderBy('date');
            $tanggal = "order.tanggal";
        } else if ($method === 'a') {
            $result = $result->groupBy('date')->orderBy('date');
            $tanggal = "2000";
        }

        $result = $result->select(DB::raw($tanggal . ' as date'),
                    DB::raw("COUNT(DISTINCT id_order) as norder"),
                    DB::raw("COUNT(DISTINCT CASE WHEN metode = 'cash' THEN id_order END) as torder"),
                    DB::raw("COUNT(DISTINCT CASE WHEN metode = 'credit' THEN id_order END) as korder"),
                    DB::raw('SUM(qty) as nproduk'),
                    DB::raw('SUM((100-diskon)/100*qty*harga) as total')
                );
        
        // $result = $order->joinModel($result,'ro','od.id_order','order.id');
        
        if ($from != null) $result = $result->whereDate('tanggal','>=',$from);
        if ($to != null) $result = $result->whereDate('tanggal','<=',$to);

        // dd($result->get()->toArray());
        return $result;
    }

    public static function excelCustomer($method,$from,$to) {
        $reportData = self::envelop(self::customer($method,$from,$to));
        if ($method === 'd')
            $reportData = $reportData->select('date as TANGGAL','nama as CUSTOMER','qty as JUMLAH_PRODUK','nilai as NILAI_ORDER','terbayar as TERBAYAR');
        else if ($method === 'm')
            $reportData = $reportData->select(DB::raw("DATE_FORMAT(date,'%Y-%m') as BULAN"),'nama as CUSTOMER','qty as JUMLAH_PRODUK','nilai as NILAI_ORDER','terbayar as TERBAYAR');
        else if ($method === 'y')
            $reportData = $reportData->select(DB::raw("YEAR(date) as TAHUN"),'nama as CUSTOMER','qty as JUMLAH_PRODUK','nilai as NILAI_ORDER','terbayar as TERBAYAR');
        else
            $reportData = $reportData->select('nama as CUSTOMER','qty as JUMLAH_PRODUK','nilai as NILAI_ORDER','terbayar as TERBAYAR');

        $reportDataGet = $reportData->get()
            ->map(function ($item, $key) {
                return (array) $item;
            })
            ->all();

        $sheet = new ExcelHelper("Customer");
        $sheet->setTitle('Laporan Customer');
        if ($method === 'd')
            $sheet->setSubTitle("Harian");
        else if ($method === 'm')
            $sheet->setSubTitle("Bulanan");
        else if ($method === 'y')
            $sheet->setSubTitle("Tahunan");

        if ($from !== null || $to !== null) {
            $from = $from == null ? '' : $from;
            $to = $to == null ? '' : $to;
            $sheet->setHeaderMeta("tgl","Tanggal {$from} s/d {$to}",'#000000',10,"right");
            // public function setHeaderMeta($meta,$string,$color="#000000",$size=12,$align="center",$weight="normal") {
        }
        $sheet->setData($reportDataGet,'#808080','#ffffff');
        
        ExcelHelper::render();
    }

    
    public static function excelCustomerproduk($method,$from,$to,$fCust = null) {
        $reportData = self::envelop(self::customerproduk($method,$from,$to, $fCust));
        if ($method === 'd')
            $reportData = $reportData->select('date as TANGGAL','nama as CUSTOMER','nama_produk as NAMA_PRODUK','qty as JUMLAH_PRODUK','nilai as NILAI_ORDER');
        else if ($method === 'm')
            $reportData = $reportData->select(DB::raw("DATE_FORMAT(date,'%Y-%m') as BULAN"),'nama as CUSTOMER','nama_produk as NAMA_PRODUK','qty as JUMLAH_PRODUK','nilai as NILAI_ORDER');
        else if ($method === 'y')
            $reportData = $reportData->select(DB::raw("YEAR(date) as TAHUN"),'nama as CUSTOMER','nama_produk as NAMA_PRODUK','qty as JUMLAH_PRODUK','nilai as NILAI_ORDER');
        else
            $reportData = $reportData->select('nama as CUSTOMER','nama_produk as NAMA_PRODUK','qty as JUMLAH_PRODUK','nilai as NILAI_ORDER');

        $reportDataGet = $reportData->get()
            ->map(function ($item, $key) {
                return (array) $item;
            })
            ->all();

        $sheet = new ExcelHelper("Produk Per Customer");
        $sheet->setTitle('Laporan Produk Per Customer');
        if ($method === 'd')
            $sheet->setSubTitle("Harian");
        else if ($method === 'm')
            $sheet->setSubTitle("Bulanan");
        else if ($method === 'y')
            $sheet->setSubTitle("Tahunan");

        if ($from !== null || $to !== null) {
            $from = $from == null ? '' : $from;
            $to = $to == null ? '' : $to;
            $sheet->setHeaderMeta("tgl","Tanggal {$from} s/d {$to}",'#000000',10,"right");
            // public function setHeaderMeta($meta,$string,$color="#000000",$size=12,$align="center",$weight="normal") {
        }
        if ($fCust != null) {
            $cust = Customer::find($fCust);
            $sheet->setHeaderMeta("cust","Customer {$cust->nama}",'#000000',10,"right");
            $sheet->setTitle("Laporan Produk Per {$cust->nama}");
        }
        $sheet->setData($reportDataGet,'#808080','#ffffff');
        
        ExcelHelper::render();
    }

    public static function excelProduk($method,$from,$to) {
        $reportData = self::envelop(self::produk($method,$from,$to));
        if ($method === 'd')
            $reportData = $reportData->select('date as TANGGAL','nama as PRODUK','qty as JUMLAH_PRODUK','total as NOMINAL','net as NETTO');
        else if ($method === 'm')
            $reportData = $reportData->select(DB::raw("DATE_FORMAT(date,'%Y-%m') as BULAN"),'nama as PRODUK','qty as JUMLAH_PRODUK','total as NOMINAL','net as NETTO');
        else if ($method === 'y')
            $reportData = $reportData->select(DB::raw("YEAR(date) as TAHUN"),'nama as PRODUK','qty as JUMLAH_PRODUK','total as NOMINAL','net as NETTO');
        else
            $reportData = $reportData->select('nama as PRODUK','qty as JUMLAH_PRODUK','total as NOMINAL','net as NETTO');

        $reportDataGet = $reportData->get()
            ->map(function ($item, $key) {
                return (array) $item;
            })
            ->all();

        $sheet = new ExcelHelper("Produk");
        $sheet->setTitle('Laporan Produk');
        if ($method === 'd')
            $sheet->setSubTitle("Harian");
        else if ($method === 'm')
            $sheet->setSubTitle("Bulanan");
        else if ($method === 'y')
            $sheet->setSubTitle("Tahunan");

        if ($from !== null || $to !== null) {
            $from = $from == null ? '' : $from;
            $to = $to == null ? '' : $to;
            $sheet->setHeaderMeta("tgl","Tanggal {$from} s/d {$to}",'#000000',10,"right");
            // public function setHeaderMeta($meta,$string,$color="#000000",$size=12,$align="center",$weight="normal") {
        }
        $sheet->setData($reportDataGet,'#808080','#ffffff');
        
        ExcelHelper::render();
    }

    public static function excelOrder($method,$from,$to) {
        $reportData = self::envelop(self::order($method,$from,$to));
        if ($method === 'd')
            $reportData = $reportData->select('date as TANGGAL','norder AS JUMLAH_ORDER','nproduk as JUMLAH_PRODUK','total as NOMINAL','torder as CASH','korder as CREDIT');
        else if ($method === 'm')
            $reportData = $reportData->select(DB::raw("DATE_FORMAT(date,'%Y-%m') as BULAN"),'norder AS JUMLAH_ORDER','nproduk as JUMLAH_PRODUK','total as NOMINAL','torder as CASH','korder as CREDIT');
        else if ($method === 'y')
            $reportData = $reportData->select(DB::raw("YEAR(date) as TAHUN"),'norder AS JUMLAH_ORDER','nproduk as JUMLAH_PRODUK','total as NOMINAL','torder as CASH','korder as CREDIT');
        else
            $reportData = $reportData->select('norder AS JUMLAH_ORDER','nproduk as JUMLAH_PRODUK','total as NOMINAL','torder as CASH','korder as CREDIT');

        $reportDataGet = $reportData->get()
            ->map(function ($item, $key) {
                return (array) $item;
            })
            ->all();

        $sheet = new ExcelHelper("Order");
        $sheet->setTitle('Laporan Order');
        if ($method === 'd')
            $sheet->setSubTitle("Harian");
        else if ($method === 'm')
            $sheet->setSubTitle("Bulanan");
        else if ($method === 'y')
            $sheet->setSubTitle("Tahunan");

        if ($from !== null || $to !== null) {
            $from = $from == null ? '' : $from;
            $to = $to == null ? '' : $to;
            $sheet->setHeaderMeta("tgl","Tanggal {$from} s/d {$to}",'#000000',10,"right");
            // public function setHeaderMeta($meta,$string,$color="#000000",$size=12,$align="center",$weight="normal") {
        }
        $sheet->setData($reportDataGet,'#808080','#ffffff');
        
        ExcelHelper::render();
    }
}

