<?php

namespace App\Http\Controllers;

use App\Models\Aposta;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApostaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'apostador_id' => 'required|exists:apostadores,id',
            'corrida_id' => 'required|exists:corridas,id',
            'rodada' => 'required|integer|min:1',
            'animal' => 'required|integer|min:1',
            'valor' => 'required|numeric|min:0.01',
            'lo' => 'nullable|numeric|min:0',
        ]);

        Aposta::create($validated);

        return redirect()->route('home', ['corrida_id' => $validated['corrida_id']])->with('success', 'Aposta criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Aposta $aposta)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Aposta $aposta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Aposta $aposta)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aposta $aposta)
    {
        //
    }
}
