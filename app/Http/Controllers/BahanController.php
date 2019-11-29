<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bahan;
use App\BahanResep;

class BahanController extends Controller
{
    public function index()
    {
        $data = Bahan::select('*');
        $tableData = (new Bahan)->getTableProperties();
        $data = $data->searchAllFields($tableData);
        return bd_json($data);
    }

    public function store(Request $request)
    {
        $data = (new Bahan)->record($request);      
        return bd_json($data);
    }

    public function show($id)
    {
        $data = Bahan::find($id);
        return bd_json($data);
    }

    public function update(Request $request, $id)
    {
        $data = Bahan::find($id)->record($request);
        return bd_json($data);
    }

    public function destroy($id)
    {
        $data = Bahan::find($id);
        if ($data) {
            $data->delete();
        }
        return bd_json($data);
    }

    public function getBahan($id_resep) {
        $data = Bahan::select('bahan.*','bahan.nama as bahan','batch')->joinModel(
            BahanResep::where('id_resep',$id_resep),'br','bahan.id','br.id_bahan'
        );
        return bd_json($data);
    }
}
