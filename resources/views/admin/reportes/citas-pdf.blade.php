<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Citas</title>
    <style>
        .logo {
        align-items: left;
        position: absolute;
        left: 15px;
        top: 10px;
            max-width: 80px;
            max-height: 80px;
            display: block;
        }
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
            background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
            color: #3B82F6;
            padding: 20px;
            margin-bottom: 15px;
            width: 100%;
            text-align: center;
        }
        
        .header-center {
            width: 100%;
            text-align: center;
        }
        
        .header h1 {
          
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
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
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
            margin-bottom: 15px;
            border-collapse: separate;
            border-spacing: 8px;
        }
        
        .stat-card {
            display: table-cell;
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
            width: 25%;
            vertical-align: top;
        }
        
        .stat-value {
            font-size: 20pt;
            font-weight: bold;
            color: #3B82F6;
            margin-bottom: 3px;
        }
        
        .stat-label {
            font-size: 8pt;
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
            border-bottom: 2px solid #3B82F6;
            padding-bottom: 5px;
        }
        
        .chart-container {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .chart-visual {
            display: table-cell;
            width: 50%;
            vertical-align: middle;
            text-align: center;
        }
        
        .chart-legend {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-left: 15px;
        }
        
        .pie-chart {
            width: 250px;
            height: 250px;
            border-radius: 50%;
            margin: 0 auto;
            position: relative;
            background: #E5E7EB;
            border: 3px solid white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .pie-segment {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            clip-path: polygon(50% 50%, 50% 0%, 100% 0%, 100% 100%, 50% 100%);
        }
        
        .bar-chart {
            margin-top: 15px;
        }
        
        .bar-item {
            margin-bottom: 10px;
        }
        
        .bar-label {
            font-size: 8pt;
            font-weight: bold;
            color: #111827;
            margin-bottom: 4px;
        }
        
        .bar-container {
            background: #E5E7EB;
            height: 20px;
            border-radius: 3px;
            position: relative;
            overflow: hidden;
        }
        
        .bar-fill {
            height: 100%;
            border-radius: 3px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 6px;
            color: white;
            font-weight: bold;
            font-size: 7pt;
        }
        
        .legend-item {
            margin-bottom: 12px;
            display: table;
            width: 100%;
        }
        
        .legend-color {
            display: table-cell;
            width: 20px;
            height: 20px;
            border-radius: 4px;
            vertical-align: middle;
        }
        
        .legend-text {
            display: table-cell;
            padding-left: 10px;
            vertical-align: middle;
            font-size: 10pt;
        }
        
        .legend-label {
            font-weight: bold;
            color: #111827;
        }
        
        .legend-value {
            color: #6B7280;
            font-size: 9pt;
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
            border-bottom: 2px solid #3B82F6;
            padding-bottom: 8px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9pt;
        }
        
        thead {
            background: #3B82F6;
            color: white;
        }
        
        th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #2563EB;
        }
        
        td {
            padding: 8px 10px;
            border: 1px solid #E5E7EB;
        }
        
        tbody tr:nth-child(even) {
            background: #F9FAFB;
        }
        
        tbody tr:hover {
            background: #F3F4F6;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .badge-pendiente {
            background: #FEF3C7;
            color: #92400E;
        }
        
        .badge-confirmada {
            background: #DBEAFE;
            color: #1E40AF;
        }
        
        .badge-completada {
            background: #D1FAE5;
            color: #065F46;
        }
        
        .badge-cancelada {
            background: #FEE2E2;
            color: #991B1B;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            margin-top: 40px;
            padding: 10px 20px;
            border-top: 2px solid #E5E7EB;
            background: #F9FAFB;
            text-align: center;
            color: #6B7280;
            font-size: 8pt;
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
            color: #3B82F6;
        }
        
        @page {
            margin: 100px 50px 80px 50px;
        }
        
        body {
            margin-bottom: 80px;
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
    </style>
</head>
<body>
    <!-- Encabezado -->
    <div class="header">
       @if(isset($logo) && $logo)
            <img src="{{ $logo }}" alt="Cardio Vida" class="logo">
        @endif
        <div class="header-center">
            <h1>REPORTE DE CITAS MÉDICAS</h1>
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
        <div class="info-row">
            <div class="info-label">Tipo de Distribución:</div>
            <div class="info-value">
                @if($tipoDistribucion == 'estado')
                    Por Estado
                @elseif($tipoDistribucion == 'medico')
                    Por Médico
                @else
                    Por Especialidad
                @endif
            </div>
        </div>
    </div>

    <!-- Estadísticas Generales -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $estadisticas['total'] }}</div>
            <div class="stat-label">Total de Citas</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #F59E0B;">{{ $estadisticas['pendientes'] }}</div>
            <div class="stat-label">Pendientes</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #10B981;">{{ $estadisticas['completadas'] }}</div>
            <div class="stat-label">Completadas</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #EF4444;">{{ $estadisticas['canceladas'] }}</div>
            <div class="stat-label">Canceladas</div>
        </div>
    </div>

    <!-- Gráfica de Pastel -->
    @if(count($datosGrafica) > 0)
    <div class="chart-section">
        <div class="chart-title">{{ $tituloGrafica }}</div>
        <div class="chart-container">
            <div class="chart-visual">
                <!-- Gráfica de Pastel usando técnica de círculos segmentados -->
                <div style="text-align: center; margin-bottom: 20px;">
                    @php
                        $total = array_sum(array_column($datosGrafica, 'cantidad'));
                        $size = 200;
                        $center = $size / 2;
                        $radius = $size / 2;
                    @endphp
                    
                    @if(isset($imagenGrafica) && $imagenGrafica)
                        <!-- Imagen generada con GD -->
                        <img src="{{ $imagenGrafica }}" alt="Gráfica de Pastel" style="max-width: 300px; height: auto; margin: 0 auto; display: block;" />
                    @else
                        <!-- Gráfica de Pastel usando SVG (compatible con DomPDF) -->
                        <svg width="250" height="250" style="margin: 0 auto; display: block;">
                            @php
                                $centerX = 125;
                                $centerY = 125;
                                $outerRadius = 100;
                                $innerRadius = 60;
                                $currentAngle = -90; // Empezar desde arriba
                            @endphp
                            
                            @foreach($datosGrafica as $index => $dato)
                                @php
                                    $color = $colores[$index % count($colores)];
                                    $percentage = $dato['porcentaje'] / 100;
                                    $angle = $percentage * 360;
                                    $startAngle = $currentAngle;
                                    $endAngle = $currentAngle + $angle;
                                    
                                    // Convertir ángulos a radianes
                                    $startRad = deg2rad($startAngle);
                                    $endRad = deg2rad($endAngle);
                                    
                                    // Calcular puntos del arco exterior
                                    $x1 = $centerX + $outerRadius * cos($startRad);
                                    $y1 = $centerY + $outerRadius * sin($startRad);
                                    $x2 = $centerX + $outerRadius * cos($endRad);
                                    $y2 = $centerY + $outerRadius * sin($endRad);
                                    
                                    // Calcular puntos del arco interior
                                    $x3 = $centerX + $innerRadius * cos($endRad);
                                    $y3 = $centerY + $innerRadius * sin($endRad);
                                    $x4 = $centerX + $innerRadius * cos($startRad);
                                    $y4 = $centerY + $innerRadius * sin($startRad);
                                    
                                    // Determinar si el arco es grande (más de 180 grados)
                                    $largeArc = $angle > 180 ? 1 : 0;
                                    
                                    // Crear el path del segmento (donut slice)
                                    $path = "M $x1 $y1 " . 
                                            "A $outerRadius $outerRadius 0 $largeArc 1 $x2 $y2 " . 
                                            "L $x3 $y3 " . 
                                            "A $innerRadius $innerRadius 0 $largeArc 0 $x4 $y4 " . 
                                            "Z";
                                @endphp
                                
                                <!-- Segmento del pastel -->
                                <path d="{{ $path }}" 
                                      fill="{{ $color }}" 
                                      stroke="white" 
                                      stroke-width="2"/>
                                
                                @php
                                    $currentAngle = $endAngle;
                                @endphp
                            @endforeach
                            
                            <!-- Círculo central blanco -->
                            <circle cx="{{ $centerX }}" cy="{{ $centerY }}" r="{{ $innerRadius }}" fill="white" stroke="#E5E7EB" stroke-width="3"/>
                            
                            <!-- Texto del total -->
                            <text x="{{ $centerX }}" y="{{ $centerY - 5 }}" text-anchor="middle" font-size="20" font-weight="bold" fill="#3B82F6">
                                {{ $total }}
                            </text>
                            <text x="{{ $centerX }}" y="{{ $centerY + 15 }}" text-anchor="middle" font-size="12" fill="#6B7280">
                                Total
                            </text>
                        </svg>
                    @endif
                </div>
            </div>
            <div class="chart-legend">
                @foreach($datosGrafica as $index => $dato)
                <div class="legend-item">
                    <div class="legend-color" style="background-color: {{ $colores[$index % count($colores)] }};"></div>
                    <div class="legend-text">
                        <div class="legend-label">{{ $dato['label'] }}</div>
                        <div class="legend-value">
                            {{ $dato['cantidad'] }} citas ({{ $dato['porcentaje'] }}%)
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Gráfica de Barras Horizontal -->
        <div class="bar-chart">
            @foreach($datosGrafica as $index => $dato)
            <div class="bar-item">
                <div class="bar-label">{{ $dato['label'] }} - {{ $dato['cantidad'] }} citas ({{ $dato['porcentaje'] }}%)</div>
                <div class="bar-container">
                    <div class="bar-fill" style="background-color: {{ $colores[$index % count($colores)] }}; width: {{ $dato['porcentaje'] }}%;">
                        {{ $dato['porcentaje'] }}%
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Tabla de Citas -->
    @if($citas->count() > 0)
    <div class="table-section">
        <div class="table-title">Detalle de Citas</div>
        <table>
            <thead>
                <tr>
                    <th>Fecha y Hora</th>
                    <th>Paciente</th>
                    <th>Médico</th>
                    <th>Especialidad</th>
                    <th>Motivo</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($citas as $cita)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($cita->paciente && $cita->paciente->usuario)
                            {{ $cita->paciente->usuario->nombre }} {{ $cita->paciente->usuario->apPaterno }} {{ $cita->paciente->usuario->apMaterno }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if($cita->medico && $cita->medico->usuario)
                            {{ $cita->medico->usuario->nombre }} {{ $cita->medico->usuario->apPaterno }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $cita->especialidad_medica ?: 'N/A' }}</td>
                    <td>{{ Str::limit($cita->motivo, 50) }}</td>
                    <td>
                        <span class="badge badge-{{ $cita->estado }}">
                            {{ $cita->estado_formateado }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="text-center" style="padding: 40px; color: #6B7280;">
        <p>No se encontraron citas con los filtros seleccionados.</p>
    </div>
    @endif

    <!-- Pie de Página -->
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
                            $color = array(59, 130, 246);
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

