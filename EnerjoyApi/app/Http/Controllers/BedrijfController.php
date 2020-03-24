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

    public function ophalen($manier = '',$zoek = '')
    {     
        // zoeken op Id en address moet niet met een like gebeuren want dat geeft problemen 
        if ($manier == 'id' || $manier == 'address')
        {
            
            $persoon= \DB::table('suppliers')->where($manier,$zoek)->get();
            $isSet = \DB::table('suppliers')->where($manier,$zoek)->value('isSet');
            if ($persoon == "[]")
            {
                $persoon = "[{\"failed\" : \"notFound\"}]";
            }
            // Hier kijk ik na of de isSet op 0 staat, is dit het gevan dan stuur ik niks terug 
            //want deze leverancier is 'verwijderd'.
            else if($isSet == 0)
            {
                $persoon = "[{\"failed\" : \"notFound\"}]";
            }
            return $persoon;
        }

        // Hier zoek ik wel met een like, op deze manier kan je een deel van de naam of rekeningnummer... opgeven en zo zoeken.
        else if ($manier == 'companyname' || $manier == 'vatnumber' || $manier == 'email' || $manier == 'phnenumber') 
        {
            $persoon= \DB::table('suppliers')->where($manier,'LIKE','%'.$zoek.'%')->get();
            $isSet = \DB::table('suppliers')->where($manier,$zoek)->value('isSet');
            if ($persoon == "[]")
            {
                $persoon = "[{\"failed\" : \"notFound\"}]";
            }
            // Hier kijk ik na of de isSet op 0 staat, is dit het gevan dan stuur ik niks terug 
            //want deze leverancier is 'verwijderd'.
            else if($isSet == 0)
            {
                $persoon = "[{\"failed\" : \"notFound\"}]";
            }
            return $persoon;
        } 

        else
        {  
            //Ik geef alles terug waar de isSet waarde op 1 staat.
            $personen = \DB::table('suppliers')->where('isSet',1)->get();
            return $personen;
        }
    }


    //Dit blokje laat toe om een bedrijf toe te voegen. Als die nog geen adres heeft wordt dit adres aangemaakt.
    //Hetzelfde geldt voor het land en de stad.
    public function store(Request $request)
    {
        //het land ophalen en uitzoeken welke id hij heeft
        $land = request('country');
        if ($land != '')
        {
            $landId = \DB::table('country')->where("name",$land)->value('id');
            if ($landId == '')
            {
                //Als het land nog niet bestaat wordt het aangemaakt.
                $Country = new Country();
                $Country->name = $land;
                $Country->save();
                $landId = \DB::table('country')->where("name",$land)->value('id');
            }
            
            
            //de stad opvragen en uitzoeken welke id deze heeft
            $stad = request('city');
            if ($stad != '')
            {
                $stadId = \DB::table('city')->where("countryId",$landId)->where('name', $stad)->value('id');
                if ($stadId == '')
                {
                    //als de stad nog neit bestaat, wordt hij aangemaakt.
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
                if ($straat != '' && $number != '')
                {
                    $adresId = \DB::table('addresses')->where("cityId",$stadId)->where('street', $straat)->where('number',$number)->value('id');
                    if ($adresId == '')
                    {
                        //als het adres nog net bestaat wordt het aangemaakt.
                        $adres = new address();
                        $adres->street = $straat;
                        $adres->number = $number;
                        $adres->cityId = $stadId;
                        $adres->save();
                        $adresId = \DB::table('addresses')->where("cityId",$stadId)->where('street', $straat)->where('number',$number)->value('id');
                    }
                }
                else
                {
                    return "[{\"failed\" : \"found_no_street_or_number\"}]";
                }
            }
            else
            {
                return "[{\"failed\" : \"found_no_city\"}]";
            }
        }
        else
        {
            return "[{\"failed\" : \"found_no_country\"}]";
        }


        


        //Hier maak ik een object van de klasse Supplier, ik voeg in zijn variabele de waardes toe en dan met save wordt dit opgestuurd naar 
        // de database.
        $suppliers = new Supplier();

        $suppliers->companyname = request('companyname');
        $suppliers->vatnumber = request('vatnumber');
        $suppliers->email = request('email');
        $suppliers->addressId = $adresId;
        $suppliers->phonenumber = request('phonenumber');
        

        //Ik kijk na of het vat nummer al bestaat, is dit het geval meld ik dat dit reeds in gebruik is.
        $bedrijf = request('companyname');
        $btw = request('vatnumber');
        $zelfde = \DB::table('suppliers')->where('vatnumber',$btw)->pluck('id');
        $i = 0;
        while (count($zelfde) > $i)
        {
            
            //$tester = \DB::table('suppliers')->where('id',$zelfde[$i])->exists();
            if (\DB::table('suppliers')->where('vatnumber', $btw)->exists())
            {
                return "[{\"failed\" : \"vatnumber_already_exists\", \"isSet\" : \"1\"}]";
            }
            $i++;
        }
        $suppliers->isSet = 1;
        $suppliers->save();
        return "[{\"success\" : \"Supplier_was_added_to_the_database\"}]";
    }


    //Hiermee zet je de isSet terug op 1, dus voeg je het bedrijf opnieuw toe, dit kan op elke manier id, telefoonnr....
    public function softHerinstaleer(Request $reauest)
    {
        $id = request('id');
        \DB::table('suppliers')->where('id', $id)->update(['isSet' => 1]);
        $companyname = request('companyname');
        \DB::table('suppliers')->where('companyname', $companyname)->update(['isSet' => 1]);
        $vatnumber = request('vatnumber');
        \DB::table('suppliers')->where('vatnumber', $vatnumber)->update(['isSet' => 1]);
        $email = request('email');
        \DB::table('suppliers')->where('email', $email)->update(['isSet' => 1]);
        $addressid = request('addressid');
        \DB::table('suppliers')->where('addressId', $addressid)->update(['isSet' => 1]);
        $phonenumber = request('phonenumber');
        \DB::table('suppliers')->where('phonenumber', $phonenumber)->update(['isSet' => 1]);
    }


    //Hiermee verwijder je de leverancier, de gegevens worden bewaart, maar de isSet wordt op 0 gezet
    // zo kan je de gegevens nietmeer opvragen.
    public function softVerwijder(Request $request)
    {
        $id = request('id');
        \DB::table('suppliers')->where('id', $id)->update(['isSet' => 0]);
        $companyname = request('companyname');
        \DB::table('suppliers')->where('companyname', $companyname)->update(['isSet' => 0]);
        $vatnumber = request('vatnumber');
        \DB::table('suppliers')->where('vatnumber', $vatnumber)->update(['isSet' => 0]);
        $email = request('email');
        \DB::table('suppliers')->where('email', $email)->update(['isSet' => 0]);
        $addressid = request('addressid');
        \DB::table('suppliers')->where('addressId', $addressid)->update(['isSet' => 0]);
        $phonenumber = request('phonenumber');
        \DB::table('suppliers')->where('phonenumber', $phonenmuber)->update(['isSet' => 0]);

    }
    //Hier kan je waarde aanpassen, je kan wel maar op 1 manier tegelijkertijd zoeken, dus ofwel id, ofwel companyname...
    //Als je meerdere dingen meegeeft, zal het eerst kijken naar Id, dan companyname... Als er 1 is ingevult,
    //wordt de rest nietmeer nagekeken, als je op 2 dingen wilt aanpassen, dat zal de webdeveloper moeten doen.
    //Hij zal de id moeten zoeken via de zoek functie en dan kan hij zo aanpassen. 

    public function aanpas(Request $request)
    {
        $id = request('id');
        $companyname = request('companyname');
        $vatnumber = request('vatnumber');
        $email = request('email');
        $phonenumber = request('phonenumber');
        


        $n_Companyname = request('nieuwcompanyname');
        $n_Vatnumber = request('nieuwvatnumber');
        $n_Email = request('nieuwemail');
        $n_Phonenumber = request('nieuwphonenumber');




        //het land ophalen en uitzoeken welke id hij heeft
        $land = request('country');
        if ($land != '')
        {
            $landId = \DB::table('country')->where("name",$land)->value('id');
                      
            
            //de stad opvragen en uitzoeken welke id deze heeft
            $stad = request('city');
            if ($stad != '')
            {
                $stadId = \DB::table('city')->where("countryId",$landId)->where('name', $stad)->value('id');
                $straat = request('straat');
                $number = request('nummer');
                if ($traat != '' && number != '')
                {
                    $addressId = \DB::table('addresses')->where("cityId",$stadId)->where('street', $straat)->where('number',$number)->value('id');
                }
                else
                {
                    return "[{\"failed\" : \"found_no_street_or_number\"}]";
                }
            }
            else
            {
                return "[{\"failed\" : \"found_no_city\"}]";
            }
        }
        else
        {
            return "[{\"failed\" : \"found_no_country\"}]";
        }







        // Als het addressId moet aangepast worden, krijg ik het land, de stad, se straat en het nummer binnen.
        //Hier ga ik daarvan een addressid opzoeken in de tabel.
        $n_land = request('nieuwcountry');
        if ($n_land != '')
        {
            $n_landId = \DB::table('country')->where("name",$n_land)->value('id');
            if ($n_landId == '')
            {
                //Als het land nog niet bestaat wordt het aangemaakt.
                $Country = new Country();
                $Country->name = $n_land;
                $Country->save();
                $n_landId = \DB::table('country')->where("name",$n_land)->value('id');
            }
        
            //de stad opvragen en uitzoeken welke id deze heeft
            $n_stad = request('nieuwcity');
            if ($n_stad != '')
            {
                $n_stadId = \DB::table('city')->where("countryId",$n_landId)->where('name', $n_stad)->value('id');
                if ($n_stadId == '')
                {
                    //als de stad nog niest bestaat, wordt hij aangemaakt.
                    $City = new city();
                    $City->name = $n_stad;
                    $City->postalcode = request('postalcode');
                    $City->countryId = $n_landId;
                    $City->save();
                    $n_stadId = \DB::table('city')->where("countryId",$n_landId)->where('name', $n_stad)->value('id');
                }

                //de straat en het nummer opvragen en zo kijken wat het adresid is
                $n_straat = request('nieuwstraat');
                $n_number = request('nieuwnumber');
                if ($n_straat != '' && $n_number != '')
                {
                    $n_AddressId = \DB::table('addresses')->where("cityId",$n_stadId)->where('street', $n_straat)->where('number',$n_number)->value('id');
                    if ($n_AddressId == '')
                    {
                        //als het adres nog net bestaat wordt het aangemaakt.
                        $adres = new address();
                        $adres->street = $n_straat;
                        $adres->number = $n_number;
                        $adres->cityId = $n_stadId;
                        $adres->save();
                        $n_AddressId = \DB::table('addresses')->where("cityId",$n_stadId)->where('street', $n_straat)->where('number',$n_number)->value('id');
                    }
                }
                else
                {
                    $n_AddressId = '';
                }
            }
            else
            {
                $n_AddressId = '';
            }
        }
        else
        {
            $n_AddressId = '';
        }
        

        //Er werd steeds op iets specifieks gezocht daarvoor dient de eerste if else structuur.
        if ($id != '')
        {
            // Deze if else structuur dient om aan te passen wat aangepast moet worden, alles is if omdat je meerdere
            // dingen tegelijk kunt aanpassen.
            if ($n_Companyname != '')
            {
                \DB::table('suppliers')->where('id', $id)->update(['companyname' => $n_Companyname]);
            }

            if ($n_Vatnumber != '')
            {
                \DB::table('suppliers')->where('id', $id)->update(['vatnumber' => $n_Vatnumber]);
            }

            if ($n_Email != '')
            {
                \DB::table('suppliers')->where('id', $id)->update(['email' => $n_Email]);
            }

            if ($n_AddressId != '')
            {
                \DB::table('suppliers')->where('id', $id)->update(['addressid' => $n_AddressId]);
            }

            if ($n_Phonenumber != '')
            {
                \DB::table('suppliers')->where('id', $id)->update(['phonenumber' => $n_Phonenumber]);
            }
        }

        else if ($companyname != '')
        {
            if ($n_Companyname != '')
            {
                \DB::table('suppliers')->where('companyname', $companyname)->update(['companyname' => $n_Companyname]);
            }

            if ($n_Vatnumber != '')
            {
                \DB::table('suppliers')->where('companyname', $companyname)->update(['vatnumber' => $n_Vatnumber]);
            }

            if ($n_Email != '')
            {
                \DB::table('suppliers')->where('companyname', $companyname)->update(['email' => $n_Email]);
            }

            if ($AddressId != '')
            {
                \DB::table('suppliers')->where('companyname', $companyname)->update(['addressid' => $AddressId]);
            }

            if ($n_Phonenumber != '')
            {
                \DB::table('suppliers')->where('companyname', $companyname)->update(['phonenumber' => $n_Phonenumber]);
            }
        }

        else if ($vatnumber != '')
        {
            if ($n_Companyname != '')
            {
                \DB::table('suppliers')->where('vatnumber', $vatnumber)->update(['companyname' => $n_Companyname]);
            }

            if ($n_Vatnumber != '')
            {
                \DB::table('suppliers')->where('vatnumber', $vatnumber)->update(['vatnumber' => $n_Vatnumber]);
            }

            if ($n_Email != '')
            {
                \DB::table('suppliers')->where('vatnumber', $vatnumber)->update(['email' => $n_Email]);
            }

            if ($AddressId != '')
            {
                \DB::table('suppliers')->where('vatnumber', $vatnumber)->update(['addressid' => $AddressId]);
            }

            if ($n_Phonenumber != '')
            {
                \DB::table('suppliers')->where('vatnumber', $vatnumber)->update(['phonenumber' => $n_Phonenumber]);
            }
        }

        else if ($email != '')
        {
            if ($n_Companyname != '')
            {
                \DB::table('suppliers')->where('email', $email)->update(['companyname' => $n_Companyname]);
            }

            if ($n_Vatnumber != '')
            {
                \DB::table('suppliers')->where('email', $email)->update(['vatnumber' => $n_Vatnumber]);
            }

            if ($n_Email != '')
            {
                \DB::table('suppliers')->where('email', $email)->update(['email' => $n_Email]);
            }

            if ($AddressId != '')
            {
                \DB::table('suppliers')->where('email', $email)->update(['addressid' => $AddressId]);
            }

            if ($n_Phonenumber != '')
            {
                \DB::table('suppliers')->where('email', $email)->update(['phonenumber' => $n_Phonenumber]);
            }
        }



        //hier moet ik nog aanpassen als ze het land enzo willen veranderen. Of de straat....
        else if ($addressid != '')
        {
            if ($n_Companyname != '')
            {
                \DB::table('suppliers')->where('addressid', $addressid)->update(['companyname' => $n_Companyname]);
            }

            if ($n_Vatnumber != '')
            {
                \DB::table('suppliers')->where('addressid', $addressid)->update(['vatnumber' => $n_Vatnumber]);
            }

            if ($n_Email != '')
            {
                \DB::table('suppliers')->where('addressid', $addressid)->update(['email' => $n_Email]);
            }

            if ($AddressId != '')
            {
                \DB::table('suppliers')->where('addressid', $addressid)->update(['addressid' => $AddressId]);
            }

            if ($n_Phonenumber != '')
            {
                \DB::table('suppliers')->where('addressid', $addressid)->update(['phonenumber' => $n_Phonenumber]);
            }
        }

        else if ($phonenumber != '')
        {
            if ($n_Companyname != '')
            {
                \DB::table('suppliers')->where('phonenumber', $phonenumber)->update(['companyname' => $n_Companyname]);
            }

            if ($n_Vatnumber != '')
            {
                \DB::table('suppliers')->where('phonenumber', $phonenumber)->update(['vatnumber' => $n_Vatnumber]);
            }

            if ($n_Email != '')
            {
                \DB::table('suppliers')->where('phonenumber', $phonenumber)->update(['email' => $n_Email]);
            }

            if ($AddressId != '')
            {
                \DB::table('suppliers')->where('phonenumber', $phonenumber)->update(['addressid' => $AddressId]);
            }

            if ($n_Phonenumber != '')
            {
                \DB::table('suppliers')->where('phonenumber', $phonenumber)->update(['phonenumber' => $n_Phonenumber]);
            }
        }

    }

}
