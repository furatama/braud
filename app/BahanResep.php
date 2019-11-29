<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BahanResep extends Model
{
    use \App\BDSM\ModelHelper;

    protected $table = 'bahan_resep';

    protected $fillable = ['id_resep','id_bahan','batch'];

    public $timestamps = false;
}
