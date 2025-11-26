<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tratamiento extends Model
{
    use HasFactory;

    protected $table = 'tratamientos';
    protected $primaryKey = 'id_tratamiento';
    public $timestamps = true;

    protected $fillable = [
        'id_paciente',
        'id_medico',
        'id_diagnostico',
        'nombre',
        'dosis',
        'frecuencia',
        'duracion',
        'observaciones',
        'fecha_inicio',
        'activo',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'activo' => 'boolean',
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

    public function publicacionesForo()
    {
        return $this->hasMany(PublicacionForo::class, 'id_tratamiento', 'id_tratamiento');
    }
} 