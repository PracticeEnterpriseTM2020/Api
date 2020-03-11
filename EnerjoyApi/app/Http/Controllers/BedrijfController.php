<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BedrijfController extends Controller
{
    public function ophalen($manier,$zoek)
    {      
        if ($manier == 'id' || $manier == 'companyname' || $manier == 'vatnumber' || $manier == 'email' || $manier == 'address' || $manier == 'phnenumber')
        {
            // de manier voor opzoeken op address aanpassen.
            $persoon= \DB::table('suppliers')->where($manier, $zoek)->get();
            if ($persoon == "[]")
            {
                $persoon = "[{\"failed\" : \"notFound\"}]";
            }
            return $persoon;
        }  

        else
        {
            return '[{"failed" : "wrongMethod"}]';
        }
    }
    public function store(Request $request)
    {
      $bedrijf = suplier::create([
        'id' => $request->id,
        'companyname' => $request->companyname,
        'vat' => $request->vatnumbre,
        'email' => $request->email, 
        'addressId' => (string) $request->addressId,
        'phonenumber' => $request->phonenumber,
      ]);

      return new suplierresource($bedrijf);
    }
}
