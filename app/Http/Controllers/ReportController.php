<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Report;

class ReportController extends Controller
{
    public function customer(Request $request) {
        $method = $request->input('method','d');
        $from = $request->input('from',null);
        $to = $request->input('to',null);
        $customer = $request->input('customer',null);
        $data = Report::customer($method,$from,$to,$customer);
        return bd_json(Report::envelop($data));
    }

    public function produk(Request $request) {
        $method = $request->input('method','d');
        $from = $request->input('from',null);
        $to = $request->input('to',null);
        $produk = $request->input('produk',null);
        $data = Report::produk($method,$from,$to,$produk);
        return bd_json(Report::envelop($data));
    }

    public function order(Request $request) {
        $method = $request->input('method','d');
        $from = $request->input('from',null);
        $to = $request->input('to',null);
        $data = Report::order($method,$from,$to);
        return bd_json(Report::envelop($data));
    }

    public function customerExcel(Request $request) {
        $method = $request->input('method','d');
        $from = $request->input('from',null);
        $to = $request->input('to',null);
        Report::excelCustomer($method,$from,$to);
    }

    public function produkExcel(Request $request) {
        $method = $request->input('method','d');
        $from = $request->input('from',null);
        $to = $request->input('to',null);
        Report::excelProduk($method,$from,$to);
    }

    public function orderExcel(Request $request) {
        $method = $request->input('method','d');
        $from = $request->input('from',null);
        $to = $request->input('to',null);
        Report::excelOrder($method,$from,$to);
    }
}
