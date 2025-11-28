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
    public $expiresInMinutes;
    public $expiresAt;

    public function __construct($name, $code, $purpose)
    {
        $this->name = $name;
        $this->code = $code;
        $this->purpose = $purpose;
        $this->expiresInMinutes = 60;

        // Calcular la hora exacta de expiración
        $expirationTime = now()->addMinutes($this->expiresInMinutes);
        $this->expiresAt = $expirationTime->format('d/m/Y \a \l\a\s g:i A');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Código de verificación',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verification-code',
            with: [
                'name' => $this->name,
                'code' => $this->code,
                'purpose' => $this->purpose,
                'expiresInMinutes' => $this->expiresInMinutes,
                'expiresAt' => $this->expiresAt,
            ],
        );
    }
}
