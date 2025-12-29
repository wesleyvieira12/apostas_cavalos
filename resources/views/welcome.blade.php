<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
        @endif
    </head>
    <body class="bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="container mx-auto px-4 py-8 max-w-7xl">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Sistema de Apostas em Corridas</h1>
                <p class="text-gray-600 dark:text-gray-400">Gerencie corridas, apostadores e apostas</p>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(!$corridaSelecionada)
                <!-- Lista de Corridas -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <!-- Barra de busca e botão cadastrar -->
                    <div class="flex flex-col md:flex-row gap-4 mb-6">
                        <form method="GET" action="{{ route('home') }}" class="flex-1">
                            <div class="relative">
                                <input 
                                    type="text" 
                                    name="search" 
                                    value="{{ $search }}" 
                                    placeholder="Buscar corridas por nome..." 
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                >
                                <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </form>
                        <button 
                            onclick="abrirModalNovaCorrida()"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors"
                        >
                            + Nova Corrida
                        </button>
                    </div>

                    <!-- Lista de corridas -->
                    @if($corridas->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nome</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Taxa (%)</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($corridas as $corrida)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $corrida->nome }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $corrida->data->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ number_format($corrida->taxa, 2, ',', '.') }}%
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <a 
                                                    href="{{ route('home', ['corrida_id' => $corrida->id]) }}" 
                                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 font-medium"
                                                >
                                                    Entrar
                                                </a>
                                                <span class="mx-2">|</span>
                                                <a 
                                                    href="#"
                                                    class="btn-editar-corrida text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 font-medium"
                                                    data-corrida-id="{{ $corrida->id }}"
                                                    data-corrida-nome="{{ $corrida->nome }}"
                                                    data-corrida-data="{{ $corrida->data->format('Y-m-d\TH:i') }}"
                                                    data-corrida-taxa="{{ $corrida->taxa }}"
                                                >
                                                    Editar
                                                </a>
                                                <span class="mx-2">|</span>
                                                <form id="delete-form-{{ $corrida->id }}" action="{{ route('corridas.destroy', $corrida->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                                <a 
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 font-medium cursor-pointer"
                                                    onclick="event.preventDefault(); if (confirm('Tem certeza que deseja deletar esta corrida?')) { document.getElementById('delete-form-{{ $corrida->id }}').submit(); }"
                                                >
                                                    Deletar
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        <div class="mt-6">
                            {{ $corridas->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-gray-500 dark:text-gray-400">Nenhuma corrida encontrada.</p>
                        </div>
                    @endif
                </div>
            @else
                <!-- Detalhes da Corrida Selecionada -->
                <div class="space-y-6">
                    <!-- Header da corrida -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $corridaSelecionada->nome }}</h2>
                                <p class="text-gray-600 dark:text-gray-400 mt-1">
                                    Data: {{ $corridaSelecionada->data->format('d/m/Y H:i') }} | 
                                    Taxa: {{ number_format($corridaSelecionada->taxa, 2, ',', '.') }}%
                                </p>
                            </div>
                            <div class="flex gap-2">
                                @if($corridaSelecionada->apostas->count() > 0)
                                    <a 
                                        href="{{ route('relatorios.imprimir.todos', $corridaSelecionada->id) }}" 
                                        target="_blank"
                                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                        </svg>
                                        Imprimir Todos
                                    </a>
                                @endif
                                <a 
                                    href="{{ route('home') }}" 
                                    class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors"
                                >
                                    Voltar
                                </a>
                            </div>
                        </div>
                    </div>

                    

                    <!-- Seção de Relatórios -->
                    @if($corridaSelecionada->apostas->count() > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Relatórios e Impressões</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @php
                                    $apostadoresUnicos = $corridaSelecionada->apostas->map(function($aposta) {
                                        return $aposta->apostador;
                                    })->unique('id')->values();
                                @endphp
                                @foreach($apostadoresUnicos as $apostador)
                                    <a 
                                        href="{{ route('relatorios.imprimir.apostador', ['corridaId' => $corridaSelecionada->id, 'apostadorId' => $apostador->id]) }}" 
                                        target="_blank"
                                        class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 border border-blue-200 dark:border-blue-700 rounded-lg hover:shadow-md transition-all"
                                    >
                                        <div class="flex items-center gap-3">
                                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $apostador->nome }}</span>
                                        </div>
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Lista de Apostas -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Apostas</h3>
                            <button 
                                onclick="abrirModalNovaAposta()"
                                class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors"
                            >
                                + Nova Aposta
                            </button>
                        </div>
                        @if($corridaSelecionada->apostas->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Apostador</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rodada</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Animal</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Valor</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">LO</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($corridaSelecionada->apostas as $aposta)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $aposta->apostador->nome }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $aposta->rodada }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $aposta->animal }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    R$ {{ number_format($aposta->valor, 2, ',', '.') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    R$ {{ number_format($aposta->lo, 2, ',', '.') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <a 
                                                        href="#"
                                                        class="btn-editar-aposta text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 font-medium"
                                                        data-aposta-id="{{ $aposta->id }}"
                                                        data-aposta-apostador-id="{{ $aposta->apostador_id }}"
                                                        data-aposta-rodada="{{ $aposta->rodada }}"
                                                        data-aposta-animal="{{ $aposta->animal }}"
                                                        data-aposta-valor="{{ $aposta->valor }}"
                                                        data-aposta-lo="{{ $aposta->lo }}"
                                                    >
                                                        Editar
                                                    </a>
                                                    <span class="mx-2">|</span>
                                                    <form id="delete-form-aposta-{{ $aposta->id }}" action="{{ route('apostas.destroy', $aposta->id) }}" method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                    <a 
                                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 font-medium cursor-pointer"
                                                        onclick="event.preventDefault(); if (confirm('Tem certeza que deseja deletar esta aposta?')) { document.getElementById('delete-form-aposta-{{ $aposta->id }}').submit(); }"
                                                    >
                                                        Deletar
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400">Nenhuma aposta cadastrada ainda.</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Modal Nova/Editar Corrida -->
        <div id="modal-nova-corrida" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 id="modal-corrida-titulo" class="text-lg font-bold text-gray-900 dark:text-white">Nova Corrida</h3>
                        <button 
                            onclick="fecharModalCorrida()"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                        </button>
                    </div>
                    <form id="form-corrida" method="POST" action="{{ route('corridas.store') }}">
                        @csrf
                        <input type="hidden" id="method-field" name="_method" value="">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome *</label>
                                <input 
                                    type="text" 
                                    id="corrida-nome"
                                    name="nome" 
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                >
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data e Hora *</label>
                                <input 
                                    type="datetime-local" 
                                    id="corrida-data"
                                    name="data" 
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                >
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Taxa (%) *</label>
                                <input 
                                    type="number" 
                                    id="corrida-taxa"
                                    name="taxa" 
                                    step="0.01"
                                    required
                                    min="0"
                                    max="100"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                >
                            </div>
                            
                            <div class="flex gap-3">
                                <button 
                                    type="submit" 
                                    class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors"
                                >
                                    Cadastrar
                                </button>
                                <button 
                                    type="button"
                                    onclick="document.getElementById('modal-nova-corrida').classList.add('hidden')"
                                    class="flex-1 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors"
                                >
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Novo Apostador -->
        <div id="modal-novo-apostador" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-60">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Novo Apostador</h3>
                        <button 
                            onclick="document.getElementById('modal-novo-apostador').classList.add('hidden')"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                        </button>
                    </div>
                    <form id="form-novo-apostador">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome *</label>
                                <input 
                                    type="text" 
                                    id="apostador-nome"
                                    name="nome" 
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                >
                            </div>
                            <input type="hidden" name="corrida_id" value="{{ $corridaSelecionada?->id }}">
                            
                            <div class="flex gap-3">
                                <button 
                                    type="submit" 
                                    class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors"
                                >
                                    Cadastrar
                                </button>
                                <button 
                                    type="button"
                                    onclick="document.getElementById('modal-novo-apostador').classList.add('hidden')"
                                    class="flex-1 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors"
                                >
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Cadastrar/Editar Aposta -->
        <div id="modal-aposta" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 id="modal-aposta-titulo" class="text-lg font-bold text-gray-900 dark:text-white">Nova Aposta</h3>
                        <button 
                            onclick="fecharModalAposta()"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <form id="form-aposta" method="POST" action="{{ route('apostas.store') }}">
                        @csrf
                        <input type="hidden" id="method-field-aposta" name="_method" value="">
                        <input type="hidden" name="corrida_id" value="{{ $corridaSelecionada?->id }}">
                        
                        <div class="space-y-4">
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Apostador *</label>
                                    <button 
                                        type="button"
                                        onclick="document.getElementById('modal-novo-apostador').classList.remove('hidden')"
                                        class="flex items-center gap-1 px-2 py-1 text-xs bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Novo
                                    </button>
                                </div>
                                <div class="relative">
                                    <div class="relative">
                                        <input 
                                            type="text" 
                                            id="search-apostador"
                                            placeholder="Selecione ou busque um apostador..."
                                            autocomplete="off"
                                            readonly
                                            class="w-full px-4 py-2 pr-8 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white cursor-pointer"
                                        >
                                        <svg class="absolute right-3 top-3 h-5 w-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                    <select 
                                        id="select-apostador"
                                        name="apostador_id" 
                                        required
                                        class="hidden"
                                    >
                                        <option value="">Selecione um apostador</option>
                                        @foreach($apostadores as $apostador)
                                            <option value="{{ $apostador->id }}">{{ $apostador->nome }}</option>
                                        @endforeach
                                    </select>
                                    <div id="apostadores-dropdown" class="hidden absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg">
                                        <div class="p-2 border-b border-gray-200 dark:border-gray-600">
                                            <input 
                                                type="text" 
                                                id="filter-apostador"
                                                placeholder="Buscar..."
                                                autocomplete="off"
                                                class="w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-600 dark:text-white text-sm"
                                            >
                                        </div>
                                        <div class="max-h-48 overflow-y-auto">
                                            @foreach($apostadores as $apostador)
                                                <div class="apostador-option px-4 py-2 hover:bg-blue-50 dark:hover:bg-gray-600 cursor-pointer text-gray-900 dark:text-white" data-id="{{ $apostador->id }}" data-nome="{{ $apostador->nome }}">
                                                    {{ $apostador->nome }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rodada *</label>
                                <input 
                                    type="number" 
                                    id="aposta-rodada"
                                    name="rodada" 
                                    required
                                    min="1"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                >
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Animal *</label>
                                <input 
                                    type="number" 
                                    id="aposta-animal"
                                    name="animal" 
                                    required
                                    min="1"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                >
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valor (R$)</label>
                                <input 
                                    type="number" 
                                    id="aposta-valor"
                                    name="valor" 
                                    step="0.01"
                                    min="0"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                >
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">LO (R$)</label>
                                <input 
                                    type="number" 
                                    id="aposta-lo"
                                    name="lo" 
                                    step="0.01"
                                    min="0"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                >
                            </div>
                            
                            <div class="flex gap-3">
                                <button 
                                    type="submit" 
                                    class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors"
                                >
                                    Salvar
                                </button>
                                <button 
                                    type="button"
                                    onclick="fecharModalAposta()"
                                    class="flex-1 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors"
                                >
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.getElementById('form-novo-apostador').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const nomeInput = document.getElementById('apostador-nome');
                const searchApostadorInput = document.getElementById('search-apostador');
                const selectApostadorElement = document.getElementById('select-apostador');
                const apostadoresDropdown = document.getElementById('apostadores-dropdown');
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                fetch('{{ route("apostadores.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Adiciona ao select real
                        const novaOptionSelect = document.createElement('option');
                        novaOptionSelect.value = data.apostador.id;
                        novaOptionSelect.textContent = data.apostador.nome;
                        selectApostadorElement.appendChild(novaOptionSelect);
                        
                        // Adiciona ao dropdown visual
                        const novaOptionDropdown = document.createElement('div');
                        novaOptionDropdown.className = 'apostador-option px-4 py-2 hover:bg-blue-50 dark:hover:bg-gray-600 cursor-pointer text-gray-900 dark:text-white';
                        novaOptionDropdown.dataset.id = data.apostador.id;
                        novaOptionDropdown.dataset.nome = data.apostador.nome;
                        novaOptionDropdown.textContent = data.apostador.nome;
                        
                        // Adiciona evento de clique
                        novaOptionDropdown.addEventListener('click', function() {
                            searchApostadorInput.value = this.dataset.nome;
                            selectApostadorElement.value = this.dataset.id;
                            apostadoresDropdown.classList.add('hidden');
                        });
                        
                        // Adiciona ao container do dropdown
                        const dropdownContainer = apostadoresDropdown.querySelector('.max-h-48');
                        dropdownContainer.appendChild(novaOptionDropdown);
                        
                        // Seleciona automaticamente o novo apostador
                        if (searchApostadorInput && selectApostadorElement) {
                            searchApostadorInput.value = data.apostador.nome;
                            selectApostadorElement.value = data.apostador.id;
                        }
                        
                        // Fecha o modal e limpa o formulário
                        document.getElementById('modal-novo-apostador').classList.add('hidden');
                        nomeInput.value = '';
                        
                        // Mostra mensagem de sucesso
                        const container = document.querySelector('.container');
                        const successDiv = document.createElement('div');
                        successDiv.className = 'mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative';
                        successDiv.textContent = 'Apostador criado com sucesso!';
                        container.insertBefore(successDiv, container.firstChild);
                        
                        // Remove a mensagem após 3 segundos
                        setTimeout(() => {
                            successDiv.remove();
                        }, 3000);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert(error.message || 'Erro ao cadastrar apostador. Tente novamente.');
                });
            });

            // Funções para gerenciar modal de corrida
            function abrirModalNovaCorrida() {
                // Limpa os campos
                document.getElementById('corrida-nome').value = '';
                document.getElementById('corrida-data').value = '';
                document.getElementById('corrida-taxa').value = '';
                
                // Configura para modo criação
                document.getElementById('modal-corrida-titulo').textContent = 'Nova Corrida';
                document.getElementById('form-corrida').action = '{{ route("corridas.store") }}';
                document.getElementById('method-field').value = '';
                
                // Abre o modal
                document.getElementById('modal-nova-corrida').classList.remove('hidden');
            }

            function abrirModalEdicaoCorrida(id, nome, data, taxa) {
                // Preenche os campos com os dados da corrida
                document.getElementById('corrida-nome').value = nome;
                document.getElementById('corrida-data').value = data;
                document.getElementById('corrida-taxa').value = taxa;
                
                // Configura para modo edição
                document.getElementById('modal-corrida-titulo').textContent = 'Editar Corrida';
                document.getElementById('form-corrida').action = '/corridas/' + id;
                document.getElementById('method-field').value = 'PUT';
                
                // Abre o modal
                document.getElementById('modal-nova-corrida').classList.remove('hidden');
            }

            function fecharModalCorrida() {
                document.getElementById('modal-nova-corrida').classList.add('hidden');
                // Limpa os campos ao fechar
                document.getElementById('corrida-nome').value = '';
                document.getElementById('corrida-data').value = '';
                document.getElementById('corrida-taxa').value = '';
                document.getElementById('method-field').value = '';
            }

            // Event listeners para botões de editar
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.btn-editar-corrida').forEach(function(btn) {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const id = this.dataset.corridaId;
                        const nome = this.dataset.corridaNome;
                        const data = this.dataset.corridaData;
                        const taxa = this.dataset.corridaTaxa;
                        abrirModalEdicaoCorrida(id, nome, data, taxa);
                    });
                });
                
                // Event listeners para botões de editar aposta
                document.querySelectorAll('.btn-editar-aposta').forEach(function(btn) {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const id = this.dataset.apostaId;
                        const apostadorId = this.dataset.apostaApostadorId;
                        const rodada = this.dataset.apostaRodada;
                        const animal = this.dataset.apostaAnimal;
                        const valor = this.dataset.apostaValor;
                        const lo = this.dataset.apostaLo;
                        abrirModalEdicaoAposta(id, apostadorId, rodada, animal, valor, lo);
                    });
                });
            });

            // Funções para gerenciar modal de aposta
            function abrirModalNovaAposta() {
                // Limpa os campos
                document.getElementById('search-apostador').value = '';
                document.getElementById('select-apostador').value = '';
                document.getElementById('aposta-rodada').value = '';
                document.getElementById('aposta-animal').value = '';
                document.getElementById('aposta-valor').value = '';
                document.getElementById('aposta-lo').value = '';
                
                // Configura para modo criação
                document.getElementById('modal-aposta-titulo').textContent = 'Nova Aposta';
                document.getElementById('form-aposta').action = '{{ route("apostas.store") }}';
                document.getElementById('method-field-aposta').value = '';
                
                // Abre o modal
                document.getElementById('modal-aposta').classList.remove('hidden');
            }

            function abrirModalEdicaoAposta(id, apostadorId, rodada, animal, valor, lo) {
                // Busca o nome do apostador no select
                const selectApostador = document.getElementById('select-apostador');
                const option = selectApostador.querySelector(`option[value="${apostadorId}"]`);
                const apostadorNome = option ? option.textContent : '';
                
                // Preenche os campos com os dados da aposta
                document.getElementById('search-apostador').value = apostadorNome;
                selectApostador.value = apostadorId;
                document.getElementById('aposta-rodada').value = rodada;
                document.getElementById('aposta-animal').value = animal;
                document.getElementById('aposta-valor').value = valor;
                document.getElementById('aposta-lo').value = lo;
                
                // Configura para modo edição
                document.getElementById('modal-aposta-titulo').textContent = 'Editar Aposta';
                document.getElementById('form-aposta').action = '/apostas/' + id;
                document.getElementById('method-field-aposta').value = 'PUT';
                
                // Abre o modal
                document.getElementById('modal-aposta').classList.remove('hidden');
            }

            function fecharModalAposta() {
                document.getElementById('modal-aposta').classList.add('hidden');
                // Limpa os campos ao fechar
                document.getElementById('search-apostador').value = '';
                document.getElementById('select-apostador').value = '';
                document.getElementById('aposta-rodada').value = '';
                document.getElementById('aposta-animal').value = '';
                document.getElementById('aposta-valor').value = '';
                document.getElementById('aposta-lo').value = '';
                document.getElementById('method-field-aposta').value = '';
                document.getElementById('apostadores-dropdown').classList.add('hidden');
            }

            // Sistema de select com busca integrada
            const searchApostador = document.getElementById('search-apostador');
            const selectApostador = document.getElementById('select-apostador');
            const apostadoresDropdown = document.getElementById('apostadores-dropdown');
            const filterApostador = document.getElementById('filter-apostador');
            const apostadorOptions = document.querySelectorAll('.apostador-option');

            if (searchApostador && selectApostador) {
                // Clique no campo para abrir/fechar dropdown
                searchApostador.addEventListener('click', function() {
                    apostadoresDropdown.classList.toggle('hidden');
                    if (!apostadoresDropdown.classList.contains('hidden')) {
                        filterApostador.value = '';
                        apostadorOptions.forEach(opt => opt.style.display = 'block');
                        setTimeout(() => filterApostador.focus(), 100);
                    }
                });
                
                // Filtro de busca
                filterApostador.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    
                    apostadorOptions.forEach(function(option) {
                        const nome = option.dataset.nome.toLowerCase();
                        if (nome.includes(searchTerm)) {
                            option.style.display = 'block';
                        } else {
                            option.style.display = 'none';
                        }
                    });
                });
                
                // Previne o dropdown de fechar quando clicar no campo de busca
                filterApostador.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
                
                // Clique nas opções
                apostadorOptions.forEach(function(option) {
                    option.addEventListener('click', function() {
                        searchApostador.value = this.dataset.nome;
                        selectApostador.value = this.dataset.id;
                        apostadoresDropdown.classList.add('hidden');
                    });
                });
                
                // Fecha o dropdown ao clicar fora
                document.addEventListener('click', function(e) {
                    if (!searchApostador.contains(e.target) && !apostadoresDropdown.contains(e.target)) {
                        apostadoresDropdown.classList.add('hidden');
                    }
                });
            }
        </script>
    </body>
</html>
