<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialClinico extends Model
{
    use HasFactory;

    protected $table = 'historial_clinico';
    protected $primaryKey = 'id_historial';
    public $timestamps = false;

    protected $fillable = [
        'id_paciente',
        'id_medico',
        'id_diagnostico',
        'id_tratamiento',
        'observaciones',
        'fecha_registro',
        'fecha_evento',
        'resultados_analisis',
        'archivos_adjuntos',
        'estado',
    ];

    protected $casts = [
        'fecha_registro' => 'date',
        'fecha_evento' => 'date',
        'archivos_adjuntos' => 'array',
    ];

    // Relaciones
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'id_medico', 'id_medico');
    }

    public function diagnostico()
    {
        return $this->belongsTo(Diagnostico::class, 'id_diagnostico', 'id_diagnostico');
    }

    public function tratamiento()
    {
        return $this->belongsTo(Tratamiento::class, 'id_tratamiento', 'id_tratamiento');
    }
} 