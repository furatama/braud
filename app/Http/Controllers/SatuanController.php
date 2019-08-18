<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Satuan;

class SatuanController extends Controller
{
    public function index()
    {
        $data = Satuan::select('*');
        $tableData = (new Satuan)->getTableProperties();
        $data = $data->searchAllFields($tableData);
        return bd_json($data);
    }

    public function store(Request $request)
    {
        $data = (new Satuan)->record($request);      
        return bd_json($data);
    }

    public function show($id)
    {
        $data = Satuan::find($id);
        return bd_json($data);
    }

    public function update(Request $request, $id)
    {
        $data = Satuan::find($id)->record($request);
        return bd_json($data);
    }

    public function destroy($id)
    {
        $data = Satuan::find($id);
        if ($data) {
            $data->delete();
        }
        return bd_json($data);
    }
}
