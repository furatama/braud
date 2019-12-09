<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResepDetail extends Model
{
    use SoftDeletes;
    use \App\BDSM\ModelHelper;

    protected $table = 'resep_detail';

    protected $fillable = ['id_resep','id_produk','ratio','berat'];
    protected $hidden = ['deleted_at', 'created_at','updated_at'];
    
    public static function retrieve() {
        $data = \DB::table(\DB::raw("(
            SELECT resep_detail.*, resep_produk.nama as resep, produk.nama as produk
            FROM resep_detail
            LEFT JOIN resep_produk ON resep_produk.id = resep_detail.id_resep
            LEFT JOIN produk ON resep_detail.id_produk = produk.id) tbl
            ORDER BY resep, ratio
            "));
        return $data;
    }



}
