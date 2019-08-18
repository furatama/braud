<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Harga extends Model
{
    use \App\BDSM\ModelHelper;

    protected $table = 'harga';

    protected $fillable = ['id_customer','id_produk','harga'];

    public $timestamps = false;
}
