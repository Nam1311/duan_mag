<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BulkNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectText;
    public $content;

    public function __construct($subjectText, $content)
    {
        $this->subjectText = $subjectText;
        $this->content = $content;
    }

    public function build()
    {
        return $this->subject($this->subjectText)
                    ->view('emails.bulk_notification');
    }
}
