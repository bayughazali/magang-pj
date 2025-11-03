<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $debugCode;

    /**
     * Create a new message instance.
     */
    public function __construct($code)
    {
        $this->debugCode = $code;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Kode Reset Password - ' . config('app.name', 'Laravel'))
                    ->view('emails.reset-password');
    }
}
