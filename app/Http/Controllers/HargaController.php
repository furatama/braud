<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Harga;

class HargaController extends Controller
{
    public function index()
    {
        $data = Harga::select('*');
        $tableData = (new Harga)->getTableProperties();
        $data = $data->searchAllFields($tableData);
        return bd_json($data);
    }

    public function store(Request $request)
    {
        $data = (new Harga)->record($request);      
        return bd_json($data);
    }

    public function show($id)
    {
        $data = Harga::find($id);
        return bd_json($data);
    }

    public function update(Request $request, $id)
    {
        $data = Harga::find($id)->record($request);
        return bd_json($data);
    }

    public function destroy($id)
    {
        $data = Harga::find($id);
        if ($data) {
            $data->delete();
        }
        return bd_json($data);
    }
}
