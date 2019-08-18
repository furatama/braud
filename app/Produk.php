<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produk extends Model
{
    use SoftDeletes;
    use \App\BDSM\ModelHelper;

    protected $table = 'produk';

    protected $hidden = ['deleted_at', 'created_at','updated_at'];

    protected $fillable = ['kode','nama','id_satuan','id_kategori','keterangan','aktif'];

}
