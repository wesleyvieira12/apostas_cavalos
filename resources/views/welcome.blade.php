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
                            onclick="document.getElementById('modal-nova-corrida').classList.remove('hidden')"
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
                            <a 
                                href="{{ route('home') }}" 
                                class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors"
                            >
                                Voltar
                            </a>
                        </div>
                    </div>

                    <!-- Formulário Cadastrar Aposta -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Cadastrar Aposta</h3>
                        <form method="POST" action="{{ route('apostas.store') }}">
                            @csrf
                            <input type="hidden" name="corrida_id" value="{{ $corridaSelecionada->id }}">
                            
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
                                    <select 
                                        id="select-apostador"
                                        name="apostador_id" 
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                    >
                                        <option value="">Selecione um apostador</option>
                                        @foreach($apostadores as $apostador)
                                            <option value="{{ $apostador->id }}">{{ $apostador->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rodada *</label>
                                        <input 
                                            type="number" 
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
                                            name="animal" 
                                            required
                                            min="1"
                                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                        >
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Valor (R$) *</label>
                                        <input 
                                            type="number" 
                                            name="valor" 
                                            step="0.01"
                                            required
                                            min="0.01"
                                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                        >
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">LO (R$)</label>
                                        <input 
                                            type="number" 
                                            name="lo" 
                                            step="0.01"
                                            min="0"
                                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                        >
                                    </div>
                                    
                                <button 
                                    type="submit" 
                                    class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors"
                                >
                                    Cadastrar Aposta
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Lista de Apostas -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Apostas da Corrida</h3>
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

        <!-- Modal Nova Corrida -->
        <div id="modal-nova-corrida" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Nova Corrida</h3>
                        <button 
                            onclick="document.getElementById('modal-nova-corrida').classList.add('hidden')"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('corridas.store') }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome *</label>
                                <input 
                                    type="text" 
                                    name="nome" 
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                >
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data e Hora *</label>
                                <input 
                                    type="datetime-local" 
                                    name="data" 
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                >
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Taxa (%) *</label>
                                <input 
                                    type="number" 
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
        <div id="modal-novo-apostador" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
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

        <script>
            document.getElementById('form-novo-apostador').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const nomeInput = document.getElementById('apostador-nome');
                const selectApostador = document.getElementById('select-apostador');
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
                        // Adiciona a nova opção ao select
                        const option = document.createElement('option');
                        option.value = data.apostador.id;
                        option.textContent = data.apostador.nome;
                        option.selected = true;
                        selectApostador.appendChild(option);
                        
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
        </script>
    </body>
</html>
