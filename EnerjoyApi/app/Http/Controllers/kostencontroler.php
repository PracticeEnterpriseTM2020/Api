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
        $type = htmlspecialchars(request("type"));
        if (preg_match("/[^A-Za-z]+/",$type) || $tupe == "")
        {
            return "[{\"failed\" : \"This_is_no_type\"}]";
        }
        $supplierId = htmlspecialchars(request('supplierId'));
        if (preg_match("/[^0-9]+/",$supplierId) || $supplierId == "")
        {
            return "[{\"failed\" : \"This_is_no_id\"}]";
        }
        if ($supplierId == "")
        {
            $vat = htmlspecialchars(request('vatnumber'));
            if (preg_match("/\A[A-Z]{2}[A-Za-z0-9]{2,13}\b/",$vat) && $vat != "")
            {
                $supplierId = \DB::table('suppliers')->where('Vatnumber',$vat)->where('isset',1)->value('id');
                if ($supplierId == "")
                {
                    return "[{\"failed\" : \"(".$vat.") is not in use\"}]";
                }
            }
            else
            {
                return "[{\"failed\" : \"(".$vat.") is no valit vatnumber\"}]";
            }
        }
        $prijs = htmlspecialchars(request('prijsPerEenheid'));
        if (preg_match("/[^0-9]+/",$prijs) || $prijs == "")
        {
            return "[{\"failed\" : \"expected a number\"}]";
        }
        $eenheid = htmlspecialchars(request('eenheid'));
        if (preg_match("/\p{No}/",$eenheid) || $eenheid == "")
        {
            return "[{\"failed\" : \"subscripts are not allowed use normal numbers\"}]";
        }
        $kosten->type = $type;
        $kosten->supplierId = $supplierId;
        $kosten->prijs_per_eenheid = $prijs;
        $kosten->eenheid = $eenheid;
        $kosten->save();
        return "[{\"succes\" : \"tarief is toegevoegd\"}]";
    }

    public function rekenaar(Request $request)
    {
        $verbruik = htmlspecialchars(request('verbruik'));
        if (preg_match("/[^0-9]+/",$verbruik) || $verbruik == "")
        {
            return "[{\"failed\" : \"expected a number for verbruik\"}]";
        }
        
        $dealId = htmlspecialchars(request('tariefid'));
        if (preg_match("/[^0-9]+/",$dealId) || $dealId == "")
        {
            return "[{\"failed\" : \"expected a number for id\"}]";
        }
        $vermenigvuldiger = \DB::table('kostens')->where('id',$dealId)->value('prijs_per_eenheid');
        if ($vermenigvuldiger == "")
        {
            return "[{\"failed\" : \"wasn't a valid id\"}]";
        }
        $totaalPrijs = $vermenigvuldiger * $verbruik;
        return "[{\"succes\" : \"$totaalPrijs is de totaalprijs\"}]";

    }
}
