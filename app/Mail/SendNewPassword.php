<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class SendNewPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;

    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function build()
    {
        return $this
            ->subject('Your account password has been reset')
            ->view('emails.new_password') // create resources/views/emails/new_password.blade.php
            ->with([
                'name' => $this->user->name,
                'password' => $this->password,
            ]);
    }
}
