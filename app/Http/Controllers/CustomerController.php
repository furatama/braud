<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer;
use App\Harga;

class CustomerController extends Controller
{
    public function index()
    {
        $data = Customer::select('*');
        $tableData = (new Customer)->getTableProperties();
        $data = $data->searchAllFields($tableData);
        return bd_json($data);
    }

    public function store(Request $request)
    {
        $data = (new Customer)->record($request);      
        return bd_json($data);
    }

    public function show($id)
    {
        $data = Customer::find($id);
        return bd_json($data);
    }

    public function update(Request $request, $id)
    {
        $data = Customer::find($id)->record($request);
        return bd_json($data);
    }

    public function destroy($id)
    {
        $data = Customer::find($id);
        if ($data) {
            $data->delete();
        }
        return bd_json($data);
    }

    public function showHarga($id)
    {
        $data = Harga::where("id_customer",$id);
        return bd_json($data);
    }

    public function storeHarga(Request $request,$id)
    {
        Harga::where('id_customer',$id)->delete();
        $data = Harga::massRecord($request->harga);
        return bd_json($data);
    }
    
}