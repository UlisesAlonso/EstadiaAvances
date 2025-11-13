<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    use HasFactory;

    protected $table = 'actividades';
    protected $primaryKey = 'id_actividad';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'descripcion',
        'instrucciones',
        'fecha_asignacion',
        'fecha_limite',
        'periodicidad',
        'id_paciente',
        'id_medico',
        'completada',
        'comentarios_paciente',
        'comentarios_medico',
    ];

    protected $casts = [
        'fecha_asignacion' => 'date',
        'fecha_limite' => 'date',
        'completada' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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

    public function historialActividades()
    {
        return $this->hasMany(HistorialActividad::class, 'id_actividad', 'id_actividad');
    }
} 