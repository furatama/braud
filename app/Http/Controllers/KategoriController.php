<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Kategori;

class KategoriController extends Controller
{
    public function index()
    {
        $data = Kategori::select('*');
        $tableData = (new Kategori)->getTableProperties();
        $data = $data->searchAllFields($tableData);
        return bd_json($data);
    }

    public function store(Request $request)
    {
        $data = (new Kategori)->record($request);      
        return bd_json($data);
    }

    public function show($id)
    {
        $data = Kategori::find($id);
        return bd_json($data);
    }

    public function update(Request $request, $id)
    {
        $data = Kategori::find($id)->record($request);
        return bd_json($data);
    }

    public function destroy($id)
    {
        $data = Kategori::find($id);
        if ($data) {
            $data->delete();
        }
        return bd_json($data);
    }
}
