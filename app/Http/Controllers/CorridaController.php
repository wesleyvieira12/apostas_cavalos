<?php

namespace App\Http\Controllers;

use App\Models\Corrida;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CorridaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $corridaId = $request->get('corrida_id');
        
        $corridas = Corrida::when($search, function ($query, $search) {
            return $query->where('nome', 'like', '%' . $search . '%');
        })
        ->orderBy('data', 'desc')
        ->paginate(10);

        $corridaSelecionada = null;
        $apostadores = collect();
        
        if ($corridaId) {
            $corridaSelecionada = Corrida::with(['apostas.apostador'])->find($corridaId);
            $apostadores = $corridaSelecionada->apostadores()->orderBy('nome')->get();
        }

        return view('welcome', compact('corridas', 'search', 'corridaSelecionada', 'apostadores'));
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
            'data' => 'required|date',
            'taxa' => 'required|numeric|min:0|max:100',
        ]);

        Corrida::create($validated);

        return redirect()->route('home')->with('success', 'Corrida criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Corrida $corrida)
    {
        $corrida->load(['apostas.apostador']);
        $apostadores = \App\Models\Apostador::orderBy('nome')->get();
        return view('corridas.show', compact('corrida', 'apostadores'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Corrida $corrida)
    {
        // A edição é feita via modal na página principal
        return redirect()->route('home');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Corrida $corrida)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'data' => 'required|date',
            'taxa' => 'required|numeric|min:0|max:100',
        ]);

        $corrida->update($validated);

        return redirect()->route('home')->with('success', 'Corrida atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Corrida $corrida)
    {
        $corrida->delete();
        return redirect()->route('home')->with('success', 'Corrida deletada com sucesso!');
    }
}
