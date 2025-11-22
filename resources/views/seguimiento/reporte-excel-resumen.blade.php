<table>
    <thead>
        <tr>
            <th colspan="2" style="background-color: #2563eb; color: white; font-size: 16px; font-weight: bold; padding: 10px;">
                Resumen de Seguimiento - {{ $paciente->usuario->nombre }} {{ $paciente->usuario->apPaterno }}
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Paciente:</strong></td>
            <td>{{ $paciente->usuario->nombre }} {{ $paciente->usuario->apPaterno }} {{ $paciente->usuario->apMaterno }}</td>
        </tr>
        <tr>
            <td><strong>Correo:</strong></td>
            <td>{{ $paciente->usuario->correo }}</td>
        </tr>
        @if($paciente->fecha_nacimiento)
        <tr>
            <td><strong>Fecha de Nacimiento:</strong></td>
            <td>{{ \Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td><strong>Edad:</strong></td>
            <td>{{ \Carbon\Carbon::parse($paciente->fecha_nacimiento)->age }} años</td>
        </tr>
        @endif
        <tr>
            <td colspan="2" style="background-color: #f3f4f6; font-weight: bold; padding: 10px;">Estadísticas</td>
        </tr>
        <tr>
            <td><strong>Total de Citas:</strong></td>
            <td>{{ $estadisticas['citas']['total'] }}</td>
        </tr>
        <tr>
            <td><strong>Citas Completadas:</strong></td>
            <td>{{ $estadisticas['citas']['completadas'] }}</td>
        </tr>
        <tr>
            <td><strong>Citas Pendientes:</strong></td>
            <td>{{ $estadisticas['citas']['pendientes'] }}</td>
        </tr>
        <tr>
            <td><strong>Citas Canceladas:</strong></td>
            <td>{{ $estadisticas['citas']['canceladas'] }}</td>
        </tr>
        <tr>
            <td><strong>% Cumplimiento Citas:</strong></td>
            <td>{{ $estadisticas['cumplimiento_citas'] }}%</td>
        </tr>
        <tr>
            <td><strong>Total Tratamientos:</strong></td>
            <td>{{ $estadisticas['tratamientos']['total'] }}</td>
        </tr>
        <tr>
            <td><strong>Tratamientos Activos:</strong></td>
            <td>{{ $estadisticas['tratamientos']['activos'] }}</td>
        </tr>
        <tr>
            <td><strong>Total Actividades:</strong></td>
            <td>{{ $estadisticas['actividades']['total'] }}</td>
        </tr>
        <tr>
            <td><strong>Actividades Completadas:</strong></td>
            <td>{{ $estadisticas['actividades']['completadas'] }}</td>
        </tr>
        <tr>
            <td><strong>% Cumplimiento Actividades:</strong></td>
            <td>{{ $estadisticas['cumplimiento_actividades'] }}%</td>
        </tr>
        <tr>
            <td><strong>Total Análisis:</strong></td>
            <td>{{ $estadisticas['analisis']['total'] }}</td>
        </tr>
    </tbody>
</table>

