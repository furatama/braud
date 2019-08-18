<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;
    use \App\BDSM\ModelHelper;

    protected $table = 'customer';

    protected $hidden = ['deleted_at', 'created_at','updated_at'];

    protected $fillable = ['nama','alamat','telepon','email','keterangan','aktif'];
}
