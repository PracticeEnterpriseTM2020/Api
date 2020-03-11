<?php

namespace App\Http\Controllers;
use App\Supplier;
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

        $land = request('country');
        $landId = \DB::table('country')->where("name",$land)->value('id');
        


        $stad = request('city');
        $stadId = \DB::table('city')->where("countryId",$landId)->where('name', $stad)->value('id');


        $straat = request('straat');
        $number = request('nummer');
        $adres = \DB::table('addresses')->where("cityId",$stadId)->where('street', $straat)->where('number',$number)->value('id');


        $suppliers = new Supplier();

        $suppliers->companyname = request('companyname');
        $suppliers->vatnumber = request('vatnumber');
        $suppliers->email = request('email');
        $suppliers->addressId = $landId;
        $suppliers->phonenumber = request('phonenumber');
        $suppliers->save();
        echo $adres;
    }
}
