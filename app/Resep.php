<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Bahan;
use BahanResep;

class Resep extends Model
{
    use \App\BDSM\ModelHelper;

    protected $table = 'resep';

    protected $fillable = ['id_produk','ratio'];

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
