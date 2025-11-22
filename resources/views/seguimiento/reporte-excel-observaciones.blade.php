<table>
    <thead>
        <tr style="background-color: #2563eb; color: white;">
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Observación</th>
            <th>Médico</th>
        </tr>
    </thead>
    <tbody>
        @foreach($observaciones as $observacion)
        <tr>
            <td>{{ \Carbon\Carbon::parse($observacion->fecha_observacion)->format('d/m/Y') }}</td>
            <td>{{ $observacion->tipo ? ucfirst($observacion->tipo) : 'N/A' }}</td>
            <td>{{ $observacion->observacion }}</td>
            <td>{{ $observacion->medico ? $observacion->medico->usuario->nombre . ' ' . $observacion->medico->usuario->apPaterno : 'N/A' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

