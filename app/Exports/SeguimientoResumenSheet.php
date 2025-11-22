<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;

class SeguimientoResumenSheet implements FromView, WithTitle
{
    protected $paciente;
    protected $estadisticas;

    public function __construct($paciente, $estadisticas)
    {
        $this->paciente = $paciente;
        $this->estadisticas = $estadisticas;
    }

    public function view(): View
    {
        return view('seguimiento.reporte-excel-resumen', [
            'paciente' => $this->paciente,
            'estadisticas' => $this->estadisticas
        ]);
    }

    public function title(): string
    {
        return 'Resumen';
    }
}

