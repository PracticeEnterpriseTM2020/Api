<?php

namespace App\Http\Controllers;
use DB;
use App\invoice;

class invoiceController extends Controller
{
    public function show($invoiceId)
    {
        //$invoice = DB::table('invoices')->where('id',$invoiceId)->first();

        //Get the invoice with the ID 'invoiceId' from the database, if none found, throw 404
        $invoice = Invoice::where('id',$invoiceId)->firstOrFail();

        //Dump and die the data on the screen
        dd($invoice);
    }
}
