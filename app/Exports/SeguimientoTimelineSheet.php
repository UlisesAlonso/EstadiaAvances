<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;

class SeguimientoTimelineSheet implements FromView, WithTitle
{
    protected $datosConsolidados;

    public function __construct($datosConsolidados)
    {
        $this->datosConsolidados = $datosConsolidados;
    }

    public function view(): View
    {
        return view('seguimiento.reporte-excel-timeline', [
            'datosConsolidados' => $this->datosConsolidados
        ]);
    }

    public function title(): string
    {
        return 'Timeline';
    }
}

