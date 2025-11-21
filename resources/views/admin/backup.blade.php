@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Mensajes de éxito/error -->
        @if(session('success'))
            <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Sección: Descargar Respaldo -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Generar Respaldo</h2>
            <p class="text-gray-600 mb-4">
                Descargue un respaldo completo de la base de datos en formato ZIP. 
                Este archivo contiene un dump SQL de toda la base de datos.
            </p>
            <a 
                href="{{ route('backup.respaldo') }}" 
                class="inline-flex items-center px-6 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Descargar Respaldo
            </a>
        </div>

        <!-- Sección: Restaurar Respaldo -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Restaurar Respaldo</h2>

            <form action="{{ route('backup.restore') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label for="backup_file" class="block text-sm font-medium text-gray-700 mb-2">
                        Seleccionar archivo de respaldo (.zip):
                    </label>
                    <input 
                        type="file" 
                        name="backup_file" 
                        id="backup_file"
                        accept=".zip"
                        required
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                    >
                    <p class="mt-1 text-sm text-gray-500">Seleccione un archivo ZIP que contenga el respaldo de la base de datos.</p>
                </div>

                <div class="flex justify-end">
                    <button 
                        type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition"
                    >
                        Restaurar Respaldo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

