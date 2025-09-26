
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de cita</title>
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
        .code {
            background-color: #1f2937;
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            letter-spacing: 4px;
        }
        .warning {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            color: #92400e;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
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
        <p>Cita Confirmada</p>
    </div>
    
    <div class="content">
        <h1>Hola {{ $user->nombre }}</h1>

    <p>Tu cita ha sido confirmada con éxito.</p>
    <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</p>
    <p><strong>Hora:</strong> {{ \Carbon\Carbon::parse($cita->fecha)->format('H:i') }}</p>
    <p><strong>Médico:</strong> {{ $cita->medico->usuario->nombre }} {{ $cita->medico->usuario->apPaterno }}</p>

    <br>
    <p>Gracias por confiar en <strong>Cardio Vida</strong>.</p>
    
    <div class="footer">
        <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
        <p>© {{ date('Y') }} Cardio Vida. Todos los derechos reservados.</p>
    </div>
</body>
</html> 