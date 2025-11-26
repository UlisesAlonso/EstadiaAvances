<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Análisis Clínicos</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            color: #333;
            line-height: 1.4;
        }
        
        .header {
            background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
            color: white;
            padding: 20px;
            margin-bottom: 15px;
            width: 100%;
            text-align: center;
        }
        
        .header-center {
            color:rgb(74, 177, 255);
            width: 100%;
            text-align: center;
        }
        
        .header h1 {
            color:rgb(74, 177, 255);
            font-size: 20pt;
            font-weight: bold;
            margin-bottom: 3px;
            text-align: center;
        }
        
        .header p {
            font-size: 9pt;
            opacity: 0.9;
            text-align: center;
        }
        
        .info-section {
            background: #EEF2FF;
            border: 1px solid #C7D2FE;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 15px;
        }
        
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        
        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 40%;
            color: #6B7280;
        }
        
        .info-value {
            display: table-cell;
            color: #111827;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            display: table-cell;
            background: #EEF2FF;
            border: 2px solid #6366F1;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            vertical-align: middle;
        }
        
        .stat-value {
            font-size: 24pt;
            font-weight: bold;
            color: #6366F1;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 9pt;
            color: #6B7280;
            text-transform: uppercase;
        }
        
        .table-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        
        .table-title {
            font-size: 14pt;
            font-weight: bold;
            color: #111827;
            margin-bottom: 15px;
            border-bottom: 2px solid #6366F1;
            padding-bottom: 8px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        thead {
            background: #6366F1;
            color: white;
        }
        
        th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #4F46E5;
        }
        
        td {
            padding: 8px 10px;
            border: 1px solid #E5E7EB;
        }
        
        tbody tr:nth-child(even) {
            background: #F9FAFB;
        }
        
        .footer {
            margin-top: 40px;
            padding: 10px 20px;
            border-top: 2px solid #E5E7EB;
            background: #F9FAFB;
            text-align: center;
            color: #6B7280;
            font-size: 8pt;
        }
        .logo {
        align-items: left;
        position: absolute;
        left: 15px;
        top: 10px;
            max-width: 80px;
            max-height: 80px;
            display: block;
        }
    </style>
</head>
<body>
    <!-- Encabezado -->
    <div class="header">
    @if(isset($logo) && $logo)
            <img src="{{ $logo }}" alt="Cardio Vida" class="logo">
        @endif
        <div class="header-center">
            <h1>REPORTE DE ANÁLISIS CLÍNICOS MÁS REPETIDOS</h1>
            <p>Sistema de Gestión Cardiovascular</p>
        </div>
    </div>

    <!-- Información del Reporte -->
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Fecha de Generación:</div>
            <div class="info-value">{{ $fechaGeneracion }}</div>
        </div>
        @if($fechaDesde || $fechaHasta)
        <div class="info-row">
            <div class="info-label">Período:</div>
            <div class="info-value">
                @if($fechaDesde && $fechaHasta)
                    Del {{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaHasta)->format('d/m/Y') }}
                @elseif($fechaDesde)
                    Desde el {{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }}
                @elseif($fechaHasta)
                    Hasta el {{ \Carbon\Carbon::parse($fechaHasta)->format('d/m/Y') }}
                @endif
            </div>
        </div>
        @endif
        @if($medicoFiltrado)
        <div class="info-row">
            <div class="info-label">Médico:</div>
            <div class="info-value">{{ $medicoFiltrado }}</div>
        </div>
        @endif
        @if($pacienteFiltrado)
        <div class="info-row">
            <div class="info-label">Paciente:</div>
            <div class="info-value">{{ $pacienteFiltrado }}</div>
        </div>
        @endif
        <div class="info-row">
            <div class="info-label">Top N Análisis:</div>
            <div class="info-value">{{ $limite }}</div>
        </div>
    </div>

    <!-- Estadísticas Generales -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $estadisticas['total'] }}</div>
            <div class="stat-label">Total de Análisis</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $estadisticas['tipos_unicos'] }}</div>
            <div class="stat-label">Tipos Únicos</div>
        </div>
    </div>

    <!-- Tabla de Análisis Más Repetidos -->
    @if($analisisPorTipo->count() > 0)
    <div class="table-section">
        <div class="table-title">Análisis Clínicos Más Repetidos (Top {{ $limite }})</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tipo de Análisis</th>
                    <th>Cantidad</th>
                    <th>Porcentaje</th>
                </tr>
            </thead>
            <tbody>
                @foreach($analisisPorTipo as $index => $item)
                <tr>
                    <td><strong>{{ $index + 1 }}</strong></td>
                    <td>{{ $item['tipo'] }}</td>
                    <td><strong>{{ $item['cantidad'] }}</strong></td>
                    <td><strong>{{ $item['porcentaje'] }}%</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Detalles por Tipo de Análisis -->
    @foreach($analisisPorTipo as $item)
    @if($item['analisis']->count() > 0)
    <div class="table-section">
        <div class="table-title">Detalles: {{ $item['tipo'] }} ({{ $item['cantidad'] }} análisis)</div>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Paciente</th>
                    <th>Médico</th>
                    <th>Resultado</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($item['analisis'] as $analisis)
                <tr>
                    <td>{{ $analisis->fecha ? \Carbon\Carbon::parse($analisis->fecha)->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $analisis->paciente && $analisis->paciente->usuario ? ($analisis->paciente->usuario->nombre . ' ' . $analisis->paciente->usuario->apPaterno) : 'N/A' }}</td>
                    <td>{{ $analisis->medico && $analisis->medico->usuario ? ($analisis->medico->usuario->nombre . ' ' . $analisis->medico->usuario->apPaterno) : 'N/A' }}</td>
                    <td>{{ $analisis->resultado ?: 'N/A' }}</td>
                    <td>{{ $analisis->observaciones_clinicas ? substr($analisis->observaciones_clinicas, 0, 50) . '...' : 'Sin observaciones' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    @endforeach

    <!-- Footer -->
    <div class="footer">
        <p>Documento generado el {{ $fechaGeneracion }} | Sistema de Gestión Cardiovascular</p>
    </div>
</body>
</html>

