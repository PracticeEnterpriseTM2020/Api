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
    public function store($companyname, $vatnumber, $email, $addressId, $phonenumber)
    {
        $Leverancier = new Leverancier();

        $Leverancier->companyname = request('Leverancier_id');
        $Leverancier->creation_timestamp = strtotime(request('creation_timestamp'));
        $Leverancier->save();
        $data=array("companyname"=>$companyname,"vatnumber"=>$vatnumber,"email"=>$email,"addressId"=>$addressId, "phonenumber"=>$phonenumber);
        \DB::table('suppliers')->insert($data);
        echo "Record inserted successfully.<br/>";
    }
}
