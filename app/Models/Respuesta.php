<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Respuesta extends Model
{
    use HasFactory;

    protected $table = 'respuestas';
    protected $primaryKey = 'id_respuesta';
    public $timestamps = false;

    protected $fillable = [
        'id_pregunta',
        'id_usuario',
        'id_paciente',
        'respuesta',
        'fecha',
        'fecha_hora',
        'cumplimiento',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_hora' => 'datetime',
        'cumplimiento' => 'boolean',
    ];

    // Relaciones
    public function pregunta()
    {
        return $this->belongsTo(Pregunta::class, 'id_pregunta', 'id_pregunta');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }
} 