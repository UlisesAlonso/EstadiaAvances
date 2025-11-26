<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Seguimiento</title>
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
            background: linear-gradient(135deg, #9333EA 0%, #7C3AED 100%);
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
            background: #FAF5FF;
            border: 1px solid #E9D5FF;
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
            color: #6B21A8;
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
            background: linear-gradient(135deg, #FAF5FF 0%, #F3E8FF 100%);
            border: 2px solid #9333EA;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            width: 100%;
            vertical-align: middle;
            box-shadow: 0 2px 4px rgba(147, 51, 234, 0.1);
        }
        
        .stat-value {
            font-size: 32pt;
            font-weight: bold;
            color: #7C3AED;
            margin-bottom: 8px;
            line-height: 1;
        }
        
        .stat-label {
            font-size: 10pt;
            color: #6B21A8;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .section-title {
            font-size: 16pt;
            font-weight: bold;
            color: #6B21A8;
            margin: 25px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 3px solid #9333EA;
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
            background: #9333EA !important;
            color: white !important;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10pt;
            border: 1px solid #7C3AED;
            vertical-align: middle;
        }
        
        .stats-table th[style*="text-align: center"] {
            text-align: center !important;
        }
        
        .stats-table td {
            padding: 10px 12px;
            border: 1px solid #E9D5FF;
            font-size: 9pt;
        }
        
        .stats-table tr:nth-child(even) {
            background-color: #FAF5FF;
        }
        
        .stats-table tr:hover {
            background-color: #F3E8FF;
        }
        
        .highlight-number {
            font-weight: bold;
            color: #7C3AED;
            font-size: 11pt;
        }
        
        .table-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        
        .table-title {
            font-size: 16pt;
            font-weight: bold;
            color: #6B21A8;
            margin-bottom: 15px;
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 3px solid #9333EA;
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
            border: 1px solid #E9D5FF;
            padding: 10px 8px;
            text-align: left;
            font-size: 9pt;
        }
        
        th {
            background: #9333EA !important;
            color: white !important;
            font-weight: bold;
            text-align: center;
            font-size: 9pt;
            vertical-align: middle;
        }
        
        tr:nth-child(even) {
            background-color: #FAF5FF;
        }
        
        tr:hover {
            background-color: #F3E8FF;
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
            border-top: 2px solid #E9D5FF;
            background: #FAF5FF;
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
            color: #9333EA;
        }
        
        @page {
            margin: 100px 50px 80px 50px;
        }
        
        body {
            margin-bottom: 80px;
        }
        
        .observacion-text {
            max-width: 300px;
            word-wrap: break-word;
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
            <h1>REPORTE DE SEGUIMIENTO DE PACIENTES</h1>
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
                    @if($tipoDistribucion == 'tipo')
                        Distribución por Tipo de Observación
                    @elseif($tipoDistribucion == 'medico')
                        Distribución por Médico
                    @else
                        Distribución por Paciente
                    @endif
                </div>
            </div>
        </div>
        @if($fechaDesde || $fechaHasta)
        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #E9D5FF;">
            <div class="info-label">Período de Observación:</div>
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
        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #E9D5FF;">
            <div class="info-label">Médico Filtrado:</div>
            <div class="info-value">{{ $medicoFiltrado }}</div>
        </div>
        @endif
        @if($pacienteFiltrado)
        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #E9D5FF;">
            <div class="info-label">Paciente Filtrado:</div>
            <div class="info-value">{{ $pacienteFiltrado }}</div>
        </div>
        @endif
        @if($tipoObservacion)
        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #E9D5FF;">
            <div class="info-label">Tipo de Observación Filtrado:</div>
            <div class="info-value">{{ ucfirst($tipoObservacion) }}</div>
        </div>
        @endif
    </div>

    <!-- Estadísticas Generales -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $estadisticas['total'] }}</div>
            <div class="stat-label">Total de Observaciones</div>
        </div>
    </div>

    <!-- Estadísticas Detalladas según Tipo de Distribución -->
    @if(isset($estadisticasDetalladas) && !empty($estadisticasDetalladas['datos']))
    <div class="section-title">{{ $estadisticasDetalladas['titulo'] }}</div>
    <div style="background: #FAF5FF; border-left: 4px solid #9333EA; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
        <p style="font-size: 9pt; color: #6B21A8; margin: 0;">
            <strong>¿Qué muestra esta tabla?</strong><br>
            @if($estadisticasDetalladas['tipo'] == 'tipo')
                Esta tabla muestra cuántas observaciones hay de cada tipo (por ejemplo: general, evolución, alerta), junto con el porcentaje que representa cada tipo del total de observaciones.
            @elseif($estadisticasDetalladas['tipo'] == 'medico')
                Esta tabla muestra la cantidad de observaciones realizadas por cada médico, incluyendo el porcentaje del total que representa cada médico.
            @else
                Esta tabla muestra cuántas observaciones tiene cada paciente, incluyendo el porcentaje del total que representa cada paciente.
            @endif
        </p>
    </div>
    <table class="stats-table">
        <thead>
            <tr style="background: #9333EA; color: white;">
                @if($estadisticasDetalladas['tipo'] == 'tipo')
                    <th style="background: #9333EA !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #7C3AED;">Tipo de Observación</th>
                    <th style="background: #9333EA !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #7C3AED; text-align: center;">Cantidad</th>
                    <th style="background: #9333EA !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #7C3AED; text-align: center;">Porcentaje</th>
                @elseif($estadisticasDetalladas['tipo'] == 'medico')
                    <th style="background: #9333EA !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #7C3AED;">Nombre del Médico</th>
                    <th style="background: #9333EA !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #7C3AED;">Apellido del Médico</th>
                    <th style="background: #9333EA !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #7C3AED; text-align: center;">Total</th>
                    <th style="background: #9333EA !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #7C3AED; text-align: center;">Porcentaje</th>
                @else
                    <th style="background: #9333EA !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #7C3AED;">Nombre del Paciente</th>
                    <th style="background: #9333EA !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #7C3AED;">Apellido del Paciente</th>
                    <th style="background: #9333EA !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #7C3AED; text-align: center;">Total</th>
                    <th style="background: #9333EA !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #7C3AED; text-align: center;">Porcentaje</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($estadisticasDetalladas['datos'] as $dato)
            <tr>
                @if($estadisticasDetalladas['tipo'] == 'tipo')
                    <td><strong>{{ $dato['label'] }}</strong></td>
                    <td style="text-align: center;" class="highlight-number">{{ $dato['cantidad'] }}</td>
                    <td style="text-align: center;" class="highlight-number">{{ $dato['porcentaje'] }}%</td>
                @elseif($estadisticasDetalladas['tipo'] == 'medico')
                    <td><strong>{{ $dato['nombre'] }}</strong></td>
                    <td>{{ $dato['apellido'] }}</td>
                    <td style="text-align: center;" class="highlight-number">{{ $dato['cantidad'] }}</td>
                    <td style="text-align: center;" class="highlight-number">{{ $dato['porcentaje'] }}%</td>
                @else
                    <td><strong>{{ $dato['nombre'] }}</strong></td>
                    <td>{{ $dato['apellido'] }}</td>
                    <td style="text-align: center;" class="highlight-number">{{ $dato['cantidad'] }}</td>
                    <td style="text-align: center;" class="highlight-number">{{ $dato['porcentaje'] }}%</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Distribución por Tipo de Observación -->
    @if($observacionesPorTipo->isNotEmpty())
    <div class="section-title">Distribución por Tipo de Observación</div>
    <div style="background: #FAF5FF; border-left: 4px solid #9333EA; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
        <p style="font-size: 9pt; color: #6B21A8; margin: 0;">
            <strong>¿Qué muestra esta tabla?</strong><br>
            Esta tabla muestra cuántas observaciones hay de cada tipo (por ejemplo: general, evolución, alerta). 
            Esto ayuda a entender qué tipos de observaciones se registran con más frecuencia en el seguimiento de pacientes.
        </p>
    </div>
    <table class="stats-table">
        <thead>
            <tr style="background: #9333EA; color: white;">
                <th style="background: #9333EA !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #7C3AED;">Tipo de Observación</th>
                <th style="background: #9333EA !important; color: white !important; font-weight: bold; padding: 12px 8px; border: 1px solid #7C3AED; text-align: center;">Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($observacionesPorTipo as $tipo)
            <tr>
                <td><strong>{{ $tipo['tipo'] }}</strong></td>
                <td style="text-align: center;" class="highlight-number">{{ $tipo['cantidad'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Tabla Detallada de Observaciones -->
    @if($observaciones->count() > 0)
    <div class="table-section">
        <div class="table-title">Detalle Completo de Observaciones de Seguimiento</div>
        <div style="background: #FAF5FF; border-left: 4px solid #9333EA; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
            <p style="font-size: 9pt; color: #6B21A8; margin: 0;">
                <strong>¿Qué muestra esta tabla?</strong><br>
                Esta tabla muestra <strong>todas las observaciones de seguimiento</strong> que cumplen con los filtros aplicados. 
                Para cada observación se muestra: el paciente, el médico que la realizó, la fecha de la observación, 
                el tipo de observación y el contenido completo de la observación médica.
            </p>
        </div>
        <table>
            <thead>
                <tr style="background: #9333EA; color: white;">
                    <th style="width: 10%; background: #9333EA !important; color: white !important; font-weight: bold; padding: 10px 8px; border: 1px solid #7C3AED; text-align: center;">Nombre del Paciente</th>
                    <th style="width: 10%; background: #9333EA !important; color: white !important; font-weight: bold; padding: 10px 8px; border: 1px solid #7C3AED; text-align: center;">Apellido del Paciente</th>
                    <th style="width: 10%; background: #9333EA !important; color: white !important; font-weight: bold; padding: 10px 8px; border: 1px solid #7C3AED; text-align: center;">Nombre del Médico</th>
                    <th style="width: 10%; background: #9333EA !important; color: white !important; font-weight: bold; padding: 10px 8px; border: 1px solid #7C3AED; text-align: center;">Apellido del Médico</th>
                    <th style="width: 10%; background: #9333EA !important; color: white !important; font-weight: bold; padding: 10px 8px; border: 1px solid #7C3AED; text-align: center;">Fecha de Observación</th>
                    <th style="width: 10%; background: #9333EA !important; color: white !important; font-weight: bold; padding: 10px 8px; border: 1px solid #7C3AED; text-align: center;">Tipo</th>
                    <th style="width: 30%; background: #9333EA !important; color: white !important; font-weight: bold; padding: 10px 8px; border: 1px solid #7C3AED; text-align: center;">Observación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($observaciones as $observacion)
                    <tr>
                        <td>{{ $observacion->paciente->usuario->nombre }}</td>
                        <td>{{ $observacion->paciente->usuario->apPaterno }}</td>
                        <td>{{ $observacion->medico->usuario->nombre }}</td>
                        <td>{{ $observacion->medico->usuario->apPaterno }}</td>
                        <td style="text-align: center;">{{ $observacion->fecha_observacion->format('d/m/Y') }}</td>
                        <td style="text-align: center;">{{ $observacion->tipo ? ucfirst($observacion->tipo) : 'Sin tipo' }}</td>
                        <td class="observacion-text">{{ $observacion->observacion }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="text-center" style="padding: 40px; color: #6B7280;">
        <p style="font-size: 12pt;">No se encontraron observaciones de seguimiento con los filtros aplicados.</p>
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
                            $color = array(147, 51, 234);
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

