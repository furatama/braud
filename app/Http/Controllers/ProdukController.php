<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Produk;
use App\Kategori;
use App\Satuan;

class ProdukController extends Controller
{
    public function index()
    {        
        $produkData = Produk::select('*');
        $kategoriData = Kategori::select('id as id_kategori','nama as kategori');
        $satuanData = Satuan::select('id as id_satuan','nama as satuan');

        $data = $produkData
            ->leftJoinModel($kategoriData, 'kategori' , 'produk.id_kategori' , 'kategori.id_kategori')
            ->leftJoinModel($satuanData, 'satuan' , 'produk.id_satuan' , 'satuan.id_satuan');

        $produkTableData = (new Produk)->getTableProperties();
        $kategoriTableData = (new Kategori)->getTableProperties();
        $satuanTableData = (new Satuan)->getTableProperties();

        $kategoriTableData['fields'] = ["kategori"];
        $satuanTableData['fields'] = ["satuan"];
        
        $data = $data->searchAllFields($produkTableData,$kategoriTableData,$satuanTableData);
        // dd($data->toSql());

        return bd_json($data);
    }

    public function indexAktif()
    {        
        $data = Produk::where('aktif',1)->orderBy('nama')->get();
        return bd_json($data);
    }

    public function store(Request $request)
    {
        $data = (new Produk)->record($request);      
        return bd_json($data);
    }

    public function show($id)
    {
        $data = Produk::find($id);
        return bd_json($data);
    }

    public function update(Request $request, $id)
    {
        $data = Produk::find($id)->record($request);
        return bd_json($data);
    }

    public function destroy($id)
    {
        $data = Produk::find($id);
        if ($data) {
            $data->delete();
        }
        return bd_json($data);
    }


}
