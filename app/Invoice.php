<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;
    use \App\BDSM\ModelHelper;

    protected $table = 'invoice';

    protected $fillable = ['id_customer','catatan','tanggal','lunas_at'];
    use \App\BDSM\ModelDetailHelper;
    protected $detailModelClass = "App\\InvoiceDetail"; //nama class detail
    protected $detailForeignKey = "id_invoice"; //nama foreign key pada tabel detail

    public static function listing() {
        $data = \DB::table(\DB::raw("(
            SELECT invoice.id AS id, 
                invoice.tanggal,
                customer.id AS id_customer,
                customer.nama AS customer, 
                catatan, 
                IF(invoice.lunas_at IS NULL,0,1) as lunas,
                GROUP_CONCAT(SUBSTR(`order`.no,-3,3) SEPARATOR ',') AS no,
                SUM(od.total) as `order`
            FROM invoice 
            LEFT JOIN customer ON invoice.id_customer = customer.id
            LEFT JOIN invoice_detail ON invoice.id = invoice_detail.id_invoice
            LEFT JOIN `order` ON invoice_detail.id_order = `order`.id
            LEFT JOIN (
                SELECT id_order, SUM((100-diskon)/100*qty*harga) as total
                FROM order_detail
                WHERE deleted_at IS NULL
                GROUP BY id_order
            ) od ON od.id_order = order.id
            GROUP BY invoice.id, invoice.tanggal, customer.id, lunas_at, customer.nama, catatan
        ) tbl"));
        return $data;
    }
}
