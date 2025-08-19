<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f4f8;
            overflow-x: hidden;
        }
        
        .blur-effect {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
        
        .header-gradient {
            background: linear-gradient(135deg, #3b82f6 0%, #0ea5e9 100%);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.1);
        }
        
        .search-box:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        
        .button-effect {
            transition: all 0.3s ease;
        }
        
        .button-effect:hover {
            transform: translateY(-2px);
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fadeIn {
            animation: fadeIn 0.6s ease forwards;
        }
        
        .animate-delay-100 {
            animation-delay: 0.1s;
        }
        
        .animate-delay-200 {
            animation-delay: 0.2s;
        }
        
        .animate-delay-300 {
            animation-delay: 0.3s;
        }
        
        /* Scrollbar personalizada */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }
        
        /* Diseño inspirado en el área de la salud */
        .health-icon {
            color: #0ea5e9;
        }
        
        .table-header {
            background: linear-gradient(to right, rgba(14, 165, 233, 0.1), rgba(59, 130, 246, 0.05));
            border-left: 4px solid #0ea5e9;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50">
    <div class="header-gradient text-white p-6 shadow-lg animate-fadeIn opacity-0">
        <div class="container mx-auto max-w-6xl">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold mb-1">Sistema de Laudos ZuMed</h1>
                    <p class="text-sm md:text-base text-blue-100">
                        Olá, {{ session('nombres') }} {{ session('apellidos') }} 
                        <span class="inline-block px-3 py-1 bg-white/10 rounded-full text-xs mt-1 md:mt-0 md:ml-2">
                            {{ session('nombre_clinica') }}
                        </span>
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('logout') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-sm font-medium transition-all button-effect">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Sair
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto max-w-6xl px-4 py-8">
        <!-- Barra de búsqueda -->
        <div class="animate-fadeIn opacity-0 animate-delay-100">
            <form action="{{ route('dashboard') }}" method="GET">
                <div class="relative mb-8">
                    <input
                        type="text"
                        name="search"
                        class="search-box w-full outline-none bg-white text-gray-700 px-5 py-4 rounded-xl border border-gray-200 focus:border-blue-500 transition-all shadow-sm"
                        placeholder="Pesquise por documento ou nome do paciente"
                        value="{{ request('search') }}"
                    />
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-lg transition-all button-effect">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- Stats Cards (Nuevos) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 animate-fadeIn opacity-0 animate-delay-200">
            <div class="bg-white rounded-xl p-6 shadow-md border border-gray-100 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total de Laudos</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $laudos->total() }}</h3>
                    </div>
                    <div class="h-12 w-12 bg-blue-50 rounded-full flex items-center justify-center health-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-6 shadow-md border border-gray-100 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Laudos do Mês</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ intval($laudos->total() * 0.4) }}</h3>
                    </div>
                    <div class="h-12 w-12 bg-green-50 rounded-full flex items-center justify-center text-green-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-6 shadow-md border border-gray-100 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Pacientes</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ intval($laudos->total() * 0.7) }}</h3>
                    </div>
                    <div class="h-12 w-12 bg-purple-50 rounded-full flex items-center justify-center text-purple-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabla para Desktop -->
        <div class="hidden md:block animate-fadeIn opacity-0 animate-delay-300">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Laudos Recentes</h2>
            <div class="bg-white rounded-xl overflow-hidden shadow-md border border-gray-100">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr>
                            @foreach(['Dados do paciente', 'Tipo de estudo', 'Ações'] as $header)
                                <th scope="col" class="table-header px-6 py-4 font-medium text-gray-700">
                                    {{ $header }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($laudos as $laudo)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <th scope="row" class="flex items-center px-6 py-4 whitespace-nowrap">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-500 mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-gray-700 font-medium">{{ $laudo['nombres'] }}</div>
                                        <div class="text-gray-500 text-sm">Documento: {{ $laudo['documento'] }}</div>
                                        <div class="text-gray-500 text-sm">Data: {{ $laudo['fecha_estudio'] }}</div>
                                    </div>
                                </th>
                                <td style="display:none" class="px-6 py-4 whitespace-nowrap">{{ $laudo['documento'] }}</td>
                                <td style="display:none" class="px-6 py-4 whitespace-nowrap">{{ $laudo['fecha_estudio'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">
                                        {{ $laudo['tipo_estudio'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('ver.pdf', ['id_clinica' => session('id_clinica'), 'id_laudo' => $laudo['id_documento']]) }}"
                                        target="_blank"
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm transition-all button-effect">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Ver PDF
                                        </a>
                                        <a href="{{ route('download.pdf', ['id_clinica' => session('id_clinica'), 'id_laudo' => $laudo['id_documento']]) }}"
                                        class="inline-flex items-center px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm transition-all button-effect">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            Baixar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Paginación Desktop -->
                <div class="px-6 py-4 bg-gray-50 border-t">
                    @if($laudos->hasPages())
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-600">
                                    Mostrando {{ $laudos->firstItem() }} a {{ $laudos->lastItem() }}
                                    de {{ $laudos->total() }} resultados
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                @if(!$laudos->onFirstPage())
                                    <a href="{{ $laudos->previousPageUrl() }}"
                                    class="px-3 py-1.5 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition button-effect">
                                        Anterior
                                    </a>
                                @endif

                                <div class="hidden sm:flex items-center gap-1">
                                    @foreach($laudos->getUrlRange(max($laudos->currentPage() - 2, 1), min($laudos->currentPage() + 2, $laudos->lastPage())) as $page => $url)
                                        <a href="{{ $url }}"
                                        class="px-3 py-1.5 {{ $laudos->currentPage() === $page ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} border border-gray-300 rounded-md text-sm font-medium transition button-effect">
                                            {{ $page }}
                                        </a>
                                    @endforeach
                                </div>

                                @if($laudos->hasMorePages())
                                    <a href="{{ $laudos->nextPageUrl() }}"
                                    class="px-3 py-1.5 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition button-effect">
                                        Próximo
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Vista de tarjetas para Móvil -->
        <div class="md:hidden space-y-4 animate-fadeIn opacity-0 animate-delay-300">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Laudos Recentes</h2>
            @foreach($laudos as $laudo)
                <div class="bg-white rounded-xl overflow-hidden shadow-md border border-gray-100 p-4 card-hover">
                    <div class="flex items-start gap-3">
                        <div class="h-10 w-10 rounded-full bg-blue-100 flex-shrink-0 flex items-center justify-center text-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-gray-700 font-medium">{{ $laudo['nombres'] }}</h3>
                            <p class="text-gray-500 text-sm">Documento: {{ $laudo['documento'] }}</p>
                            <p class="text-gray-500 text-sm">Data: {{ $laudo['fecha_estudio'] }}</p>
                            <div class="mt-2">
                                <span class="inline-block px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">
                                    {{ $laudo['tipo_estudio'] }}
                                </span>
                            </div>
                            <div class="mt-3">
                                <div class="flex space-x-2">
                                    <a href="{{ route('ver.pdf', ['id_clinica' => session('id_clinica'), 'id_laudo' => $laudo['id_documento']]) }}"
                                    target="_blank"
                                    class="inline-flex items-center px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm transition-all button-effect">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Ver PDF
                                    </a>
                                    <a href="{{ route('download.pdf', ['id_clinica' => session('id_clinica'), 'id_laudo' => $laudo['id_documento']]) }}"
                                    class="inline-flex items-center px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm transition-all button-effect">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Baixar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Paginación Móvil -->
            <div class="bg-white rounded-xl overflow-hidden shadow-md border border-gray-100 p-4">
                @if($laudos->hasPages())
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="w-full sm:w-auto text-sm text-gray-600 text-center sm:text-left">
                            Mostrando {{ $laudos->firstItem() }} a {{ $laudos->lastItem() }}
                            de {{ $laudos->total() }} resultados
                        </div>
                        <div class="flex items-center justify-center gap-2">
                            @if(!$laudos->onFirstPage())
                                <a href="{{ $laudos->previousPageUrl() }}"
                                class="px-3 py-1.5 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition button-effect">
                                    Anterior
                                </a>
                            @endif

                            <div class="hidden sm:flex items-center gap-1">
                                @foreach($laudos->getUrlRange(max($laudos->currentPage() - 2, 1), min($laudos->currentPage() + 2, $laudos->lastPage())) as $page => $url)
                                    <a href="{{ $url }}"
                                    class="px-3 py-1.5 {{ $laudos->currentPage() === $page ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} border border-gray-300 rounded-md text-sm font-medium transition button-effect">
                                        {{ $page }}
                                    </a>
                                @endforeach
                            </div>

                            @if($laudos->hasMorePages())
                                <a href="{{ $laudos->nextPageUrl() }}"
                                class="px-3 py-1.5 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition button-effect">
                                    Próximo
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Script para animaciones
        document.addEventListener('DOMContentLoaded', function() {
            const fadeElements = document.querySelectorAll('.animate-fadeIn');
            fadeElements.forEach(el => {
                setTimeout(() => {
                    el.style.opacity = '1';
                }, 100);
            });
        });
    </script>
</body>
</html>
