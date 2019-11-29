<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BahanResep;

class BahanResepController extends Controller
{
    public function index()
    {
        $data = BahanResep::select('*');
        $tableData = (new BahanResep)->getTableProperties();
        $data = $data->searchAllFields($tableData);
        return bd_json($data);
    }

    public function store(Request $request)
    {
        $data = (new BahanResep)->record($request);      
        return bd_json($data);
    }

    public function show($id)
    {
        $data = BahanResep::find($id);
        return bd_json($data);
    }

    public function update(Request $request, $id)
    {
        $data = BahanResep::find($id)->record($request);
        return bd_json($data);
    }

    public function destroy($id)
    {
        $data = BahanResep::find($id);
        if ($data) {
            $data->delete();
        }
        return bd_json($data);
    }
}
