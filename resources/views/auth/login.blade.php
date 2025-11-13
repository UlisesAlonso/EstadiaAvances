@extends('layouts.auth')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="min-h-screen flex">
    <!-- Left side - Login Form -->
    <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Logo -->
            <div class="flex justify-center mb-6">
                <div class="relative">
                    <!-- Heart icon with ECG line -->
                    <svg class="h-16 w-16 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                        <!-- Heart shape -->
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        <!-- ECG line through heart -->
                        <path d="M6 12 L8 10 L10 14 L12 8 L14 12 L16 10 L18 14" 
                              stroke="white" 
                              stroke-width="2" 
                              fill="none" 
                              stroke-linecap="round" 
                              stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
            
            <h1 class="text-center text-4xl font-serif font-bold text-gray-900 mb-2">
                Cardio Vida
            </h1>
            <p class="mt-2 text-center text-sm text-gray-600">
                Inicia sesión en tu cuenta
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('timeout'))
                <div class="mb-4 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">
                                Sesión Expirada
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>{{ session('timeout') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white py-8 px-6 shadow sm:rounded-lg sm:px-10">
                <form class="space-y-6" action="{{ route('login') }}" method="POST">
                    @csrf
                    
                    <div>
                        <label for="correo" class="block text-sm font-medium text-gray-700 mb-1">
                            Correo electrónico
                        </label>
                        <input id="correo" 
                               name="correo" 
                               type="email" 
                               autocomplete="email" 
                               required 
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                               placeholder="Correo electrónico"
                               value="{{ old('correo') }}">
                    </div>

                    <div>
                        <label for="contrasena" class="block text-sm font-medium text-gray-700 mb-1">
                            Contraseña
                        </label>
                        <input id="contrasena" 
                               name="contrasena" 
                               type="password" 
                               autocomplete="current-password" 
                               required 
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                               placeholder="Contraseña">
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember_me" 
                                   name="remember" 
                                   type="checkbox" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                                Recordarme
                            </label>
                        </div>

                        <div class="text-sm">
                            <a href="{{ route('password.request') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Iniciar Sesión
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Right side - Image -->
    <div class="hidden lg:block relative w-0 flex-1 overflow-hidden bg-white"
     style="clip-path: polygon(10% 0, 100% 0, 90% 100%, 0% 100%);">
    <div class="absolute inset-0">
        <img src="{{ asset('images/login-bg.png.png') }}" 
             alt="Cardio Vida - Cuidado Cardiovascular" 
             class="w-full h-full object-cover">
    </div>
    <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/0 to-white/10"></div>
</div>

</div>
@endsection 