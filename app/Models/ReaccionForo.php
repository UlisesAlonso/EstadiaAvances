<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReaccionForo extends Model
{
    use HasFactory;

    protected $table = 'reacciones_foro';
    protected $primaryKey = 'id_reaccion';
    public $timestamps = true;

    protected $fillable = [
        'id_publicacion',
        'id_paciente',
        'tipo_reaccion',
    ];

    protected $casts = [
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
    public function scopeMeGusta($query)
    {
        return $query->where('tipo_reaccion', 'me_gusta');
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_reaccion', $tipo);
    }
}
