<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Supplier;
use App\kosten;
use App\energie_info;

class kostencontroler extends Controller
{
    public function store(Request $request)
    {
        $kosten = new kosten();
        $type = htmlspecialchars(request('type'));
        $supplierId = htmlspecialchars(request('supplierId'));
        if ($supplier == "")
        {
            $vat = htmlspecialchars(request('vatnumber'));
            $supplierId = \DB::table('suppliers')->where('Vatnumber',$vat)->where('isset',1)->get('id');
        }
        $prijs = htmlspecialchars(request('prijsPereenheid'));
        $eenheid = htmlspecialchars(request('eenheid'));
        $opwekkingstype = htmlspecialchars(request('opwekking'));
        
        $kosten->save();
    }
}
