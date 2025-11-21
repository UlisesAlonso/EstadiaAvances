<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analisis extends Model
{
    use HasFactory;

    protected $table = 'analisis';
    protected $primaryKey = 'id_analisis';
    public $timestamps = false;

    protected $fillable = [
        'tipo_estudio',
        'descripcion',
        'fecha_analisis',
        'id_paciente',
        'id_medico',
        'valores_obtenidos',
        'observaciones_clinicas',
        'fecha_creacion',
    ];

    protected $casts = [
        'fecha_analisis' => 'date',
        'fecha_creacion' => 'datetime',
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

    // Scopes
    public function scopePorPaciente($query, $idPaciente)
    {
        return $query->where('id_paciente', $idPaciente);
    }

    public function scopePorMedico($query, $idMedico)
    {
        return $query->where('id_medico', $idMedico);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_estudio', 'like', '%' . $tipo . '%');
    }

    public function scopePorFecha($query, $fechaDesde, $fechaHasta = null)
    {
        $query->whereDate('fecha_analisis', '>=', $fechaDesde);
        if ($fechaHasta) {
            $query->whereDate('fecha_analisis', '<=', $fechaHasta);
        }
        return $query;
    }
}

