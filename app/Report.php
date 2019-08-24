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

    public static function customer($method,$from,$to) {
        $order = Order::query();
        $customer = Customer::select('id','nama');
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

        $result = $result->select(DB::raw($tanggal . ' as date'),'order.id_customer','cust.nama',
                                DB::raw('SUM(qty) as qty'),DB::raw('SUM(terbayar) as terbayar'),
                                DB::raw('SUM(nilai) as nilai'));
        
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
        } else if ($method === 'd' || $method === 'a') {
            $result = $result->groupBy('date')->orderBy('date');
            $tanggal = "order.tanggal";
        }

        $result = $result->select(DB::raw($tanggal . ' as date'),
                    DB::raw('COUNT(DISTINCT id_order) as norder'),DB::raw('SUM(qty) as nproduk'),
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
            $reportData = $reportData->select('date as TANGGAL','norder AS JUMLAH_ORDER','nproduk as JUMLAH_PRODUK','total as NOMINAL');
        else if ($method === 'm')
            $reportData = $reportData->select(DB::raw("DATE_FORMAT(date,'%Y-%m') as BULAN"),'norder AS JUMLAH_ORDER','nproduk as JUMLAH_PRODUK','total as NOMINAL');
        else if ($method === 'y')
            $reportData = $reportData->select(DB::raw("YEAR(date) as TAHUN"),'norder AS JUMLAH_ORDER','nproduk as JUMLAH_PRODUK','total as NOMINAL');
        else
            $reportData = $reportData->select('norder AS JUMLAH_ORDER','nproduk as JUMLAH_PRODUK','total as NOMINAL');

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

