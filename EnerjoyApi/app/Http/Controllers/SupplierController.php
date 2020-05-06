<?php

namespace App\Http\Controllers;
use App\Supplier;
use App\country;
use App\City;
use App\address;
use Illuminate\Http\Request;

class SupplierController extends Controller
{

    //Met deze functie kan je ofwel zoeken op apparte personen, dit kan op verschillende manieren id, companyname....
    //Als je geen variabele doorgeeft, krijg je al de waarden terug.

    public function ophalen($manier = '',$zoek = '')
    {     
        // Zodat je geen sql injectie kunt doen dit stuk.
        $manier = htmlspecialchars($manier);
        $zoek = htmlspecialchars($zoek);
        // zoeken op Id en address moet niet met een like gebeuren want dat geeft problemen 
        if ($manier == 'id')
        {
            // While lus voor maken zodat ik naar al de issets kijk, nu kijk ik nog maar naar de eerste.
            // zelfde voor de 2 codes hieronder
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
        else if ($manier == 'companyname' || $manier == 'vatnumber' || $manier == 'email' || $manier == 'phonenumber') 
        {
            
            // Hier while lus maken
            $persoon= \DB::table('suppliers')->where($manier,'LIKE','%'.$zoek.'%')->where('isset',1)->get();
            
            $isSet = \DB::table('suppliers')->where($manier,'LIKE','%'.$zoek.'%')->value('isSet');
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

        else if ($manier == 'addressId')
        {
            $address = \DB::table('addresses')->where('id',$zoek)->get();
            $cityId = \DB::table('addresses')->where('id',$zoek)->value('cityId');
            $address .=  \DB::table('city')->where('id',$cityId)->get();
            $countryId = \DB::table('city')->where('id',$cityId)->value('countryId');
            $address .=  \DB::table('countries')->where('id',$countryId)->get();
            return $address;
        }
        else if ($manier == '' && $zoek == '')
        {  
            //Ik geef alles terug waar de isSet waarde op 1 staat.
            $personen = \DB::table('suppliers')->where('isSet',1)->get();
            return $personen;
        }

        else
        {
            return "[{\"failed\" : \"wrong_values\"}]";
        }
    }


    //Dit blokje laat toe om een bedrijf toe te voegen. Als die nog geen adres heeft wordt dit adres aangemaakt.
    //Hetzelfde geldt voor het land en de stad.
    public function store(Request $request)
    {
        //het land ophalen en uitzoeken welke id hij heeft
        $land = htmlspecialchars(request('country'));

        // Met deze pregmatch kijk ik via reguliere expressie na of het de juiste syntax heeft.
        if ($land != '' && !preg_match("/[^A-Za-z\s]/", $land))
        {
            // Als het land bestaar, wordt de id opgehaald.
            $landId = \DB::table('countries')->where("name",$land)->value('id');
            if ($landId == '')
            {
                //Als het land nog niet bestaat wordt het aangemaakt.
                $Country = new Country();
                $Country->name = $land;
                $Country->save();
                $landId = \DB::table('countries')->where("name",$land)->value('id');
            }
            
            
            //de stad opvragen en uitzoeken welke id deze heeft
            $stad = htmlspecialchars(request('city'));
            if ($stad != '' && !preg_match("/[^A-Za-z\s]/", $stad))
            {
                $postcode = htmlspecialchars(request('postalcode'));
                $stadId = \DB::table('city')->where("countryId",$landId)->where('name', $stad)->where('postalcode',$postcode)->value('id');
                if ($stadId == '')
                {
                    
                    //als de stad nog neit bestaat, wordt hij aangemaakt.
                    $City = new city();
                    $City->name = $stad;
                
                    
                    if ($postcode == '' || preg_match("/[\D]/",$postcode))
                    {
                        return "[{\"failed\" : \"no_good_postalcode\"}]";
                    }
                    $City->postalcode = $postcode;
                    $City->countryId = $landId;
                    $City->save();
                    $stadId = \DB::table('city')->where("countryId",$landId)->where('name', $stad)->value('id');
                }

                //de straat en het nummer opvragen en zo kijken wat het adresid is
                $straat= htmlspecialchars(request('straat'));
                $number = htmlspecialchars(request('nummer'));
                if ($straat != '' && $number != '' && !preg_match("/[^A-Za-z\s]/", $straat) && preg_match("/[0-9]+[a-zA-Z]?\b/",$number))
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
                    // Hier kijk ik na als er niets was ingegeven, geef ik terug dat ik
                    // waardes wil voor (dus voor straat of nummer) 
                    // Als er geen goede syntax was ingegeven geef ik dit terug. Zo weet de 
                    // gebruiker wat hij fout heeft gedaan.
                    if ($straat == '')
                    {
                        return "[{\"failed\" : \"found_no_street\"}]";
                    }
                    elseif ($number == '')
                    {
                        return "[{\"failed\" : \"found_no_number\"}]";
                    }
                    else
                    {
                        return "[{\"failed\" : \"no_good_values_for_street_or_number\"}]";
                    }
                    
                }
            }
            
            else
            {
                if ($stad == '')
                {
                    return "[{\"failed\" : \"found_no_city\"}]";
                }
                else
                {
                    return "[{\"failed\" : \"(".$stad.") is no city\"}]";
                }
            }
        }
        else
        {
            if ($land == '')
            {
                return "[{\"failed\" : \"found_no_country\"}]";
            }
            else
            {
                return "[{\"failed\" : \"(".$land.") is no country\"}]";
            }
        }


        


        //Hier maak ik een object van de klasse Supplier, ik voeg in zijn variabele de waardes toe en dan met save wordt dit opgestuurd naar 
        // de database.
        $suppliers = new Supplier();

        

        // Alles steeds nakijken of het de juiste syntax heeft.


        $suppliers->companyname = htmlspecialchars(request('companyname'));
        if ($suppliers->companyname == '' || preg_match("/[^A-Za-z\s]/", $suppliers->companyname))
        {
            return "[{\"failed\" : \"(".$suppliers->companyname.") is no company\"}]";
        }

        $suppliers->vatnumber = htmlspecialchars(request('vatnumber'));
        if ($suppliers->vatnumber != '' && preg_match("/\A[A-Z]{2}[A-Za-z0-9]{2,13}\b/", $suppliers->vatnumber))
        {
            
        }
        else
        {
            return "[{\"failed\" : \"(".$suppliers->vatnumber.") is no valit vatnumber\"}]";
        }

        // Email adressen moeten nu volgens een vast stramien zijn.
        $suppliers->email = htmlspecialchars(request('email'));
        if ($suppliers->email != '' && preg_match("/^[A-Za-z0-9!#$%&'*+\/=?^_`{|}~-]+\.?[A-Za-z0-9!#$%&'*+\/=?^_`{|}~-]+@((\[\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})+|[A-Za-z0-9]+\.[a-z]{2,3})\b/", $suppliers->email))
        {
            
        }
        else
        {
            return "[{\"failed\" : \"(".$suppliers->email.") is no email\"}]";
        }

        


        $suppliers->addressId = $adresId;


        $suppliers->phonenumber = htmlspecialchars(request('phonenumber'));
        if ($suppliers->phonenumber != '' && preg_match("/\d{5,15}/", $suppliers->phonenumber))
        {
           
        }
        else
        {
            return "[{\"failed\" : \"(".$suppliers->phonenumber.") is no phonenumber\"}]";
        }
        



        // Ik kijk na of het vat nummer al bestaat, is dit het geval meld ik dat dit reeds in gebruik is.
        // Tenzij deze supplier was verwijderd, dan kan het wel worden gebruikt.
        $bedrijf = request('companyname');
        $btw = request('vatnumber');
        $zelfde = \DB::table('suppliers')->where('vatnumber',$btw)->pluck('id');
        $i = 0;
        while (count($zelfde) > $i)
        {
            $ongebruikt = \DB::table('suppliers')->where('id', $zelfde[$i])->value('isset');
            if ($ongebruikt != 0)
            {
                return "[{\"failed\" : \"vatnumber_already_exists\"]";
            }
            $i++;
        }
        $suppliers->isSet = 1;
        
        if (\DB::table('suppliers')->where('companyname', $suppliers->companyname)->where('Vatnumber',$suppliers->vatnumber)->where('email', $suppliers->email)->where('addressId',$suppliers->addressId)->where('phonenumber',$suppliers->phonenumber)->exists())
        {
            \DB::table('suppliers')->where('companyname', $suppliers->companyname)->where('Vatnumber',$suppliers->vatnumber)->where('email',$suppliers->email)->where('addressId',$suppliers->addressId)->where('phonenumber',$suppliers->phonenumber)->update(['isSet' => 1]);
        }
        else
        {
            $suppliers->save();
        }
        return "[{\"success\" : \"Supplier_was_added_to_the_database\"}]";
    }


    //Hiermee zet je de isSet terug op 1, dus voeg je het bedrijf opnieuw toe, dit kan op elke manier id, telefoonnr....
    public function softHerinstaleer(Request $reauest)
    {
        // Ik moet overal ook controleren of dit btw nummer niet is gebruikt voor een ander bedrijf.
        $id = htmlspecialchars(request('id'));
        $companyname = htmlspecialchars(request('companyname'));
        $email = htmlspecialchars(request('email'));
        $phonenumber = htmlspecialchars(request('phonenumber'));

        if ($id != '')
        {
            $btw = \DB::table('suppliers')->where('id', $id)->value('Vatnumber');
            $zelfde = \DB::table('suppliers')->where('vatnumber',$btw)->pluck('id');
            $i = 0;
            
            while (count($zelfde) > $i)
            {
                $ongebruikt = \DB::table('suppliers')->where('id', $zelfde[$i])->value('isset');
                if ($ongebruikt != 0)
                {
                    return "[{\"failed\" : \"vatnumber_was_used_for_another_company\"]";
                }
                $i++;
            }
            \DB::table('suppliers')->where('id', $id)->update(['isSet' => 1]);
        }

        // Als er 1 btw nummer van de bedrijven al in gebruik is zal niets worden geherinstalleert. 
        
        else if ($companyname != '')
        {
            $btw = \DB::table('suppliers')->where('companyname', $companyname)->pluck('Vatnumber');
            $k = 0;
            while (count($btw) > $k)
            {
                $zelfde = \DB::table('suppliers')->where('vatnumber',$btw[$k])->where('companyname','!=',$companyname)->pluck('id');
                $juisteid = \DB::table('suppliers')->where('vatnumber',$btw[$k])->where('companyname',$companyname)->value('id');
                $i = 0;
                if (count($zelfde) == 0)
                {
                    \DB::table('suppliers')->where('id', $juisteid)->update(['isSet' => 1]);
                }
                else
                {
                    while (count($zelfde) > $i)
                    {
                        $ongebruikt = \DB::table('suppliers')->where('id', $zelfde[$i])->value('isset');
                        if ($ongebruikt == 0)
                        {
                            \DB::table('suppliers')->where('id', $juisteid)->update(['isSet' => 1]);
                        }
                        $i++;
                    }
                }
                $k++;
            }
        }
        
        
        
        else if ($email != '')
        {
            $btw = \DB::table('suppliers')->where('email', $email)->pluck('Vatnumber');
            $k = 0;
            while (count($btw) > $k)
            {
                $zelfde = \DB::table('suppliers')->where('vatnumber',$btw[$k])->where('email','!=',$email)->pluck('id');
                $juisteid = \DB::table('suppliers')->where('vatnumber',$btw[$k])->where('email',$email)->value('id');
                $i = 0;
                if (count($zelfde) == 0)
                {
                    \DB::table('suppliers')->where('id', $juisteid)->update(['isSet' => 1]);
                }
                else
                {
                    while (count($zelfde) > $i)
                    {
                        $ongebruikt = \DB::table('suppliers')->where('id', $zelfde[$i])->value('isset');
                        if ($ongebruikt == 0)
                        {
                            \DB::table('suppliers')->where('id', $juisteid)->update(['isSet' => 1]);
                        }
                        $i++;
                    }
                }
                $k++;
            }
        }
        
        
        else if ($phonenumber != '')
        {
            $btw = \DB::table('suppliers')->where('phonenumber', $phonenumber)->pluck('Vatnumber');
            $k = 0;
            while (count($btw) > $k)
            {
                $zelfde = \DB::table('suppliers')->where('vatnumber',$btw[$k])->where('phonenumber','!=',$phonenumber)->pluck('id');
                $juisteid = \DB::table('suppliers')->where('vatnumber',$btw[$k])->where('phonenumber',$phonenumber)->value('id');
                $i = 0;
                if (count($zelfde) == 0)
                {
                    \DB::table('suppliers')->where('id', $juisteid)->update(['isSet' => 1]);
                }
                else
                {
                    while (count($zelfde) > $i)
                    {
                        $ongebruikt = \DB::table('suppliers')->where('id', $zelfde[$i])->value('isset');
                        if ($ongebruikt == 0)
                        {
                            \DB::table('suppliers')->where('id', $juisteid)->update(['isSet' => 1]);
                        }
                        $i++;
                    }
                }
                $k++;
            }
        }
        return "[{\"success\" : \"Supplier_was_reinstalled\"}]";
    }


    //Hiermee verwijder je de leverancier, de gegevens worden bewaart, maar de isSet wordt op 0 gezet
    // zo kan je de gegevens nietmeer opvragen.
    public function softVerwijder(Request $request)
    {
        
        $id = htmlspecialchars(request('id'));
        if ($id != '')
        {
            \DB::table('suppliers')->where('id', $id)->update(['isSet' => 0]);
        }
        
        $companyname = htmlspecialchars(request('companyname'));
        if ($companyname != '')
        {
            \DB::table('suppliers')->where('companyname', $companyname)->update(['isSet' => 0]);
        }
        
        $vatnumber = htmlspecialchars(request('vatnumber'));
        if ($vatnumber != '')
        {
            \DB::table('suppliers')->where('vatnumber', $vatnumber)->update(['isSet' => 0]);
        }
        
        $email = htmlspecialchars(request('email'));
        if ($email != '')
        {
            \DB::table('suppliers')->where('email', $email)->update(['isSet' => 0]);
        }
        
        $phonenumber = htmlspecialchars(request('phonenumber'));
        if ($phonenumber != '')
        {
            \DB::table('suppliers')->where('phonenumber', $phonenumber)->update(['isSet' => 0]);
        }
        return "[{\"success\" : \"Supplier_was_deleted\"}]";
    }


    //Hier kan je waarde aanpassen, je kan wel maar op 1 manier tegelijkertijd zoeken, dus ofwel id, ofwel companyname...
    //Als je meerdere dingen meegeeft, zal het eerst kijken naar Id, dan companyname... Als er 1 is ingevult,
    //wordt de rest nietmeer nagekeken, als je op 2 dingen wilt aanpassen, dat zal de webdeveloper moeten doen.
    //Hij zal de id moeten zoeken via de zoek functie en dan kan hij zo aanpassen. 

    public function aanpas(Request $request)
    {

        $id = htmlspecialchars(request('id'));
        if (preg_match("/\D+/",$id))
        {
            return "[{\"failed\" : \"(".$id.") is no ID\"}]";
        }

        $companyname = htmlspecialchars(request('companyname'));
        if (preg_match("/[^A-Za-z\s]/", $companyname))
        {
            return "[{\"failed\" : \"(".$companyname.") is no company\"}]";
        }

        $vatnumber = htmlspecialchars(request('vatnumber'));
        if (!preg_match("/\A[A-Z]{2}[A-Za-z0-9]{2,13}\b/", $vatnumber) && $vatnumber != '')
        {
            return "[{\"failed\" : \"(".$vatnumber.") is no valit vatnumber\"}]";
        }

       
        $email = htmlspecialchars(request('email'));
        if (!preg_match("/^[A-Za-z0-9!#$%&'*+\/=?^_`{|}~-]+\.?[A-Za-z0-9!#$%&'*+\/=?^_`{|}~-]+@((\[\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})+|[A-Za-z0-9]+\.[a-z]{2,3})\b/", $email) && $email != '')
        {
            return "[{\"failed\" : \"(".$email.") is no email\"}]";
        }


        $phonenumber = htmlspecialchars(request('phonenumber'));
        if (!preg_match("/\d{5,15}/", $phonenumber) && $phonenumber != '')
        {
            return "[{\"failed\" : \"(".$phonenumber.") is no phonenumber\"}]";
        }



        
        
        


        
       
        // Hier komen de nieuwe variabelen.

        $n_Companyname = htmlspecialchars(request('nieuwcompanyname'));
        if (preg_match("/[^A-Za-z\s]/", $n_Companyname))
        {
            return "[{\"failed\" : \"(".$n_Companyname.") is no company\"}]";
        }

        $n_Vatnumber = htmlspecialchars(request('nieuwvatnumber'));
        if (!preg_match("/\A[A-Z]{2}[A-Za-z0-9]{2,13}\b/", $n_Vatnumber) && $n_Vatnumber != '')
        {
            return "[{\"failed\" : \"(".$n_Vatnumber.") is no valit vatnumber\"}]";
        }

        $n_Email = htmlspecialchars(request('nieuwemail'));
        if (!preg_match("/^[A-Za-z0-9!#$%&'*+\/=?^_`{|}~-]+\.?[A-Za-z0-9!#$%&'*+\/=?^_`{|}~-]+@((\[\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})+|[A-Za-z0-9]+\.[a-z]{2,3})\b/", $n_Email) && $n_Email != '')
        {
            return "[{\"failed\" : \"(".$n_Email.") is no email\"}]";
        }

        $n_Phonenumber = htmlspecialchars(request('nieuwphonenumber'));
        if (preg_match("/\d{5,15}/", $n_Phonenumber) && $phonenumber != '')
        {
            return "[{\"failed\" : \"(".$n_Phonenumber.") is no phonenumber\"}]";
        }



        //het land ophalen en uitzoeken welke id hij heeft
        $land = htmlspecialchars(request('country'));
        if ($land != '')
        {
            $landId = \DB::table('countries')->where("name",$land)->value('id');
            $stad = htmlspecialchars(request('city'));
            if ($stad != '')
            {
                $stadId = \DB::table('city')->where("countryId",$landId)->where('name', $stad)->value('id');
                $straat = htmlspecialchars(request('straat'));
                $number = htmlspecialchars(request('nummer'));
                if ($traat != '' && number != '')
                {
                    $AddressId = \DB::table('addresses')->where("cityId",$stadId)->where('street', $straat)->where('number',$number)->value('id');
                }
                else
                {
                    $AddressId = '';
                }
            }
            else
            {
                $AddressId = '';
            }
        }
        else
        {
            $AddressId = '';
        }







        // Als het addressId moet aangepast worden, krijg ik het land, de stad, de straat en het nummer binnen.
        //Hier ga ik daarvan een addressid opzoeken in de tabel.
        $n_land = htmlspecialchars(request('nieuwcountry'));
        if ($n_land != '' && !preg_match("/[^A-Za-z\s]/", $n_land))
        {
            $n_landId = \DB::table('countries')->where("name",$n_land)->value('id');
            if ($n_landId == '')
            {
                //Als het land nog niet bestaat wordt het aangemaakt.
                $Country = new Country();
                $Country->name = $n_land;
                $Country->save();
                $n_landId = \DB::table('countries')->where("name",$n_land)->value('id');
            }
        
            //de stad opvragen en uitzoeken welke id deze heeft
            $n_stad = htmlspecialchars(request('nieuwcity'));
            if ($n_stad != '' && !preg_match("/[^A-Za-z\s]/", $n_stad))
            {
                $n_stadId = \DB::table('city')->where("countryId",$n_landId)->where('name', $n_stad)->value('id');
                if ($n_stadId == '')
                {
                    //als de stad nog niest bestaat, wordt hij aangemaakt.
                    $City = new city();
                    $City->name = $n_stad;
                    $postcode = htmlspecialchars(request('postalcode'));
                    if ($postcode == '' || preg_match("/[\D]/", $postcode))
                    {
                        return "[{\"failed\" : \"(".$postcode.") is no postalcode\"}]";
                    }
                    $City->postalcode = $postcode;
                    $City->countryId = $n_landId;
                    $City->save();
                    $n_stadId = \DB::table('city')->where("countryId",$n_landId)->where('name', $n_stad)->value('id');
                }

                //de straat en het nummer opvragen en zo kijken wat het adresid is
                $n_straat = htmlspecialchars(request('nieuwstraat'));
                $n_number = htmlspecialchars(request('nieuwnumber'));
                if ($n_straat != '' && $n_number != '' && !preg_match("/[^A-Za-z\s]/", $n_straat) && preg_match("/[0-9]+[a-zA-Z]?/",$n_number))
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
                    if (preg_match("/[^A-Za-z\s]/", $n_straat))
                    {
                        return "[{\"failed\" : \"(".$n_straat.") is no street\"}]";
                    }
                    elseif (!preg_match("/\b[0-9]+[a-zA-Z]?/",$n_number))
                    {
                        return "[{\"failed\" : \"(".$n_number.") is no number\"}]";
                    }
                    else
                    {
                        $n_AddressId = '';
                    }
                }
            }
            else
            {
                if (preg_match("/[^A-Za-z\s]/", $n_stad))
                {
                    return "[{\"failed\" : \"(".$n_stad.") is no city\"}]";
                }
                else
                {
                    $n_AddressId = '';
                }
            }
        }
        else
        {
            if (preg_match("/[^A-Za-z\s]/", $n_land))
            {
                return "[{\"failed\" : \"(".$n_land.") is no country\"}]";
            }
            else
            {
                $n_AddressId = '';
            }
        }
        

        //Er werd steeds op iets specifieks gezocht daarvoor dient de eerste if else structuur.
        if ($id != '')
        {

            $zelfde = \DB::table('suppliers')->where('vatnumber',$n_Vatnumber)->pluck('id');
                $i = 0;
                while (count($zelfde) > $i)
                {
                    if (\DB::table('suppliers')->where('vatnumber', $n_Vatnumber)->exists())
                    {
                        $ongebruikt = \DB::table('suppliers')->where('id', $zelfde[$i])->value('isset');
                        if ($ongebruikt != 0)
                        {
                            return "[{\"failed\" : \"vatnumber_already_exists\"]";
                        }
                    }
                    $i++;
                }
                

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
            \DB::table('suppliers')->where('id', $id)->update(['isset' => 1]);
            return "[{\"success\" : \"changes are done\"}]";
        }

        else if ($companyname != '')
        {
            $zelfde = \DB::table('suppliers')->where('vatnumber',$n_Vatnumber)->pluck('id');
            $i = 0;
            
            while (count($zelfde) > $i)
            {
                if (\DB::table('suppliers')->where('vatnumber', $n_Vatnumber)->exists())
                {
                    $ongebruikt = \DB::table('suppliers')->where('id', $zelfde[$i])->value('isset');
                    if ($ongebruikt != 0)
                    {
                        return "[{\"failed\" : \"vatnumber_already_exists\"]";
                    }
                }
                $i++;
            }


            if ($n_Vatnumber != '')
            {
                \DB::table('suppliers')->where('companyname', $companyname)->update(['Vatnumber' => $n_Vatnumber]); 
            }
            if ($n_Email != '')
            {
                \DB::table('suppliers')->where('companyname', $companyname)->update(['email' => $n_Email]);
            }
            if ($n_AddressId != '')
            {
                \DB::table('suppliers')->where('companyname', $companyname)->update(['addressid' => $n_AddressId]);
            }
            if ($n_Phonenumber != '')
            {
                \DB::table('suppliers')->where('companyname', $companyname)->update(['phonenumber' => $n_Phonenumber]);
            }
            if ($n_Companyname != '')
            {
                \DB::table('suppliers')->where('companyname', $companyname)->update(['Companyname' => $n_Companyname]);
                
            }
            return "[{\"success\" : \"changes are done\"}]";
        }

        else if ($vatnumber != '')
        {
            $zelfde = \DB::table('suppliers')->where('vatnumber',$n_Vatnumber)->pluck('id');
            $i = 0;
            while (count($zelfde) > $i)
            {
                if (\DB::table('suppliers')->where('vatnumber', $n_Vatnumber)->exists())
                {
                    $ongebruikt = \DB::table('suppliers')->where('id', $zelfde[$i])->value('isset');
                    if ($ongebruikt != 0)
                    {
                        return "[{\"failed\" : \"vatnumber_already_exists\"]";
                    }
                }
                $i++;
            }


            if ($n_Companyname != '')
            {
                \DB::table('suppliers')->where('vatnumber', $vatnumber)->update(['companyname' => $n_Companyname]);
            }

            if ($n_Email != '')
            {
                \DB::table('suppliers')->where('vatnumber', $vatnumber)->update(['email' => $n_Email]);
            }

            if ($n_AddressId != '')
            {
                \DB::table('suppliers')->where('vatnumber', $vatnumber)->update(['addressid' => $n_AddressId]);
            }

            if ($n_Phonenumber != '')
            {
                \DB::table('suppliers')->where('vatnumber', $vatnumber)->update(['phonenumber' => $n_Phonenumber]);
            }

            if ($n_Vatnumber != '')
            {
                \DB::table('suppliers')->where('vatnumber', $vatnumber)->update(['vatnumber' => $n_Vatnumber]);
            }
            return "[{\"success\" : \"changes are done\"}]";
        }

        else if ($email != '')
        {
            $zelfde = \DB::table('suppliers')->where('vatnumber',$n_Vatnumber)->pluck('id');
            $i = 0;
            while (count($zelfde) > $i)
            {
                if (\DB::table('suppliers')->where('vatnumber', $n_Vatnumber)->exists())
                {
                    $ongebruikt = \DB::table('suppliers')->where('id', $zelfde[$i])->value('isset');
                    if ($ongebruikt != 0)
                    {
                        return "[{\"failed\" : \"vatnumber_already_exists\"]";
                    }
                }
                $i++;
            }


            if ($n_Companyname != '')
            {
                \DB::table('suppliers')->where('email', $email)->update(['companyname' => $n_Companyname]);
            }

            if ($n_Vatnumber != '')
            {
                \DB::table('suppliers')->where('email', $email)->update(['vatnumber' => $n_Vatnumber]);
            }

            if ($n_AddressId != '')
            {
                \DB::table('suppliers')->where('email', $email)->update(['addressid' => $n_AddressId]);
            }

            if ($n_Phonenumber != '')
            {
                \DB::table('suppliers')->where('email', $email)->update(['phonenumber' => $n_Phonenumber]);
            }

            if ($n_Email != '')
            {
                \DB::table('suppliers')->where('email', $email)->update(['email' => $n_Email]);
            }
            return "[{\"success\" : \"changes are done\"}]";
        }



        //hier moet ik nog aanpassen als ze het land enzo willen veranderen. Of de straat....
        else if ($Addressid != '')
        {
            $zelfde = \DB::table('suppliers')->where('vatnumber',$n_Vatnumber)->pluck('id');
            $i = 0;
            while (count($zelfde) > $i)
            {
                if (\DB::table('suppliers')->where('vatnumber', $n_Vatnumber)->exists())
                {
                    $ongebruikt = \DB::table('suppliers')->where('id', $zelfde[$i])->value('isset');
                    if ($ongebruikt != 0)
                    {
                        return "[{\"failed\" : \"vatnumber_already_exists\"]";
                    }
                }
                $i++;
            }


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

            if ($n_Phonenumber != '')
            {
                \DB::table('suppliers')->where('addressid', $addressid)->update(['phonenumber' => $n_Phonenumber]);
            }

            if ($n_AddressId != '')
            {
                \DB::table('suppliers')->where('addressid', $addressid)->update(['addressid' => $n_AddressId]);
            }
            return "[{\"success\" : \"changes are done\"}]";
        }

        else if ($phonenumber != '')
        {
            $zelfde = \DB::table('suppliers')->where('vatnumber',$n_Vatnumber)->pluck('id');
            $i = 0;
            while (count($zelfde) > $i)
            {
                if (\DB::table('suppliers')->where('vatnumber', $n_Vatnumber)->exists())
                {
                    $ongebruikt = \DB::table('suppliers')->where('id', $zelfde[$i])->value('isset');
                    if ($ongebruikt != 0)
                    {
                        return "[{\"failed\" : \"vatnumber_already_exists\"]";
                    }
                }
                $i++;
            }


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

            if ($n_AddressId != '')
            {
                \DB::table('suppliers')->where('phonenumber', $phonenumber)->update(['addressid' => $n_AddressId]);
            }

            if ($n_Phonenumber != '')
            {
                \DB::table('suppliers')->where('phonenumber', $phonenumber)->update(['phonenumber' => $n_Phonenumber]);
            }
            return "[{\"success\" : \"changes are done\"}]";
        }

    }

}