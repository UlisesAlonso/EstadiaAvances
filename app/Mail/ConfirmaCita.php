<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmaCita extends Mailable
{
    use Queueable, SerializesModels;

    public $cita;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($cita, $user)
    {
        $this->cita = $cita;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Confirmación de cita - Cardio Vida')
                    ->view('emails.confirmar-cita');
    }
}
