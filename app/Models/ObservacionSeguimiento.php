<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObservacionSeguimiento extends Model
{
    use HasFactory;

    protected $table = 'observaciones_seguimiento';
    protected $primaryKey = 'id_observacion';
    public $timestamps = false;

    protected $fillable = [
        'id_paciente',
        'id_medico',
        'observacion',
        'fecha_observacion',
        'tipo',
        'fecha_creacion',
    ];

    protected $casts = [
        'fecha_observacion' => 'date',
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
        return $query->where('tipo', $tipo);
    }

    public function scopePorFecha($query, $fechaDesde, $fechaHasta = null)
    {
        $query->whereDate('fecha_observacion', '>=', $fechaDesde);
        if ($fechaHasta) {
            $query->whereDate('fecha_observacion', '<=', $fechaHasta);
        }
        return $query;
    }

    public function scopeRecientes($query, $dias = 30)
    {
        return $query->where('fecha_observacion', '>=', now()->subDays($dias));
    }
}
