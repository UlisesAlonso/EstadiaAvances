<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Diagnósticos</title>
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
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
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
            background: #FEF2F2;
            border: 1px solid #FECACA;
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
            background: #FEF2F2;
            border: 2px solid #EF4444;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            vertical-align: middle;
        }
        
        .stat-value {
            font-size: 24pt;
            font-weight: bold;
            color: #EF4444;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 9pt;
            color: #6B7280;
            text-transform: uppercase;
        }
        
        .chart-section {
            margin: 15px 0;
            page-break-inside: avoid;
        }
        
        .chart-title {
            font-size: 14pt;
            font-weight: bold;
            color: #111827;
            margin-bottom: 10px;
            text-align: center;
            border-bottom: 2px solid #EF4444;
            padding-bottom: 5px;
        }
        
        .chart-container {
            text-align: center;
            margin: 20px 0;
        }
        
        .chart-image {
            max-width: 100%;
            height: auto;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
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
            border-bottom: 2px solid #EF4444;
            padding-bottom: 8px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        thead {
            background: #EF4444;
            color: white;
        }
        
        th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #DC2626;
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
            <h1>REPORTE DE DIAGNÓSTICOS POR PERÍODO</h1>
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
            <div class="info-label">Tipo de Período:</div>
            <div class="info-value">
                @if($tipoPeriodo == 'mes')
                    Por Mes
                @elseif($tipoPeriodo == 'trimestre')
                    Por Trimestre
                @else
                    Por Año
                @endif
            </div>
        </div>
    </div>

    <!-- Estadísticas Generales -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $estadisticas['total'] }}</div>
            <div class="stat-label">Total de Diagnósticos</div>
        </div>
    </div>

    <!-- Gráfica de Barras -->
    @if(!empty($datosGrafica) && isset($imagenGrafica) && $imagenGrafica)
    <div class="chart-section">
        <div class="chart-title">{{ $tituloGrafica }}</div>
        <div class="chart-container">
            <img src="{{ $imagenGrafica }}" alt="Gráfica de Barras" class="chart-image">
        </div>
    </div>
    @endif

    <!-- Tabla de Distribución -->
    @if(!empty($datosGrafica))
    <div class="table-section">
        <div class="table-title">Distribución Detallada por Período</div>
        <table>
            <thead>
                <tr>
                    <th>Período</th>
                    <th>Cantidad</th>
                    <th>Porcentaje</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datosGrafica as $dato)
                <tr>
                    <td>{{ $dato['label'] }}</td>
                    <td>{{ $dato['cantidad'] }}</td>
                    <td>{{ $dato['porcentaje'] }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Tabla de Diagnósticos -->
    <div class="table-section">
        <div class="table-title">Lista de Diagnósticos</div>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Paciente</th>
                    <th>Médico</th>
                    <th>Diagnóstico</th>
                    <th>Descripción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($diagnosticos as $diagnostico)
                <tr>
                    <td>{{ $diagnostico->fecha ? \Carbon\Carbon::parse($diagnostico->fecha)->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $diagnostico->paciente && $diagnostico->paciente->usuario ? ($diagnostico->paciente->usuario->nombre . ' ' . $diagnostico->paciente->usuario->apPaterno) : 'N/A' }}</td>
                    <td>{{ $diagnostico->medico && $diagnostico->medico->usuario ? ($diagnostico->medico->usuario->nombre . ' ' . $diagnostico->medico->usuario->apPaterno) : 'N/A' }}</td>
                    <td>{{ $diagnostico->catalogoDiagnostico ? $diagnostico->catalogoDiagnostico->descripcion_clinica : 'N/A' }}</td>
                    <td>{{ $diagnostico->descripcion ?: 'Sin descripción' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px; color: #6B7280;">
                        No se encontraron diagnósticos con los filtros seleccionados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Documento generado el {{ $fechaGeneracion }} | Sistema de Gestión Cardiovascular</p>
    </div>
</body>
</html>

