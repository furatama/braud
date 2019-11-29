<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bahan extends Model
{
    use \App\BDSM\ModelHelper;

    protected $table = 'bahan';

    protected $fillable = ['nama','harga','jenis'];
}
