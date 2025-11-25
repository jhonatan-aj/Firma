<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendVerificationCode extends Mailable
{
    public $name;
    public $code;
    public $purpose;

    public function __construct($name, $code, $purpose)
    {
        $this->name = $name;
        $this->code = $code;
        $this->purpose = $purpose;
    }

    public function build()
    {
        return $this->subject('Código de verificación')
                    ->view('emails.verification-code');
    }
}
