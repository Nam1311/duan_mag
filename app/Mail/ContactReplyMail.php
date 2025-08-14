<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function build()
    {
        return $this->subject('MAG - Phản hồi liên hệ')
            ->view('emails.contact_reply')
            ->with(['name' => $this->name])
            ->withSymfonyMessage(function ($message) {
                // Nhúng ảnh vào email và đặt Content-ID là "mag-logo"
                $message->embedFromPath(public_path('img/logomail.png'), 'mag-logo');
            });
    }
}
