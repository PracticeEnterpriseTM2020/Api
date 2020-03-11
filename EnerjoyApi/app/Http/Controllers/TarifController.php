<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tarif;

class TarifController extends Controller
{

    public function index()
    {
        return Tarif::all();
    }

    public function show($tarifid)
    {
        return Tarif::find($tarifid);
    }

    public function store(Request $request)
    {
        $tarif = Tarif::create($request->all());

        return response()->json($tarif, 201);
    }

    public function update(Request $request, Tarif $tarif)
    {
        $tarif->update($request->all());

        return response()->json($tarif, 200);
    }

    public function delete($tarifid)
    {
        $tarif = Tarif::findOrFail($tarifid);
        $tarif->delete();

        return 204;



    }
}
