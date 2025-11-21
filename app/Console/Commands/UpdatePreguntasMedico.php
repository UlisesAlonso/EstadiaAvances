<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdatePreguntasMedico extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'preguntas:update-medico';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza las preguntas sin médico asignado';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $medico = DB::table('medicos')->first();
        
        if (!$medico) {
            $this->error('No hay médicos en el sistema.');
            return 1;
        }
        
        $updated = DB::table('preguntas')
            ->whereNull('id_medico')
            ->update(['id_medico' => $medico->id_medico]);
        
        $this->info("Se actualizaron {$updated} preguntas.");
        
        return 0;
    }
}
