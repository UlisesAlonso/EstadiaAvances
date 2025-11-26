<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Efectividad de Actividades</title>
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
            background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
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
            background: #FFFBEB;
            border: 1px solid #FDE68A;
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
            background: #FFFBEB;
            border: 2px solid #F59E0B;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            vertical-align: middle;
        }
        
        .stat-value {
            font-size: 20pt;
            font-weight: bold;
            color: #F59E0B;
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
            border-bottom: 2px solid #F59E0B;
            padding-bottom: 8px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        thead {
            background: #F59E0B;
            color: white;
        }
        
        th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #D97706;
        }
        
        td {
            padding: 8px 10px;
            border: 1px solid #E5E7EB;
        }
        
        tbody tr:nth-child(even) {
            background: #F9FAFB;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: bold;
        }
        
        .badge-completada {
            background: #D1FAE5;
            color: #065F46;
        }
        
        .badge-pendiente {
            background: #FEF3C7;
            color: #92400E;
        }
        
        .badge-vencida {
            background: #FEE2E2;
            color: #991B1B;
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
            <h1>REPORTE DE EFECTIVIDAD DE ACTIVIDADES</h1>
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
    </div>

    <!-- Estadísticas Generales -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $estadisticas['total'] }}</div>
            <div class="stat-label">Total de Actividades</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $estadisticas['completadas'] }}</div>
            <div class="stat-label">Completadas</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $estadisticas['pendientes'] }}</div>
            <div class="stat-label">Pendientes</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $estadisticas['tasa_completitud'] }}%</div>
            <div class="stat-label">Tasa de Completitud</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $estadisticas['tasa_cumplimiento'] }}%</div>
            <div class="stat-label">Tasa de Cumplimiento</div>
        </div>
    </div>

    <!-- Estadísticas por Nombre de Actividad -->
    @if($actividadesPorNombre->count() > 0)
    <div class="table-section">
        <div class="table-title">Efectividad por Nombre de Actividad (Top 10)</div>
        <table>
            <thead>
                <tr>
                    <th>Nombre de Actividad</th>
                    <th>Total</th>
                    <th>Completadas</th>
                    <th>Pendientes</th>
                    <th>Tasa de Completitud</th>
                </tr>
            </thead>
            <tbody>
                @foreach($actividadesPorNombre as $actividad)
                <tr>
                    <td>{{ $actividad['nombre'] }}</td>
                    <td>{{ $actividad['total'] }}</td>
                    <td>{{ $actividad['completadas'] }}</td>
                    <td>{{ $actividad['pendientes'] }}</td>
                    <td><strong>{{ $actividad['tasa_completitud'] }}%</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Estadísticas por Paciente -->
    @if($actividadesPorPaciente->count() > 0)
    <div class="table-section">
        <div class="table-title">Efectividad por Paciente (Top 10)</div>
        <table>
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>Total</th>
                    <th>Completadas</th>
                    <th>Tasa de Completitud</th>
                </tr>
            </thead>
            <tbody>
                @foreach($actividadesPorPaciente as $item)
                <tr>
                    <td>{{ $item['nombre'] }}</td>
                    <td>{{ $item['total'] }}</td>
                    <td>{{ $item['completadas'] }}</td>
                    <td><strong>{{ $item['tasa_completitud'] }}%</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Lista de Actividades -->
    <div class="table-section">
        <div class="table-title">Lista de Actividades</div>
        <table>
            <thead>
                <tr>
                    <th>Fecha Asignación</th>
                    <th>Fecha Límite</th>
                    <th>Paciente</th>
                    <th>Nombre</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($actividades as $actividad)
                <tr>
                    <td>{{ $actividad->fecha_asignacion ? \Carbon\Carbon::parse($actividad->fecha_asignacion)->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $actividad->fecha_limite ? \Carbon\Carbon::parse($actividad->fecha_limite)->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $actividad->paciente && $actividad->paciente->usuario ? ($actividad->paciente->usuario->nombre . ' ' . $actividad->paciente->usuario->apPaterno) : 'N/A' }}</td>
                    <td>{{ $actividad->nombre }}</td>
                    <td>
                        @if($actividad->completada)
                            <span class="badge badge-completada">Completada</span>
                        @elseif($actividad->fecha_limite && \Carbon\Carbon::parse($actividad->fecha_limite)->isPast())
                            <span class="badge badge-vencida">Vencida</span>
                        @else
                            <span class="badge badge-pendiente">Pendiente</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px; color: #6B7280;">
                        No se encontraron actividades con los filtros seleccionados.
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

