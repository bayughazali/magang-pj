<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $resetCode;  // ← Protected/Private OK

    public function __construct($code)
    {
        $this->resetCode = $code;
    }

    public function build()
    {
        return $this->subject('Kode Reset Password')
                    ->view('emails.reset-password')
                    ->with('code', $this->resetCode);  // ← Pass manual
    }
}
