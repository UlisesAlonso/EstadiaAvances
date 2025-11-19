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
        'texto',
        'descripcion',
        'tipo',
        'categoria',
        'especialidad_medica',
        'fecha_asignacion',
        'id_diagnostico',
        'id_tratamiento',
        'id_paciente',
        'id_medico',
        'opciones',
        'estado',
        'fecha_creacion',
    ];

    protected $casts = [
        'fecha_asignacion' => 'date',
        'fecha_creacion' => 'datetime',
        'opciones' => 'array',
        'estado' => 'string',
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

    // Métodos helper
    public function puedeSerEliminada()
    {
        // No se puede eliminar si tiene respuestas consolidadas (más de 0 respuestas)
        return $this->respuestas()->count() === 0;
    }

    public function getTotalRespuestasAttribute()
    {
        return $this->respuestas()->count();
    }

    public function puedeResponder()
    {
        // Verificar si se puede responder (máximo 3 respuestas en total por pregunta)
        $totalRespuestas = $this->respuestas()->count();
        
        return $totalRespuestas < 3;
    }

    public function getRespuestasPaciente($idPaciente)
    {
        return $this->respuestas()
            ->where('id_paciente', $idPaciente)
            ->get();
    }
} 