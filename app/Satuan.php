<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    use \App\BDSM\ModelHelper;

    protected $table = 'satuan';

    protected $fillable = ['nama','keterangan'];

    public $timestamps = false;
}
