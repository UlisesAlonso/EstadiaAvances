<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComentarioForo extends Model
{
    use HasFactory;

    protected $table = 'comentarios_foro';
    protected $primaryKey = 'id_comentario';
    public $timestamps = true;

    protected $fillable = [
        'id_publicacion',
        'id_paciente',
        'contenido',
        'fecha_comentario',
    ];

    protected $casts = [
        'fecha_comentario' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    public function publicacion()
    {
        return $this->belongsTo(PublicacionForo::class, 'id_publicacion', 'id_publicacion');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }

    // Scopes
    public function scopePorFecha($query, $orden = 'asc')
    {
        return $query->orderBy('fecha_comentario', $orden);
    }

    // Métodos helper
    public function puedeEditar($id_paciente)
    {
        return $this->id_paciente === $id_paciente;
    }
}
