<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Cardio Vida') }} - @yield('title', 'Iniciar Sesión')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* Estilos personalizados para componentes */
        .form-input {
            appearance: none;
            border-radius: 0.375rem;
            position: relative;
            display: block;
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            background-color: #fff;
            color: #111827;
        }
        .form-input:focus {
            outline: none;
            ring: 2px;
            ring-color: #3b82f6;
            border-color: #3b82f6;
        }
        .form-input::placeholder {
            color: #6b7280;
        }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            border: 1px solid transparent;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #fff;
            background-color: #2563eb;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
        }
        .btn-primary:focus {
            outline: none;
            ring: 2px;
            ring-offset: 2px;
            ring-color: #3b82f6;
        }
        .alert-error {
            background-color: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            position: relative;
        }
    </style>
</head>
<body class="font-sans antialiased bg-white">
    @yield('content')
</body>
</html>

