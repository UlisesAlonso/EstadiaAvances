<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\Paciente;

class SeguimientoExport implements WithMultipleSheets
{
    protected $paciente;
    protected $datosConsolidados;
    protected $estadisticas;
    protected $observaciones;

    public function __construct($paciente, $datosConsolidados, $estadisticas, $observaciones)
    {
        $this->paciente = $paciente;
        $this->datosConsolidados = $datosConsolidados;
        $this->estadisticas = $estadisticas;
        $this->observaciones = $observaciones;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        // Hoja 1: Resumen
        $sheets[] = new SeguimientoResumenSheet($this->paciente, $this->estadisticas);

        // Hoja 2: Timeline
        $sheets[] = new SeguimientoTimelineSheet($this->datosConsolidados);

        // Hoja 3: Observaciones
        $sheets[] = new SeguimientoObservacionesSheet($this->observaciones);

        return $sheets;
    }
}

