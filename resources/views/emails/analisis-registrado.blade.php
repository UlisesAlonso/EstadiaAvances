<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análisis registrado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #3b82f6;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .info-box {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .info-box p {
            margin: 10px 0;
        }
        .info-box strong {
            color: #1f2937;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Cardio Vida</h1>
        <p>Nuevo análisis registrado</p>
    </div>
    
    <div class="content">
        <h1>Hola {{ $user->nombre }}</h1>

        <p>Se ha registrado un nuevo análisis clínico a tu nombre en el sistema.</p>
        
        <div class="info-box">
            <p><strong>Tipo de estudio:</strong> {{ $analisis->tipo_estudio }}</p>
            <p><strong>Fecha del análisis:</strong> {{ \Carbon\Carbon::parse($analisis->fecha_analisis)->format('d/m/Y') }}</p>
            @if($analisis->medico && $analisis->medico->usuario)
            <p><strong>Médico responsable:</strong> {{ $analisis->medico->usuario->nombre }} {{ $analisis->medico->usuario->apPaterno }}</p>
            @endif
            @if($analisis->descripcion)
            <p><strong>Descripción:</strong> {{ $analisis->descripcion }}</p>
            @endif
        </div>

        <p>Ingresa a tu cuenta para revisar los detalles completos del análisis y sus resultados.</p>
        
        <br>
        <p>Gracias por confiar en <strong>Cardio Vida</strong>.</p>
        
        <div class="footer">
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
            <p>© {{ date('Y') }} Cardio Vida. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>



