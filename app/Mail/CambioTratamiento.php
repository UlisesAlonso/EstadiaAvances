<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CambioTratamiento extends Mailable
{
    use Queueable, SerializesModels;

    public $cita;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($tratamiento, $user)
    {
        $this->tratamiento = $tratamiento;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Cambio en tratamiento')
                    ->view('emails.cambioTratamiento');
    }
}
