<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoritoForo extends Model
{
    use HasFactory;

    protected $table = 'favoritos_foro';
    protected $primaryKey = 'id_favorito';
    public $timestamps = true;

    protected $fillable = [
        'id_publicacion',
        'id_paciente',
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
}
