<?php

return [
    'required' => ':attribute is verplicht.',
    'string' => ':attribute moet tekst zijn.',
    'confirmed' => ':attribute bevestiging is niet hetzelfde.',
    'email' => ':attribute moet een correct email adres zijn.',
    'unique' => ':attribute is al in gebruik.',
    'numeric' => ':attribute moet een getal zijn.',
    'gte' => [
        'numeric' => ':attribute moet groter zijn dan of gelijk zijn aan :value.',
        'file' => ':attribute moet groter zijn dan of gelijk zijn aan :value kilobytes.',
        'string' => ':attribute moet groter zijn dan of gelijk zijn aan :value karakters.',
        'array' => ':attribute moet :value of meer items bevatten.',
    ],
    'gt' => [
        'numeric' => ':attribute moet groter zijn dan :value.',
        'file' => ':attribute moet groter zijn dan :value kilobytes.',
        'string' => ':attribute moet groter zijn dan :value karakter.',
        'array' => ':attribute moet meer dan :value items bevatten.',
    ],
    'date' => ':attribute is geen geldige datum.',
    'before' => ':attribute moet voor :date komen.',
    'integer' => ':attribute moet een niet kommagetal zijn.',
    'in' => ':attribute moet één van de volgende types zijn: :values',
    'exists' => ':attribute bestaat niet.',

    'max' => [
        'string' => ':attribute mag niet langer zijn dan :max',
    ],

    'min' => [
        'string' => ':attribute mag niet korter zijn dan :min',
    ],

    'attributes' => [
        'first_name' => 'voornaam',
        'last_name' => 'achternaam',
        'password' => 'wachtwoord',
        'salary' => 'loon',
        'phone' => 'telefoon',
        'ssn' => 'rijksregisternummer',
        'birthdate' => 'geboortedatum',
        'street' => 'straat',
        'number' => 'huisnummer',
        'city' => 'woonplaats',
        'postalcode' => 'postcode',
        'country_id' => 'land',
        'job_id' => 'functie',
        'brand' => 'merk',
        'owner_id' => 'eigenaar',
        'job_title' => 'functie naam',
        'job_offer_title' => 'vacature titel',
        'job_offer_description' => 'vacature beschrijving',
        'creator_id' => 'eigenaar',
        'receiver_email' => 'email',
    ],
];
