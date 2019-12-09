<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ResepDetail;

class ResepDetailController extends Controller
{
    public function index()
    {
        $data = ResepDetail::retrieve();
        $data = $data->searchAllFields();
        return bd_json($data);
    }

    public function store(Request $request)
    {
        $data = (new ResepDetail)->record($request);      
        return bd_json($data);
    }

    public function show($id)
    {
        $data = ResepDetail::find($id);
        return bd_json($data);
    }

    public function update(Request $request, $id)
    {
        $data = ResepDetail::find($id)->record($request);
        return bd_json($data);
    }

    public function destroy($id)
    {
        $data = ResepDetail::find($id);
        if ($data) {
            $data->delete();
        }
        return bd_json($data);
    }
}
