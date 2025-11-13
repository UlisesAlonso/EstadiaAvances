<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogoDiagnostico extends Model
{
    use HasFactory;

    protected $table = 'catalogo_diagnosticos';
    protected $primaryKey = 'id_diagnostico';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'descripcion_clinica',
        'categoria_medica',
        'id_usuario_creador',
        'id_usuario_modificador',
        'fecha_creacion',
        'fecha_modificacion',
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'fecha_modificacion' => 'datetime',
    ];

    // Relaciones
    public function usuarioCreador()
    {
        return $this->belongsTo(User::class, 'id_usuario_creador', 'id_usuario');
    }

    public function usuarioModificador()
    {
        return $this->belongsTo(User::class, 'id_usuario_modificador', 'id_usuario');
    }

    public function diagnosticos()
    {
        return $this->hasMany(Diagnostico::class, 'id_PDiag', 'id_diagnostico');
    }
}


