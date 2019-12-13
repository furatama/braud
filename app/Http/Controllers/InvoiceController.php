<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\OrderDetail;
use App\Kredit;
use App\Invoice;
use App\InvoiceDetail;
use App\Customer;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $data = Invoice::listing();
        $cust = $request->id_customer;
        $dari = $request->dari;
        $sampai = $request->sampai;
        if ($cust) {
            $data = $data->where('id_customer', $cust);
        }
        if ($dari) {
            $data = $data->whereDate('tanggal','>=', $dari);
        }
        if ($sampai) {
            $data = $data->whereDate('tanggal','<=', $sampai);
        }
        
        return bd_json($data);
    }

    public function store(Request $request)
    {
        $data = (new Invoice)->record($request); 
        $data = $data->recordDetail($request->detail);

        return bd_json($data);
    }

    public function show($id)
    {
        $data = Invoice::find($id);
        return bd_json($data);
    }

    

    public function update(Request $request, $id)
    {
        $lunas = $request->lunaskan;
        $invoice = Invoice::find($id);
        $invoice->record($request);

        if ($lunas) {
            $detail = Invoice::selectRaw("invoice.id, GROUP_CONCAT(invd.id_order SEPARATOR ',') AS oid")->joinModel(
                InvoiceDetail::select('*'),'invd','invd.id_invoice','invoice.id'
            )->groupBy('invoice.id')->where('invoice.id',$id)->first()->toArray();
            $detail = explode(',',$detail['oid']);
            // dd($detail);
            $od = OrderDetail::select('id_order',\DB::raw('SUM((100-diskon)/100*qty*harga) as total'))->groupBy('id_order');
            Kredit::whereIn('id_order',$detail)->delete();
            $data = Order::whereIn('id',$detail)
                ->joinModel($od, 'od' , 'od.id_order' , 'order.id');
            $update = $data->update([
                'tunai' => \DB::raw("`total`")
            ]);
                
            $kredit = $data->get()->map(function($items) {
                    $kreditParams = [
                        "id_order" => $items->id,
                        "tunai" => $items->total
                    ];
                    $kredit = (new Kredit)->record($kreditParams);
                    return $items;
                });

            $invoice->lunas_at = Carbon::now();
            $invoice->save();
        }

        return bd_json($invoice);
    }

    public function destroy($id)
    {
        $data = Invoice::find($id);
        if ($data) {
            $data->deleteDetail();
            $data->delete();
        }
        return bd_json($data);
    }
}
