<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResepProduk extends Model
{
    use SoftDeletes;
    use \App\BDSM\ModelHelper;

    protected $table = 'resep_produk';

    protected $fillable = ['nama','jenis','berat'];
    protected $hidden = ['deleted_at', 'created_at','updated_at'];

    public static function retrieve() {
        $data = \DB::table(\DB::raw("(SELECT resep.id AS id, produk.nama AS produk, ratio, GROUP_CONCAT(bahan.nama SEPARATOR ', ') AS komposisi
        FROM resep 
        LEFT JOIN produk ON resep.id_produk = produk.id
        LEFT JOIN bahan_resep ON resep.id = bahan_resep.id_resep
        LEFT JOIN bahan ON bahan_resep.id_bahan = bahan.id
        GROUP BY resep.id) tbl"));
        return $data;
    }
}
