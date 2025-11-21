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
        'respuesta',
        'fecha_respuesta',
        'cumplimiento',
    ];

    protected $casts = [
        'fecha_respuesta' => 'datetime',
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

    // Scopes
    public function scopeCumplidas($query)
    {
        return $query->where('cumplimiento', true);
    }

    public function scopeNoCumplidas($query)
    {
        return $query->where('cumplimiento', false);
    }

    public function scopePorPaciente($query, $idUsuario)
    {
        return $query->where('id_usuario', $idUsuario);
    }
} 