<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalisisClinico extends Model
{
    use HasFactory;

    protected $table = 'analisis_clinicos';
    protected $primaryKey = 'id_analisis';
    public $timestamps = false;

    protected $fillable = [
        'id_paciente',
        'id_medico',
        'tipo_analisis',
        'descripcion',
        'resultado',
        'valores_cuantitativos',
        'observaciones_clinicas',
        'fecha',
        'estado',
    ];

    protected $casts = [
        'fecha' => 'date',
        'valores_cuantitativos' => 'array',
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
} 