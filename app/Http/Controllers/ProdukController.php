<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Produk;
use App\Kategori;
use App\Satuan;
use App\Harga;
use App\Customer;
use DB;

class ProdukController extends Controller
{
    public function index()
    {        
        $produkData = Produk::selectRaw('*, CAST(produk.harga_global AS SIGNED) as harga_global');
        $kategoriData = Kategori::select('id as id_kategori','nama as kategori');
        $satuanData = Satuan::select('id as id_satuan','nama as satuan');

        $data = $produkData
            ->leftJoinModel($kategoriData, 'kategori' , 'produk.id_kategori' , 'kategori.id_kategori')
            ->leftJoinModel($satuanData, 'satuan' , 'produk.id_satuan' , 'satuan.id_satuan');
        
        $data = $data->searchAllFields();

        return bd_json($data);
    }

    public function indexAktif()
    {        
        $data = Produk::where('aktif',1)->orderBy('nama')->get();
        return bd_json($data);
    }

    public function indexAll()
    {        
        $data = Produk::orderBy('nama')->get();
        return bd_json($data);
    }

    public function store(Request $request)
    {
        $data = (new Produk)->record($request);
        $this->_updateHargaCustomer($request, $data->id);
        return bd_json($data);
    }

    public function show($id)
    {
        $data = Produk::selectRaw('*, CAST(produk.harga_global AS SIGNED) as harga_global')->find($id);
        return bd_json($data);
    }

    public function update(Request $request, $id)
    {
        $this->_updateHargaCustomer($request, $id);
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

    public function byCustomer($id_customer)
    {        
        $harga = Harga::where('id_customer', $id_customer)->select('harga','id_produk');
        $data = Produk::where('aktif',1)->select('*')->joinModel($harga,'harga', 'produk.id', 'harga.id_produk')->orderBy('nama')->get();

        return bd_json($data);
    }

    public function options()
    {        
        $data = Produk::select('id','nama')->orderBy('nama');
        return bd_json($data);
    }

    private function _updateHargaCustomer($request, $id_produk = null) {
        if ($request->harga_global > 0) {
            $harga = Customer::all()->map(function($item) use ($id_produk, $request) {
                return [
                    'id_customer' => $item->id,
                    'id_produk' => $id_produk,
                    'harga' => $request->harga_global
                ];
            });

            DB::transaction(function () use ($harga, $id_produk) {
                Harga::where('id_produk',$id_produk)->delete();
                Harga::massRecord($harga);
            });
        }
    }


}
