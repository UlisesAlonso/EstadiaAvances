<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Tratamientos</title>
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
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            color: white;
            padding: 20px;
            margin-bottom: 20px;
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
        
        .clinic-info {
            font-size: 8pt;
            opacity: 0.85;
            margin-top: 5px;
        }
        
        .info-section {
            background: #F0FDF4;
            border: 1px solid #BBF7D0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px;
        }
        
        .info-item {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }
        
        .info-label {
            font-weight: bold;
            color: #065F46;
            font-size: 9pt;
            margin-bottom: 3px;
        }
        
        .info-value {
            color: #111827;
            font-size: 10pt;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border-collapse: separate;
            border-spacing: 12px;
        }
        
        .stat-card {
            display: table-cell;
            background: linear-gradient(135deg, #F0FDF4 0%, #D1FAE5 100%);
            border: 2px solid #10B981;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            width: 33.33%;
            vertical-align: middle;
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.1);
        }
        
        .stat-value {
            font-size: 32pt;
            font-weight: bold;
            color: #059669;
            margin-bottom: 8px;
            line-height: 1;
        }
        
        .stat-label {
            font-size: 10pt;
            color: #065F46;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .section-title {
            font-size: 16pt;
            font-weight: bold;
            color: #065F46;
            margin: 25px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 3px solid #10B981;
            text-align: center;
        }
        
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .stats-table thead {
            display: table-header-group;
        }
        
        .stats-table th {
            background: #10B981 !important;
            color: white !important;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10pt;
            border: 1px solid #059669;
            vertical-align: middle;
        }
        
        .stats-table th[style*="text-align: center"] {
            text-align: center !important;
        }
        
        .stats-table td {
            padding: 10px 12px;
            border: 1px solid #D1FAE5;
            font-size: 9pt;
        }
        
        .stats-table tr:nth-child(even) {
            background-color: #F0FDF4;
        }
        
        .stats-table tr:hover {
            background-color: #D1FAE5;
        }
        
        .highlight-number {
            font-weight: bold;
            color: #059669;
            font-size: 11pt;
        }
        
        .table-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        
        .table-title {
            font-size: 16pt;
            font-weight: bold;
            color: #065F46;
            margin-bottom: 15px;
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 3px solid #10B981;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        thead {
            display: table-header-group;
        }
        
        th, td {
            border: 1px solid #D1FAE5;
            padding: 10px 8px;
            text-align: left;
            font-size: 9pt;
        }
        
        th {
            background: #10B981 !important;
            color: white !important;
            font-weight: bold;
            text-align: center;
            font-size: 9pt;
            vertical-align: middle;
        }
        
        tr:nth-child(even) {
            background-color: #F0FDF4;
        }
        
        tr:hover {
            background-color: #D1FAE5;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 8pt;
            font-weight: bold;
            color: white;
            text-align: center;
        }
        
        .badge-activo {
            background-color: #10B981;
        }
        
        .badge-inactivo {
            background-color: #6B7280;
        }
        
        .summary-box {
            background: linear-gradient(135deg, #F0FDF4 0%, #D1FAE5 100%);
            border: 2px solid #10B981;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .summary-title {
            font-size: 14pt;
            font-weight: bold;
            color: #065F46;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 15px;
        }
        
        .summary-item {
            display: table-cell;
            text-align: center;
            vertical-align: top;
        }
        
        .summary-label {
            font-size: 8pt;
            color: #065F46;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .summary-value {
            font-size: 18pt;
            font-weight: bold;
            color: #059669;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            margin-top: 30px;
            padding: 10px 20px;
            border-top: 2px solid #D1FAE5;
            background: #F0FDF4;
            text-align: center;
            font-size: 8pt;
            color: #6B7280;
        }
        
        .footer-content {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px;
        }
        
        .footer-left {
            display: table-cell;
            text-align: left;
            vertical-align: middle;
            width: 40%;
        }
        
        .footer-center {
            display: table-cell;
            text-align: center;
            vertical-align: middle;
            width: 20%;
        }
        
        .footer-right {
            display: table-cell;
            text-align: right;
            vertical-align: middle;
            width: 40%;
        }
        
        .page-number {
            font-weight: bold;
            color: #10B981;
        }
        
        @page {
            margin: 100px 50px 80px 50px;
        }
        
        body {
            margin-bottom: 80px;
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
            <h1>REPORTE DE TRATAMIENTOS MÉDICOS</h1>
            <p>Sistema de Gestión Cardiovascular</p>
        </div>
    </div>

    <!-- Información del Reporte -->
    <div class="info-section">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Fecha de Generación:</div>
                <div class="info-value">{{ $fechaGeneracion }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Tipo de Análisis:</div>
                <div class="info-value">
                    @if($tipoDistribucion == 'estado')
                        Distribución por Estado
                    @elseif($tipoDistribucion == 'medico')
                        Distribución por Médico
                    @elseif($tipoDistribucion == 'paciente')
                        Distribución por Paciente
                    @else
                        Distribución por Frecuencia
                    @endif
                </div>
            </div>
        </div>
        @if($fechaDesde || $fechaHasta)
        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #BBF7D0;">
            <div class="info-label">Período de Inicio:</div>
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
        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #BBF7D0;">
            <div class="info-label">Médico Filtrado:</div>
            <div class="info-value">{{ $medicoFiltrado }}</div>
        </div>
        @endif
        @if($pacienteFiltrado)
        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #BBF7D0;">
            <div class="info-label">Paciente Filtrado:</div>
            <div class="info-value">{{ $pacienteFiltrado }}</div>
        </div>
        @endif
        @if($nombreTratamiento)
        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #BBF7D0;">
            <div class="info-label">Tratamiento Filtrado:</div>
            <div class="info-value">{{ $nombreTratamiento }}</div>
        </div>
        @endif
        @if($estado && $estado !== 'todos')
        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #BBF7D0;">
            <div class="info-label">Estado Filtrado:</div>
            <div class="info-value">{{ $estado == 'activo' ? 'Solo Activos' : 'Solo Inactivos' }}</div>
        </div>
        @endif
    </div>

    <!-- Estadísticas Generales -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $estadisticas['total'] }}</div>
            <div class="stat-label">Total de Tratamientos</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #10B981;">{{ $estadisticas['activos'] }}</div>
            <div class="stat-label">Tratamientos Activos</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #6B7280;">{{ $estadisticas['inactivos'] }}</div>
            <div class="stat-label">Tratamientos Inactivos</div>
        </div>
    </div>

    <!-- Estadísticas Detalladas según Tipo de Distribución -->
    @if(isset($estadisticasDetalladas) && !empty($estadisticasDetalladas['datos']))
    <div class="section-title">{{ $estadisticasDetalladas['titulo'] }}</div>
    <div style="background: #F0FDF4; border-left: 4px solid #10B981; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
        <p style="font-size: 9pt; color: #065F46; margin: 0;">
            <strong>¿Qué muestra esta tabla?</strong><br>
            @if($estadisticasDetalladas['tipo'] == 'estado')
                Esta tabla muestra cuántos tratamientos están <strong>activos</strong> (en curso) y cuántos están <strong>inactivos</strong> (finalizados), junto con el porcentaje que representa cada categoría del total de tratamientos.
            @elseif($estadisticasDetalladas['tipo'] == 'medico')
                Esta tabla muestra la cantidad de tratamientos prescritos por cada médico, incluyendo cuántos están activos e inactivos, y el porcentaje del total que representa cada médico.
            @elseif($estadisticasDetalladas['tipo'] == 'paciente')
                Esta tabla muestra cuántos tratamientos tiene cada paciente, incluyendo cuántos están activos e inactivos, y el porcentaje del total que representa cada paciente.
            @else
                Esta tabla muestra la distribución de tratamientos según su frecuencia de aplicación (por ejemplo: "1 vez al día", "2 veces al día"), incluyendo cuántos están activos e inactivos en cada frecuencia.
            @endif
        </p>
    </div>
    <table class="stats-table">
        <thead>
            <tr style="background: #10B981; color: white;">
                @if($estadisticasDetalladas['tipo'] == 'estado')
                    <th style="background: #10B981 !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #059669;">Estado</th>
                    <th style="background: #10B981 !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #059669; text-align: center;">Cantidad</th>
                    <th style="background: #10B981 !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #059669; text-align: center;">Porcentaje</th>
                @elseif($estadisticasDetalladas['tipo'] == 'medico')
                    <th style="background: #10B981 !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #059669;">Nombre del Médico</th>
                    <th style="background: #10B981 !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #059669;">Apellido del Médico</th>
                    <th style="background: #10B981 !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #059669; text-align: center;">Total</th>
                    <th style="background: #10B981 !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #059669; text-align: center;">Activos</th>
                    <th style="background: #10B981 !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #059669; text-align: center;">Inactivos</th>
                    <th style="background: #10B981 !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #059669; text-align: center;">Porcentaje</th>
                @elseif($estadisticasDetalladas['tipo'] == 'paciente')
                    <th style="background: #10B981 !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #059669;">Nombre del Paciente</th>
                    <th style="background: #10B981 !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #059669;">Apellido del Paciente</th>
                    <th style="background: #10B981 !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #059669; text-align: center;">Total</th>
                    <th style="background: #10B981 !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #059669; text-align: center;">Activos</th>
                    <th style="background: #10B981 !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #059669; text-align: center;">Inactivos</th>
                    <th style="background: #10B981 !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #059669; text-align: center;">Porcentaje</th>
                @else
                    <th style="background: #10B981 !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #059669;">Frecuencia</th>
                    <th style="background: #10B981 !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #059669; text-align: center;">Total</th>
                    <th style="background: #10B981 !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #059669; text-align: center;">Activos</th>
                    <th style="background: #10B981 !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #059669; text-align: center;">Inactivos</th>
                    <th style="background: #10B981 !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #059669; text-align: center;">Porcentaje</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($estadisticasDetalladas['datos'] as $dato)
            <tr>
                @if($estadisticasDetalladas['tipo'] == 'estado')
                    <td><strong>{{ $dato['label'] }}</strong></td>
                    <td style="text-align: center;" class="highlight-number">{{ $dato['cantidad'] }}</td>
                    <td style="text-align: center;" class="highlight-number">{{ $dato['porcentaje'] }}%</td>
                @elseif($estadisticasDetalladas['tipo'] == 'medico')
                    <td><strong>{{ $dato['nombre'] }}</strong></td>
                    <td>{{ $dato['apellido'] }}</td>
                    <td style="text-align: center;" class="highlight-number">{{ $dato['cantidad'] }}</td>
                    <td style="text-align: center;" class="highlight-number" style="color: #10B981;">{{ $dato['activos'] }}</td>
                    <td style="text-align: center;" class="highlight-number" style="color: #6B7280;">{{ $dato['inactivos'] }}</td>
                    <td style="text-align: center;" class="highlight-number">{{ $dato['porcentaje'] }}%</td>
                @elseif($estadisticasDetalladas['tipo'] == 'paciente')
                    <td><strong>{{ $dato['nombre'] }}</strong></td>
                    <td>{{ $dato['apellido'] }}</td>
                    <td style="text-align: center;" class="highlight-number">{{ $dato['cantidad'] }}</td>
                    <td style="text-align: center;" class="highlight-number" style="color: #10B981;">{{ $dato['activos'] }}</td>
                    <td style="text-align: center;" class="highlight-number" style="color: #6B7280;">{{ $dato['inactivos'] }}</td>
                    <td style="text-align: center;" class="highlight-number">{{ $dato['porcentaje'] }}%</td>
                @else
                    <td><strong>{{ $dato['label'] }}</strong></td>
                    <td style="text-align: center;" class="highlight-number">{{ $dato['cantidad'] }}</td>
                    <td style="text-align: center;" class="highlight-number" style="color: #10B981;">{{ $dato['activos'] }}</td>
                    <td style="text-align: center;" class="highlight-number" style="color: #6B7280;">{{ $dato['inactivos'] }}</td>
                    <td style="text-align: center;" class="highlight-number">{{ $dato['porcentaje'] }}%</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Tratamientos Más Comunes -->
    @if($tratamientosPorNombre->isNotEmpty())
    <div class="section-title">Tratamientos Más Prescritos (Top 10)</div>
    <div style="background: #F0FDF4; border-left: 4px solid #10B981; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
        <p style="font-size: 9pt; color: #065F46; margin: 0;">
            <strong>¿Qué muestra esta tabla?</strong><br>
            Esta tabla muestra los <strong>10 medicamentos o tratamientos más prescritos</strong> en el sistema. 
            Incluye el nombre del tratamiento, cuántas veces ha sido prescrito en total, y cuántos de esos tratamientos están actualmente activos o ya fueron finalizados (inactivos).
        </p>
    </div>
    <table class="stats-table">
        <thead>
            <tr style="background: #10B981; color: white;">
                <th style="background: #10B981 !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #059669;">Nombre del Tratamiento</th>
                <th style="background: #10B981 !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #059669; text-align: center;">Total</th>
                <th style="background: #10B981 !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #059669; text-align: center;">Activos</th>
                <th style="background: #10B981 !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #059669; text-align: center;">Inactivos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tratamientosPorNombre as $tratamiento)
            <tr>
                <td><strong>{{ $tratamiento['nombre'] }}</strong></td>
                <td style="text-align: center;" class="highlight-number">{{ $tratamiento['cantidad'] }}</td>
                <td style="text-align: center;" class="highlight-number" style="color: #10B981;">{{ $tratamiento['activos'] }}</td>
                <td style="text-align: center;" class="highlight-number" style="color: #6B7280;">{{ $tratamiento['inactivos'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Distribución por Duración -->
    @if($tratamientosPorDuracion->isNotEmpty())
    <div class="section-title">Distribución por Duración del Tratamiento</div>
    <div style="background: #F0FDF4; border-left: 4px solid #10B981; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
        <p style="font-size: 9pt; color: #065F46; margin: 0;">
            <strong>¿Qué muestra esta tabla?</strong><br>
            Esta tabla muestra cuántos tratamientos hay según su <strong>duración planificada</strong> (por ejemplo: "3 meses", "6 meses", "Indefinido"). 
            También indica cuántos tratamientos de cada duración están actualmente activos y cuántos ya fueron finalizados.
        </p>
    </div>
    <table class="stats-table">
        <thead>
            <tr style="background: #10B981; color: white;">
                <th style="background: #10B981 !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #059669;">Duración</th>
                <th style="background: #10B981 !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #059669; text-align: center;">Cantidad</th>
                <th style="background: #10B981 !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #059669; text-align: center;">Activos</th>
                <th style="background: #10B981 !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #059669; text-align: center;">Inactivos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tratamientosPorDuracion as $duracion)
            <tr>
                <td><strong>{{ $duracion['duracion'] }}</strong></td>
                <td style="text-align: center;" class="highlight-number">{{ $duracion['cantidad'] }}</td>
                <td style="text-align: center;" class="highlight-number" style="color: #10B981;">{{ $duracion['activos'] }}</td>
                <td style="text-align: center;" class="highlight-number" style="color: #6B7280;">{{ $duracion['inactivos'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Tabla Detallada de Tratamientos -->
    @if($tratamientos->count() > 0)
    <div class="table-section">
        <div class="table-title">Detalle Completo de Tratamientos</div>
        <div style="background: #F0FDF4; border-left: 4px solid #10B981; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
            <p style="font-size: 9pt; color: #065F46; margin: 0;">
                <strong>¿Qué muestra esta tabla?</strong><br>
                Esta tabla muestra <strong>todos los tratamientos individuales</strong> que cumplen con los filtros aplicados. 
                Para cada tratamiento se muestra: el paciente que lo recibe, el médico que lo prescribió, el nombre del medicamento, 
                la dosis, la frecuencia de aplicación, la duración planificada, la fecha de inicio y si está activo o finalizado.
            </p>
        </div>
        <table>
            <thead>
                <tr style="background: #10B981; color: white;">
                    <th style="width: 12%; background: #10B981 !important; color: white !important; font-weight: bold; padding: 10px 8px; border: 1px solid #059669; text-align: center;">Nombre del Paciente</th>
                    <th style="width: 12%; background: #10B981 !important; color: white !important; font-weight: bold; padding: 10px 8px; border: 1px solid #059669; text-align: center;">Apellido del Paciente</th>
                    <th style="width: 12%; background: #10B981 !important; color: white !important; font-weight: bold; padding: 10px 8px; border: 1px solid #059669; text-align: center;">Nombre del Médico</th>
                    <th style="width: 12%; background: #10B981 !important; color: white !important; font-weight: bold; padding: 10px 8px; border: 1px solid #059669; text-align: center;">Apellido del Médico</th>
                    <th style="width: 15%; background: #10B981 !important; color: white !important; font-weight: bold; padding: 10px 8px; border: 1px solid #059669; text-align: center;">Nombre del Tratamiento</th>
                    <th style="width: 10%; background: #10B981 !important; color: white !important; font-weight: bold; padding: 10px 8px; border: 1px solid #059669; text-align: center;">Dosis</th>
                    <th style="width: 10%; background: #10B981 !important; color: white !important; font-weight: bold; padding: 10px 8px; border: 1px solid #059669; text-align: center;">Frecuencia</th>
                    <th style="width: 8%; background: #10B981 !important; color: white !important; font-weight: bold; padding: 10px 8px; border: 1px solid #059669; text-align: center;">Duración</th>
                    <th style="width: 9%; background: #10B981 !important; color: white !important; font-weight: bold; padding: 10px 8px; border: 1px solid #059669; text-align: center;">Fecha de Inicio</th>
                    <th style="width: 10%; background: #10B981 !important; color: white !important; font-weight: bold; padding: 10px 8px; border: 1px solid #059669; text-align: center;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tratamientos as $tratamiento)
                    <tr>
                        <td>{{ $tratamiento->paciente->usuario->nombre }}</td>
                        <td>{{ $tratamiento->paciente->usuario->apPaterno }}</td>
                        <td>{{ $tratamiento->medico->usuario->nombre }}</td>
                        <td>{{ $tratamiento->medico->usuario->apPaterno }}</td>
                        <td><strong>{{ $tratamiento->nombre }}</strong></td>
                        <td>{{ $tratamiento->dosis }}</td>
                        <td>{{ $tratamiento->frecuencia }}</td>
                        <td>{{ $tratamiento->duracion }}</td>
                        <td style="text-align: center;">{{ $tratamiento->fecha_inicio->format('d/m/Y') }}</td>
                        <td style="text-align: center;">
                            <span class="badge badge-{{ $tratamiento->activo ? 'activo' : 'inactivo' }}">
                                {{ $tratamiento->activo ? 'Activo' : 'Inactivo' }}
                            </td>
                        </span>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="text-center" style="padding: 40px; color: #6B7280;">
        <p style="font-size: 12pt;">No se encontraron tratamientos con los filtros aplicados.</p>
    </div>
    @endif

    <!-- Pie de página -->
    <div class="footer">
        <div class="footer-content">
            <div class="footer-left">
                <div><strong>Cardio Vida</strong></div>
                <div>Sistema de Gestión Cardiovascular</div>
            </div>
            <div class="footer-center">
                <span class="page-number">
                    <script type="text/php">
                        if (isset($pdf)) {
                            $text = "Página {PAGENO} de {nbpg}";
                            $font = $fontMetrics->get_font("DejaVu Sans", "normal");
                            $size = 8;
                            $color = array(16, 185, 129);
                            $word_width = $fontMetrics->get_text_width("Página 1 de 1", $font, $size);
                            $y = $pdf->get_height() - 30;
                            $x = ($pdf->get_width() - $word_width) / 2;
                            $pdf->page_text($x, $y, $text, $font, $size, $color);
                        }
                    </script>
                </span>
            </div>
            <div class="footer-right">
                <div>Generado el {{ $fechaGeneracion }}</div>
                <div>Documento confidencial</div>
            </div>
        </div>
    </div>
</body>
</html>
