<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetail extends Model
{
    use SoftDeletes;
    use \App\BDSM\ModelHelper;

    protected $table = 'order_detail';

    protected $hidden = ['deleted_at'];

    protected $fillable = ['id_order','id_produk','harga','qyt','diskon'];
}
