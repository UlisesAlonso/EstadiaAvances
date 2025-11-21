<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pregunta extends Model
{
    use HasFactory;

    protected $table = 'preguntas';
    protected $primaryKey = 'id_pregunta';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'tipo',
        'opciones_multiple',
        'especialidad_medica',
        'fecha_asignacion',
        'id_diagnostico',
        'id_tratamiento',
        'id_paciente',
        'id_medico',
        'fecha_creacion',
        'activa',
    ];

    protected $casts = [
        'fecha_asignacion' => 'date',
        'fecha_creacion' => 'datetime',
        'opciones_multiple' => 'array',
        'activa' => 'boolean',
    ];

    // Relaciones
    public function respuestas()
    {
        return $this->hasMany(Respuesta::class, 'id_pregunta', 'id_pregunta');
    }

    public function diagnostico()
    {
        return $this->belongsTo(Diagnostico::class, 'id_diagnostico', 'id_diagnostico');
    }

    public function tratamiento()
    {
        return $this->belongsTo(Tratamiento::class, 'id_tratamiento', 'id_tratamiento');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'id_medico', 'id_medico');
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    public function scopePorPaciente($query, $idPaciente)
    {
        return $query->where(function($q) use ($idPaciente) {
            $q->where('id_paciente', $idPaciente)
              ->orWhereNull('id_paciente');
        });
    }

    public function scopePorEspecialidad($query, $especialidad)
    {
        return $query->where('especialidad_medica', $especialidad);
    }

    // Métodos helper
    public function esAbierta()
    {
        return $this->tipo === 'abierta';
    }

    public function esOpcionMultiple()
    {
        return $this->tipo === 'opcion_multiple';
    }

    public function tieneRespuestas()
    {
        return $this->respuestas()->count() > 0;
    }

    public function hasRespuestas()
    {
        return $this->tieneRespuestas();
    }

    /**
     * Verifica si un paciente específico ya respondió esta pregunta
     */
    public function hasRespuestaByUsuario($idUsuario)
    {
        return $this->respuestas()
                    ->where('id_usuario', $idUsuario)
                    ->exists();
    }

    /**
     * Obtiene la respuesta de un paciente específico
     */
    public function getRespuestaByUsuario($idUsuario)
    {
        return $this->respuestas()
                    ->where('id_usuario', $idUsuario)
                    ->first();
    }
} 