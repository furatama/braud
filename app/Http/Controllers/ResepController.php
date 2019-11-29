<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Resep;
use App\BahanResep;
use App\Produk;

class ResepController extends Controller
{
    public function index()
    {
        $data = Resep::retrieve()->searchAllFields();
        return bd_json($data);
    }    

    private function storeKomposisiResep($komposisi,$id)
    {
        BahanResep::where('id_resep',$id)->delete();
        
        $requestData = [];
        foreach ($komposisi as $rdtl) {
            $requestData[] = (new BahanResep)->record([
                'id_bahan' => $rdtl['id_bahan'],
                'id_resep' => $id,
                'batch' => $rdtl['batch'],
            ]);
        }
  
        return $requestData;
    }

    public function store(Request $request)
    {
        $data = (new Resep)->record($request);    
        $komposisi = $this->storeKomposisiResep($request->komposisi,$data->id);
        $data = $data->toArray();
        $data['komposisi'] = $komposisi;
        return bd_json($data);
    }

    public function show($id)
    {
        $data = Resep::find($id);  
        $komposisi = BahanResep::where('id_resep',$id)->get()->toArray();
        $data = $data->toArray();
        $data['komposisi'] = $komposisi;
        return bd_json(['data' => $data]);
    }

    public function update(Request $request, $id)
    {
        $data = Resep::find($id)->record($request);  
        $komposisi = $this->storeKomposisiResep($request->komposisi,$id);
        $data = $data->toArray();
        $data['komposisi'] = $komposisi;
        return bd_json($data);
    }

    public function destroy($id)
    {
        $data = Resep::find($id);
        if ($data) {
            BahanResep::where('id_resep',$id)->delete();
            $data->delete();
        }
        return bd_json($data);
    }

    public function resepOptions() {
        $data = Resep::select('resep.id','nama as produk')->joinModel(
            Produk::select('*'),'prod','prod.id','resep.id_produk'
        );
        return bd_json($data);
    }
}
