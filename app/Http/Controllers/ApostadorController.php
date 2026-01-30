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
     * Busca apostadores por corrida (e opcionalmente por termo no nome). Retorna JSON para o select.
     */
    public function buscar(Request $request)
    {
        $request->validate([
            'corrida_id' => 'required|exists:corridas,id',
            'search' => 'nullable|string|max:255',
        ]);

        $apostadores = Apostador::where('corrida_id', $request->corrida_id)
            ->when($request->filled('search'), function ($query) use ($request) {
                $termo = $request->search;
                $query->where('nome', 'like', '%' . $termo . '%');
            })
            ->orderBy('nome')
            ->get(['id', 'nome']);

        return response()->json($apostadores);
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

        $jaExiste = Apostador::where('nome', $validated['nome'])->where('corrida_id', $validated['corrida_id'])->first();
        if ($jaExiste) {
            $mensagem = 'Já existe um apostador com esse nome nesta corrida.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $mensagem], 422);
            }
            return redirect()->back()->with('error', $mensagem);
        }

        $apostador = Apostador::create($validated);

        // Se for requisição AJAX, retorna JSON
        if ($request->ajax() || $request->wantsJson()) {
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
