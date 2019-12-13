<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    use \App\BDSM\ModelHelper;

    protected $table = 'invoice_detail';

    protected $hidden = ['created_at','updated_at'];

    protected $fillable = ['id_invoice','id_order'];
}