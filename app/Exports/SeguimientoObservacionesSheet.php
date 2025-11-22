<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;

class SeguimientoObservacionesSheet implements FromView, WithTitle
{
    protected $observaciones;

    public function __construct($observaciones)
    {
        $this->observaciones = $observaciones;
    }

    public function view(): View
    {
        return view('seguimiento.reporte-excel-observaciones', [
            'observaciones' => $this->observaciones
        ]);
    }

    public function title(): string
    {
        return 'Observaciones';
    }
}

