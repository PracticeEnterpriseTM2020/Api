<?php

namespace App\Http\Controllers;
use App\Supplier;
use App\country;
use App\City;
use App\address;
use Illuminate\Http\Request;

class BedrijfController extends Controller
{

    //Met deze functie kan je ofwel zoeken op apparte personen, dit kan op verschillende manieren id, companyname....
    //Als je geen variabele doorgeeft, krijg je al de waarden terug.

    // nog aanpassen als ze onactief zijn moet ik nog op testen.
    public function ophalen($manier = '',$zoek = '')
    {      
        if ($manier == 'id')
        {
            // de manier voor opzoeken op address aanpassen.
            $persoon= \DB::table('suppliers')->where($manier,$zoek)->get();
            if ($persoon == "[]")
            {
                $persoon = "[{\"failed\" : \"notFound\"}]";
            }
            return $persoon;
        }
        else if ($manier == 'companyname' || $manier == 'vatnumber' || $manier == 'email' || $manier == 'address' || $manier == 'phnenumber') 
        {
            $persoon= \DB::table('suppliers')->where($manier,'LIKE','%'.$zoek.'%')->get();
            if ($persoon == "[]")
            {
                $persoon = "[{\"failed\" : \"notFound\"}]";
            }
            return $persoon;
        } 

        else
        {
            $persoon= \DB::table('suppliers')->get();
            return $persoon;
        }
    }


    //Dit blokje laat toe om een bedrijf toe te voegen. Als die nog geen adres heeft wordt dit adres aangemaakt.
    //Hetzelfde geldt voor het land en de stad.
    public function store(Request $request)
    {
        //het land ophalen en uitzoeken welke id hij heeft
        $land = request('country');
        $landId = \DB::table('country')->where("name",$land)->value('id');
        if ($landId == '')
        {
            $Country = new Country();
            $Country->name = $land;
            $Country->save();
            $landId = \DB::table('country')->where("name",$land)->value('id');
        }
        
        
        //de stad opvragen en uitzoeken welke id deze heeft
        $stad = request('city');
        $stadId = \DB::table('city')->where("countryId",$landId)->where('name', $stad)->value('id');
        if ($stadId == '')
        {
            $City = new city();
            $City->name = $stad;
            $City->postalcode = request('postalcode');
            $City->countryId = $landId;
            $City->save();
            $stadId = \DB::table('city')->where("countryId",$landId)->where('name', $stad)->value('id');
        }

        //de straat en het nummer opvragen en zo kijken wat het adresid is
        $straat = request('straat');
        $number = request('nummer');
        $adresId = \DB::table('addresses')->where("cityId",$stadId)->where('street', $straat)->where('number',$number)->value('id');
        if ($adresId == '')
        {
            $adres = new address();
            $adres->street = $straat;
            $adres->number = $number;
            $adres->cityId = $stadId;
            $adres->save();
            $adresId = \DB::table('addresses')->where("cityId",$stadId)->where('street', $straat)->where('number',$number)->value('id');
        }

        // hier ga ik nakijken of het bedrijf al bestaat of nog niet.
        


        //hier maak ik een object van de klasse Supplier, ik voeg in zijn variabele de waardes toe en dan met save wordt dit opgestuurd naar 
        // de database
        $suppliers = new Supplier();

        $suppliers->companyname = request('companyname');
        $suppliers->vatnumber = request('vatnumber');
        $suppliers->email = request('email');
        $suppliers->addressId = $adresId;
        $suppliers->phonenumber = request('phonenumber');
        

        //bepalen wat moet er uniek zijn om een bedrijf te mogen toevoegen?
        $bedrijf = request('companyname');
        $btw = request('vatnumber');
        $zelfde = \DB::table('suppliers')->where('vatnumber',$btw)->pluck('id');
        $i = 0;
        while (count($zelfde) > $i)
        {
            
            //$tester = \DB::table('suppliers')->where('id',$zelfde[$i])->exists();
            if (\DB::table('suppliers')->where('vatnumber', $btw)->exists())
            {
                return "[{\"failed\" : \"vatnumber_already_exists\"}]";
            }
            $i++;
        }
        $suppliers->isSet = 1;
        $suppliers->save();
        return "[{\"success\" : \"Supplier_was_added_to_the_database\"}]";
    }

    public function verwijder(Request $request)
    {
        
    }

}
