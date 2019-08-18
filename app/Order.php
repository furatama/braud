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

    protected $fillable = ['no','id_customer','metode','tanggal','faktur','tunai','keterangan','id_user'];

    use \App\BDSM\ModelDetailHelper;
    protected $detailModelClass = "App\\OrderDetail";
    protected $detailForeignKey = "id_order";
}
