<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Kredit;

class KreditController extends Controller
{
    public function index()
    {
        $data = Kredit::select('*');
        $tableData = (new Kredit)->getTableProperties();
        $data = $data->searchAllFields($tableData);
        return bd_json($data);
    }

    public function store(Request $request)
    {
        $data = (new Kredit)->record($request);      
        return bd_json($data);
    }

    public function show($id)
    {
        $data = Kredit::find($id);
        return bd_json($data);
    }

    public function update(Request $request, $id)
    {
        $data = Kredit::find($id)->record($request);
        return bd_json($data);
    }

    public function destroy($id)
    {
        $data = Kredit::find($id);
        if ($data) {
            $data->delete();
        }
        return bd_json($data);
    }
}
