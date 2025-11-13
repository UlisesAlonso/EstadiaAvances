<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnostico extends Model
{
    use HasFactory;

    protected $table = 'diagnosticos';
    protected $primaryKey = 'id_diagnostico';
    public $timestamps = false;

    protected $fillable = [
        'id_paciente',
        'id_medico',
        'id_PDiag',
        'fecha',
        'descripcion',
    ];

    protected $casts = [
        'fecha' => 'date',
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

    public function tratamientos()
    {
        return $this->hasMany(Tratamiento::class, 'id_diagnostico', 'id_diagnostico');
    }

    public function catalogoDiagnostico()
    {
        return $this->belongsTo(CatalogoDiagnostico::class, 'id_PDiag', 'id_diagnostico');
    }
} 