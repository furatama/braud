<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bahan;
use App\BahanResep;
use Illuminate\Support\Facades\DB;

class OrderDisplayController extends Controller
{
    
    public function index(Request $request) {
        

        DB::select('select * from users where active = ?', [1]);
    }

}
