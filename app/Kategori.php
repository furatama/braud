<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use \App\BDSM\ModelHelper;

    protected $table = 'kategori';

    protected $fillable = ['nama','keterangan'];

    public $timestamps = false;
}
