<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tratamiento extends Model
{
    use HasFactory;

    protected $table = 'tratamientos';
    protected $primaryKey = 'id_tratamiento';
<<<<<<< HEAD
    public $timestamps = true;
=======
    public $timestamps = false;
>>>>>>> d72736d2d2666449d7a4d7da99acaf587a6c4dd8

    protected $fillable = [
        'id_paciente',
        'id_medico',
<<<<<<< HEAD
        'id_diagnostico',
=======
>>>>>>> d72736d2d2666449d7a4d7da99acaf587a6c4dd8
        'nombre',
        'dosis',
        'frecuencia',
        'duracion',
        'observaciones',
        'fecha_inicio',
        'activo',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'activo' => 'boolean',
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

    public function diagnostico()
    {
        return $this->belongsTo(Diagnostico::class, 'id_diagnostico', 'id_diagnostico');
    }
} 