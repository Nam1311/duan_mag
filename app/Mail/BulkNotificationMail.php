<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BulkNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $subjectText;
    public string $content;

    public function __construct(string $subjectText, string $content)
    {
        $this->subjectText = $subjectText;
        $this->content     = $content;
    }

    public function build()
    {
        return $this->subject($this->subjectText)
            ->view('emails.bulk_notification')
            ->withSymfonyMessage(function ($message) {
                // Nhúng logo và đặt Content-ID là "mag-logo"
                $logoPath = public_path('img/logomail.png');
                if (is_file($logoPath)) {
                    $message->embedFromPath($logoPath, 'mag-logo');
                }
            });
    }
}
