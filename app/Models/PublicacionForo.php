<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicacionForo extends Model
{
    use HasFactory;

    protected $table = 'publicaciones_foro';
    protected $primaryKey = 'id_publicacion';
    public $timestamps = true;

    protected $fillable = [
        'id_paciente',
        'titulo',
        'contenido',
        'fecha_publicacion',
        'estado',
        'id_actividad',
        'id_tratamiento',
        'etiquetas',
    ];

    protected $casts = [
        'fecha_publicacion' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }

    public function actividad()
    {
        return $this->belongsTo(Actividad::class, 'id_actividad', 'id_actividad');
    }

    public function tratamiento()
    {
        return $this->belongsTo(Tratamiento::class, 'id_tratamiento', 'id_tratamiento');
    }

    public function comentarios()
    {
        return $this->hasMany(ComentarioForo::class, 'id_publicacion', 'id_publicacion')
                    ->orderBy('fecha_comentario', 'asc');
    }

    public function reacciones()
    {
        return $this->hasMany(ReaccionForo::class, 'id_publicacion', 'id_publicacion');
    }

    public function favoritos()
    {
        return $this->hasMany(FavoritoForo::class, 'id_publicacion', 'id_publicacion');
    }

    // Scopes
    public function scopeAprobadas($query)
    {
        return $query->where('estado', 'aprobada');
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeOcultas($query)
    {
        return $query->where('estado', 'oculta');
    }

    public function scopePorFecha($query, $orden = 'desc')
    {
        return $query->orderBy('fecha_publicacion', $orden);
    }

    public function scopePorRelevancia($query)
    {
        return $query->withCount(['reacciones as total_reacciones', 'comentarios as total_comentarios'])
                     ->orderByRaw('(total_reacciones * 2 + total_comentarios) DESC');
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where(function($q) use ($termino) {
            $q->where('titulo', 'like', "%{$termino}%")
              ->orWhere('contenido', 'like', "%{$termino}%")
              ->orWhere('etiquetas', 'like', "%{$termino}%");
        });
    }

    // Métodos helper
    public function getTotalReaccionesAttribute()
    {
        return $this->reacciones()->count();
    }

    public function getTotalComentariosAttribute()
    {
        return $this->comentarios()->count();
    }

    public function getTotalFavoritosAttribute()
    {
        return $this->favoritos()->count();
    }

    public function tieneReaccionDePaciente($id_paciente)
    {
        return $this->reacciones()->where('id_paciente', $id_paciente)->exists();
    }

    public function esFavoritoDePaciente($id_paciente)
    {
        return $this->favoritos()->where('id_paciente', $id_paciente)->exists();
    }

    public function puedeEditar($id_paciente)
    {
        return $this->id_paciente === $id_paciente;
    }

    public function estaAprobada()
    {
        return $this->estado === 'aprobada';
    }
}
