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
        $data = Report::customer($method,$from,$to);
        return bd_json(Report::envelop($data));
    }

    public function produk(Request $request) {
        $method = $request->input('method','d');
        $from = $request->input('from',null);
        $to = $request->input('to',null);
        $data = Report::produk($method,$from,$to);
        return bd_json(Report::envelop($data));
    }
}
