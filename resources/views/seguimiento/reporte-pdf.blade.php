<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Seguimiento - {{ $paciente->usuario->nombre }} {{ $paciente->usuario->apPaterno }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #2563eb;
        }
        .info-paciente {
            background-color: #f3f4f6;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .info-paciente h2 {
            margin-top: 0;
            color: #1f2937;
        }
        .stats {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .stat-item {
            display: table-cell;
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        .stat-item strong {
            display: block;
            font-size: 18px;
            color: #2563eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #2563eb;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .observacion {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Seguimiento del Paciente</h1>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Información del Paciente -->
    <div class="info-paciente">
        <h2>Datos del Paciente</h2>
        <p><strong>Nombre:</strong> {{ $paciente->usuario->nombre }} {{ $paciente->usuario->apPaterno }} {{ $paciente->usuario->apMaterno }}</p>
        <p><strong>Correo:</strong> {{ $paciente->usuario->correo }}</p>
        @if($paciente->fecha_nacimiento)
        <p><strong>Fecha de Nacimiento:</strong> {{ \Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y') }}</p>
        <p><strong>Edad:</strong> {{ \Carbon\Carbon::parse($paciente->fecha_nacimiento)->age }} años</p>
        @endif
    </div>

    <!-- Estadísticas -->
    <div class="stats">
        <div class="stat-item">
            <strong>{{ $estadisticas['citas']['total'] }}</strong>
            <span>Citas</span>
        </div>
        <div class="stat-item">
            <strong>{{ $estadisticas['tratamientos']['total'] }}</strong>
            <span>Tratamientos</span>
        </div>
        <div class="stat-item">
            <strong>{{ $estadisticas['actividades']['total'] }}</strong>
            <span>Actividades</span>
        </div>
        <div class="stat-item">
            <strong>{{ $estadisticas['analisis']['total'] }}</strong>
            <span>Análisis</span>
        </div>
    </div>

    <!-- Timeline de Eventos -->
    <h2>Historial de Eventos</h2>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Descripción</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($datosConsolidados['timeline'] as $evento)
            <tr>
                <td>{{ \Carbon\Carbon::parse($evento['fecha'])->format('d/m/Y') }}</td>
                <td>{{ ucfirst($evento['tipo']) }}</td>
                <td>{{ $evento['descripcion'] }}</td>
                <td>{{ isset($evento['estado']) ? ucfirst($evento['estado']) : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Observaciones Médicas -->
    @if(count($observaciones) > 0)
    <div class="page-break"></div>
    <h2>Observaciones Médicas</h2>
    @foreach($observaciones as $observacion)
    <div class="observacion">
        <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($observacion->fecha_observacion)->format('d/m/Y') }}</p>
        @if($observacion->tipo)
        <p><strong>Tipo:</strong> {{ ucfirst($observacion->tipo) }}</p>
        @endif
        <p><strong>Observación:</strong> {{ $observacion->observacion }}</p>
        @if($observacion->medico)
        <p><strong>Médico:</strong> {{ $observacion->medico->usuario->nombre }} {{ $observacion->medico->usuario->apPaterno }}</p>
        @endif
    </div>
    @endforeach
    @endif
</body>
</html>

