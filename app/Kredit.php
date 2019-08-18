<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kredit extends Model
{
    use SoftDeletes;
    use \App\BDSM\ModelHelper;

    protected $table = 'kredit';

    protected $hidden = ['deleted_at', 'created_at','updated_at'];

    protected $fillable = ['id_order','tunai','keterangan'];
}
