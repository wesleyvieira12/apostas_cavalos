<?php

namespace App\Http\Controllers;

use App\Models\Apostador;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApostadorController extends Controller
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
            'nome' => 'required|string|max:255',
            'corrida_id' => 'required|exists:corridas,id',
        ]);

        $apostador = Apostador::create($validated);

        // Se for requisição AJAX, retorna JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'apostador' => [
                    'id' => $apostador->id,
                    'nome' => $apostador->nome
                ]
            ]);
        }

        if ($request->has('corrida_id')) {
            return redirect()->route('home', ['corrida_id' => $request->corrida_id])->with('success', 'Apostador criado com sucesso!');
        }

        return redirect()->back()->with('success', 'Apostador criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Apostador $apostador)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Apostador $apostador)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Apostador $apostador)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Apostador $apostador)
    {
        //
    }
}
