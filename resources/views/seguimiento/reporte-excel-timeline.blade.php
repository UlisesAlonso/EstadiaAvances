<table>
    <thead>
        <tr style="background-color: #2563eb; color: white;">
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Título</th>
            <th>Descripción</th>
            <th>Estado</th>
            <th>Médico</th>
        </tr>
    </thead>
    <tbody>
        @foreach($datosConsolidados['timeline'] as $evento)
        <tr>
            <td>{{ \Carbon\Carbon::parse($evento['fecha'])->format('d/m/Y') }}</td>
            <td>{{ ucfirst($evento['tipo']) }}</td>
            <td>{{ $evento['titulo'] }}</td>
            <td>{{ $evento['descripcion'] }}</td>
            <td>{{ isset($evento['estado']) ? ucfirst($evento['estado']) : 'N/A' }}</td>
            <td>{{ isset($evento['medico']) ? $evento['medico'] : 'N/A' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

