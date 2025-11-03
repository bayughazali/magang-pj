<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function build()
    {
        return $this->subject('Kode Reset Password - ' . config('app.name', 'Laravel'))
                    ->view('emails.reset-password')
                    ->with([
                        'debugCode' => $this->code
                    ]);
    }
}
