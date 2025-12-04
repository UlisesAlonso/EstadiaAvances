<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AnalisisRegistrado extends Mailable
{
    use Queueable, SerializesModels;

    public $analisis;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($analisis, $user)
    {
        $this->analisis = $analisis;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Nuevo análisis registrado - Cardio Vida')
                    ->view('emails.analisis-registrado');
    }
}



